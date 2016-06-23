<?php

	define('SITE_MENUS_TOP_MENU', 1);
	define('SITE_MENUS_FOOTER_MENU', 2);

	class coreDocumentEntity extends coreBaseEntity {
		const TABLE_NAME = 'document';
		const TABLE_NAME_CONTENT = 'document_content';
		
		var $parent_id;
		var $category;
		var $seq;
		var $is_active;
		var $url;
		var $menu; // bit mask
		var $open_new_window;
		var $open_link;
		var $protected;

		function __construct() {
			$this->title = '';
			$this->content = '';
			$this->meta_title = '';
			$this->meta_desc = '';
			$this->meta_key = '';
			$this->category = 2;
			$this->language_id = CURRENT_LANGUAGE;
		}
		

		function getTableName() {
			return self::TABLE_NAME;
		}

		function get_content_table_name() {
			return self::TABLE_NAME_CONTENT;
		}


		function get_content_subquery($language_id = CURRENT_LANGUAGE) {
			$table = $this->get_content_table_name();
			return "
                (
                    SELECT *, IF(language_id=$language_id, 1, 0) AS language_matched FROM $table
                    ORDER BY language_matched DESC
                )
            ";
		}
		
		
		protected function idRequested($params) {
			if (!isset($params['where'])) return false;
			$id_requested = false;
			$table = $this->getTableName();
			foreach ($params['where'] as $where) {
				$where = strtolower(str_replace(' ', '', $where));				
				if (strpos($where, 'id=') === 0) $id_requested = true;
				if (strpos($where, "$table.id=") === 0) $id_requested = true;
				if (strpos($where, 'idin(') === 0) $id_requested = true;
				if (strpos($where, "$table.idin(") === 0) $id_requested = true;
			}
			
			return $id_requested;
		}
		
		public function getDocumentLink($url) {			
			if (preg_match( "/^http:\/\//i", $url)) {
				return $url;
			}
			else {
				$url = '/' . ltrim($url, ' /');				
				return $url;
			}
		}
		
		public function load_list($params=array()) {
			$params_original = $params;

			// prevent setting parent_id=0 condition
			// if id requested
			if (!isset($params['parent'])) {
				if ($this->idRequested($params)) $params['parent'] = '';
			}			
			
			$parent = isset($params['parent']) ? $params['parent'] : 0;			
			if (!is_array($parent)) $parent = array($parent);
			foreach ($parent as & $p) $p = $p==='' ? '' : (int)$p;
			$parent = implode(',', $parent);
			
			if($parent !== '') $params['where'][] = "parent_id IN($parent)";


			$table = $this->getTableName();
			
			$language_id = isset($params['language_id']) ? $params['language_id'] : CURRENT_LANGUAGE;
			
			$subquery = $this->get_content_subquery($language_id);
			
            $fieldlist_mode = isset($params['fieldlist_mode']) ? $params['fieldlist_mode'] : '';

            if ($fieldlist_mode != 'specified_only') {
            	$params['fields'][] = "content.*";
            }
			
			$params['from'][] = "
				JOIN $subquery AS content
				ON content.document_id = $table.id
			"; 
			
			$params['group_by'][] = 'seq';
			$params['order_by'][] = 'content.document_id';
			
			$list = parent::load_list($params);
			
			foreach ($list as $item) {
				$item->text_category = $this->getDocumentCategories($item->category);				
				$item->children = array();
				$url = $item->open_link ? $item->open_link : $item->url;
				$internal_link = $this->getDocumentLink($url);
				$item->link = Application::getSeoUrl($internal_link);				
				$item->front_link = str_replace('/admin/', '/', $item->link);
				$this->unpackMenuInfo($item);
			}
			
									
			if($parent !== '') {
								
		        $ids = array();
		        foreach ($list as $item) $ids[] = $item->id;
				
				$children_load_params = $params_original;
				$children_load_params['parent'] = $ids;				 
				$children = $this->load_list($children_load_params);

				foreach($list as $item) {
					foreach($children as $child) {
						if ($child->parent_id == $item->id) {
							$item->children[] = $child;
							unset($child); 
						}
					}
				}				
			}

			$this->loadLanguageVersions($list);
			
			return $list;			
			
		}

		
		protected function loadLanguageVersions(&$list) {
			if (!$list) return;
			
			$mapping = array();
			
			$languages = coreRealWordEntitiesLibrary::getLanguages(null, 'id', 'native_name');
			
			foreach ($list as $item) {
				$item->language_versions = array();
				$mapping[$item->id] = $item;
			}
			
			$table = $this->get_content_table_name();
			$db = Application::getDb();
			$ids = implode(',', array_keys($mapping));
			
			
			$data = $db->executeSelectAllObjects("
				SELECT 
					document_id,
					language_id
				FROM 
					$table
				WHERE
					document_id IN($ids)			
			");
					
			foreach ($data as $d) {
				$mapping[$d->document_id]->language_versions[$d->language_id] = $languages[$d->language_id];
			}
			
		}
		
		public function getFieldProperties() {
			
			$out = parent::getFieldProperties();
			
			$out['url'] = array(
				'type' => 'text',
				'caption' => $this->gettext('URL slug'),
				'required' => true,
				'init' => array(
					'addClass' => 'type-page_itself'			
				)
			);
			
			$out['open_link'] = array(
				'type' => 'text',
				'caption' => $this->gettext('Link'),
				'required' => true,
				'init' => array(
					'addClass' => 'type-alias'			
				)
			);
						
			$out['menu'] = array(
				'type' => 'checkbox_collection',
				'caption' => $this->gettext('Display in menu'),
				'init' => array(
					'set_options' => $this->getMenuNames() 
				)
			);
			
			$out['is_active'] = array(
				'type' => 'checkbox',
				'caption' => $this->gettext('Is active')				
			);

			$out['category'] = array(
				'type' => 'radio',
				'caption' => $this->gettext('Type'),
				'init' => array(
					'set_options' => $this->getDocumentCategories() 
				)
			);
			
			$out['parent_id'] = array(
				'type' => 'select',
				'caption' => $this->gettext('Parent'),
				'init' => array(
					'set_options' => $this->get_parent_select_options($this->language_id) 
				)
			);
			
			
			$out['open_new_window'] = array(
				'type' => 'checkbox',
				'caption' => $this->gettext('Open in new window')				
			);
			
			
			$out['title'] = array(
				'type' => 'text',
				'caption' => $this->gettext('Menu and breadcrumbs title'),
				'required' => true
			);
			
			
			$out['meta_title'] = array(
				'type' => 'text',
				'caption' => $this->gettext('&lt;title&gt; (browser tab heading)'),
				'init' => array(
					'addClass' => 'type-page_itself'			
				)				
			);
			
			
			$out['content'] = array(
				'type' => 'rich_editor',
				'caption' => $this->gettext('Content'),
				'init' => array(
					'addClass' => 'type-page_itself'			
				)				
			);
			
			
			$out['meta_desc'] = array(
				'type' => 'textarea',
				'caption' => $this->gettext('META Description'),
				'init' => array(
					'addClass' => 'type-page_itself'			
				)
			);
			
			$out['meta_key'] = array(
				'type' => 'text',
				'caption' => $this->gettext('META Keywords'),
				'init' => array(
					'addClass' => 'type-page_itself'			
				)				
			);
			
			$out['language_id'] = array(
				'type' => 'hidden'				
			);
			
			$out['seq'] = array(
				'type' => 'hidden'				
			);
			
			return $out;
			
		}	
		
		
		function get_parent_select_options($language_id) {
			$out = get_empty_select($this->gettext('-- Top level --'));
			foreach ($this->get_categories($language_id) as $cat) {
				$out[$cat->id] = $cat->title;
			}
			
			return $out;			
		}
		
		function get_categories($language_id = CURRENT_LANGUAGE) {

			$table = $this->getTableName();
			$params['where'][] = "$table.category IN(0,1)";
			$categories = $this->load_list($params);
			return $categories;

		}

		function getDocumentCategories($argument = '') {
			$status = array(
				0 => $this->gettext('folder'), 
				2 => $this->gettext('page')
			);
			if ($argument == '') return $status;
			else return $status[$argument];
		}

		function loadToUrl($url, $language_id = CURRENT_LANGUAGE) {
			
			$table = $this->getTableName();			
			$url = addslashes(trim($url));
			$params['where'][] = "$table.url = '$url'";
						
			$documents = $this->load_list($params);
			if (!$documents) return null;
					
			$document = array_shift($documents);
			
			return $document;
		}

		function getMenuNames() {
			return array(
				SITE_MENUS_TOP_MENU => $this->gettext('top menu'),
				SITE_MENUS_FOOTER_MENU => $this->gettext('footer menu'),
			);
		}

		// transform bitmask to array of integer ids
		function unpackMenuInfo(&$object) {
			$menu = $object->menu;
			$object->menu = array();
			if ($menu & SITE_MENUS_TOP_MENU) $object->menu[] = SITE_MENUS_TOP_MENU;
			if ($menu & SITE_MENUS_FOOTER_MENU) $object->menu[] = SITE_MENUS_FOOTER_MENU;
			
			$object->menu_str = "";
			$menu_names = $this->getMenuNames();

			foreach ($object->menu as $item) {
				if ($object->menu_str) $object->menu_str .= ', ';
				$object->menu_str .= empty($menu_names[$item]) ? $this->getTableName('Unknown') : $menu_names[$item];
			}
		}

		// transform integer ids array to bitmask
		function packMenuInfo(&$object) {
			$menu = 0;
			foreach ($object->menu as $item) $menu = $menu | $item;
			$object->menu = $menu;
		}

		
		function save() {
			$this->packMenuInfo($this);			
			$this->menu = (int)$this->menu;
			$this->open_new_window = (int)$this->open_new_window;
			$this->language_id = isset($this->language_id) ? $this->language_id : CURRENT_LANGUAGE;
			$this->is_active = (int)$this->is_active;
			
			$db = Application::getDb();
			$table = $this->getTableName();
			$table = $this->getTableName();
			$fields = $this->getFields();

			if (is_null($this->seq)) {			
				$this->seq = (int)$db->executeScalar("
					SELECT MAX(seq)+1 FROM $table
				");
			}
			

			$language_id = (int)$this->language_id;

			$id_fieldname = $this->getPrimaryKeyField();

			$insert_fields = "`$id_fieldname`";
			$insert_values = ((int) $this->$id_fieldname != 0) ? (int) $this->id : 'NULL';
			$update = "$id_fieldname=LAST_INSERT_ID($id_fieldname)";

			foreach ($fields as $f) {
				if(strpos($f, 'internal_') === 0) continue;
				if ($f == $id_fieldname) continue;
				$val = $this->$f;
				if (!is_null($val)) $val = addslashes($val);
				$insert_fields .= ", `$f`";
				$insert_values .= is_null($val) ? ", NULL" : ", '$val'";
				$update .= is_null($val) ? ", `$f` = NULL " : ", `$f` = '$val' ";
			}

			$sql = "
                INSERT INTO $table ($insert_fields) VALUES ($insert_values)
                ON DUPLICATE KEY UPDATE $update
            ";

			$db->execute($sql);

			$id = $db->executeScalar("SELECT LAST_INSERT_ID()");
			if ($id) $this->id = $id;

			$content_table = $this->get_content_table_name();

			$db->execute("
                REPLACE INTO $content_table VALUES (
                    '".(int) $this->id."',
                    '".(int) $this->language_id."',
                    '".addslashes($this->title)."',
                    '".addslashes($this->content)."',
                    '".addslashes($this->meta_title)."',                    
                    '".addslashes($this->meta_desc)."',
                    '".addslashes($this->meta_key)."'
                )
            ");
			
			return $id;
		}

		public function delete() {
			$db = Application::getDb();
			$table = $this->getTableName();
			
			$params['parent'] = $this->id;
			
			$children = $this->load_list($params);
			
			foreach($children as $child) $child->delete();

			$content_table = $this->get_content_table_name();
			$db->execute("
				DELETE FROM $content_table
				WHERE document_id=$this->id   
			");

			return parent::delete();
		}
		
		public function deleteLanguageVersion($language_id) {
			
			$deleting_only_version = count($this->language_versions) == 1 && array_key_exists($language_id, $this->language_versions);
			if ($deleting_only_version) {
				return $this->delete();
			}
			
			$db = Application::getDb();
			$content_table = $this->get_content_table_name();
			$language_id = (int)$language_id;
			
			$sql = "
				DELETE FROM $content_table
				WHERE 
					document_id=$this->id AND
					language_id = $language_id
			";
			
			$db->execute($sql);
			
			return (bool)mysql_errno()==0;
						
		}

	}

