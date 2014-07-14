<?php

	class coreSettingsLibrary {
	
		protected static $tree;		
		protected static $tree_flat;
		protected static $group_names;
		protected static $tree_nvp;
		
		protected function getTableName() {
			return 'settings';
		}
		
		public static function get($full_param_name, $rebuild=false) {
			if (is_null(self::$tree_nvp)) self::loadTreeNvp();
			if (isset(self::$tree_nvp[$full_param_name])) {
				return self::$tree_nvp[$full_param_name];
			}
			elseif (!$rebuild && self::rebuildTree()) {
				return self::get($full_param_name, true);
			}
			else {
				return null;
			}
		}
		
		public static function getUpToDateTree() {
			self::rebuildTree();
			return self::$tree;
		}
		
		public static function getGroupNames() {
			return self::$group_names;
		}
		
		
		public static function updateTreeFromPost() {
			self::rebuildTree();
			foreach (self::$tree_flat as $item) $item->setValueFromPost();
		}
		
		public static function getErrors() {		
			$errors = array();	
			foreach (self::$tree_flat as $item){
				$item_error = $item->getError();
				if ($item_error) {
					$errors[] = "$item->group_displayed_name/$item->param_displayed_name: $item_error";
				}
			}
			return $errors;
		}
		
		
		protected static function loadTreeNvp() {
			$db = Application::getDb();
			$table = self::getTableName();
			self::$tree_nvp = array();
			$data = $db->executeSelectAllObjects("
				SELECT 
					group_name,
					param_name,
					param_value
				FROM `$table`
			");
			foreach ($data as $d) {
				self::$tree_nvp["$d->group_name/$d->param_name"] = unserialize($d->param_value);
			}
		}
		
		protected static function loadTree() {
			self::$group_names = array();
			self::$tree = array();
			self::$tree_flat = array();
			
			$db = Application::getDb();
			$table = self::getTableName();
			self::$tree_nvp = array();
			$data = $db->executeSelectAllObjects("
				SELECT *
				FROM `$table`
				ORDER BY group_name, seq
			");
			foreach ($data as $d) {
				self::$group_names[$d->group_name] = $d->group_displayed_name;
				$d->param_value = unserialize($d->param_value);
				$d->constraints = unserialize($d->constraints);
				$item = self::getParamAddon($d->group_name, $d->param_name, $d->param_type);
				$item->keep_value = true;
				foreach ($d as $k=>$v) $item->$k = $v;
			}
		}
		
		public static function saveTree() {
			$values = array();			
			$fields = array_keys(get_class_vars('coreSettingsAddonBaseParam'));

			
			foreach (self::$tree_flat as $item) {				
				$item->group_displayed_name = isset(self::$group_names[$item->group_name]) ? self::$group_names[$item->group_name] : $item->group_name;
				$item->is_mandatory = (int)$item->is_mandatory;
				
				$row = array();
				foreach ($fields as $f) {
					$value = $item->$f;
					if (in_array($f, array('constraints', 'param_value'))) $value = serialize($value);
					$value = "'" . addslashes($value) . "'";
					$row[] = $value;
				}
				
				$row = '(' . implode(',', $row) . ')';
				$values[] = $row;								
			}
			
			$fields_escaped = array();
			foreach ($fields as $f) $fields_escaped[] = "`$f`";
			
			$fields_str = implode(',', $fields_escaped);
			$values_str = implode(',', $values);
			$table = self::getTableName();
			$sql = "
				INSERT INTO `$table` ($fields_str) VALUES $values_str
			";
			
			
			$db = Application::getDb();
			$db->execute("TRUNCATE `$table`");
			$db->execute($sql);
			return mysql_errno()==0;
			
		}
		
		protected static function getParamAddon($group_name, $param_name, $type) {
			
			if (!isset(self::$tree[$group_name])) self::$tree[$group_name] = array();
			
			// Если параметр уже был когда-то внесен в БД,
			// возвращаем его (чтобы сохранить назначенное значение)
			if (isset(self::$tree[$group_name][$param_name])) {				
				if (self::$tree[$group_name][$param_name]->param_type == $type) {
					self::$tree[$group_name][$param_name]->keep_value = true;
					return self::$tree[$group_name][$param_name];
				} 
			}
			
			// если нет, создаем новый и вставляем в дерево
			$addon_name = $type . '_param';
			$param_addons = coreResourceLibrary::getAvailableFiles('addon', 'settings', "$addon_name.php");
			if (!isset($param_addons[$addon_name])) {
				throw new Exception("settings/$addon_name addon not found", 999);
				return null;
			}
			$out = new $param_addons[$addon_name]->class();
			$out->keep_value = false;
			self::$tree[$group_name][$param_name] = $out;
			self::$tree_flat["$group_name/$param_name"] = $out;
			return $out; 
		}
		
		
		protected static function rebuildTree() {
			self::loadTree();
			$addons = coreResourceLibrary::getAvailableFiles('addon', 'settings');
			
			$max_seq_by_group = array();
			foreach($addons as $addon_name=>$addon_data) {
				$is_param_set = substr($addon_name, strlen($addon_name)-10) == '_param_set';
				if (!$is_param_set) continue;
				$param_set_addon = new $addon_data->class();
				
				foreach ($param_set_addon->getGroupNames() as $name=>$displayed_name) {
					self::$group_names[$name] = $displayed_name;
				}
				
				$subtree = $param_set_addon->getParamsTree();
				
				foreach ($subtree as $group_name => $params) {					
					foreach ($params as $param_name => $param_data) {
						$param_type = $param_data['type'];
						$item = self::getParamAddon($group_name, $param_name, $param_type);
						$item->param_name = $param_name;
						$item->param_displayed_name = isset($param_data['displayed_name']) ? $param_data['displayed_name'] : $param_name;
						$item->param_displayed_unit = isset($param_data['displayed_unit']) ? $param_data['displayed_unit'] : '';   
						$item->group_name = $group_name;
						$item->param_type = $param_type;
						if (!$item->keep_value) {
							$item->param_value = isset($param_data['value']) ? $param_data['value'] : null;
						}
						$item->is_mandatory = isset($param_data['mandatory']) ? (bool)$param_data['mandatory'] : false;
						$item->constraints = isset($param_data['constraints']) ? $param_data['constraints'] : array();
						if (!isset($max_seq_by_group[$item->group_name])) $max_seq_by_group[$item->group_name] = 0;
						$item->seq = $max_seq_by_group[$item->group_name];
						$max_seq_by_group[$item->group_name]++;
						
					}
				}
				
				//print_r(self::$tree_flat);
			}
			
			self::saveTree();			
			self::loadTree();
		}
 
		
	}