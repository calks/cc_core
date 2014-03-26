<?php

	define('SITE_MENUS_TOP_MENU', 1);
	define('SITE_MENUS_FOOTER_MENU', 2);

	class coreDocumentEntity extends coreBaseEntity {
		const TABLE_NAME = 'document';
		const TABLE_NAME_CONTENT = 'document_content';
		
		var $parent_id;
		var $category;
		var $seq;
		var $active;
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
		}
		

		function get_table_name() {
			return self::TABLE_NAME;
		}

		function get_content_table_name() {
			return self::TABLE_NAME_CONTENT;
		}

		function mandatory_fields() {
			return array(
				'url' => 'URL', 
				'title' => 'Заголовок'			
			);
		}

		function unique_fields() {
			return array("url" => "URL");
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
			$table = $this->get_table_name();
			$alias = $this->get_table_abr();			
			foreach ($params['where'] as $where) {
				$where = strtolower(str_replace(' ', '', $where));				
				if (strpos($where, 'id=') === 0) $id_requested = true;
				if (strpos($where, "$table.id=") === 0) $id_requested = true;
				if (strpos($where, "$alias.id=") === 0) $id_requested = true;				
				if (strpos($where, 'idin(') === 0) $id_requested = true;
				if (strpos($where, "$table.idin(") === 0) $id_requested = true;
				if (strpos($where, "$alias.idin(") === 0) $id_requested = true;
			}
			
			return $id_requested;
		}
		
		public function getDocumentLink($url) {			
			if (preg_match( "/^http:\/\//i", $url)) {
				return $url;
			}
			else {
				$url = '/' . ltrim($url, ' /');				
				return Application::getSeoUrl($url);
			}
		}
		
		function load_list($params=array()) {
			
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
			
			$params['fields'][] = "content.*";
			$params['from'][] = "
				JOIN $subquery AS content
				ON content.document_id = $table.id
			"; 
			
			$params['order_by'][] = 'seq';
			
			$list = parent::load_list($params);
			
			foreach ($list as $item) {
				$item->text_category = $this->getDocumentCategories($item->category);				
				$item->children = array();
				
				$url = $item->open_link ? $item->open_link : $item->url;
				
				//$object->lang_version = document::check_lang_version(2, $object->id);

				$item->link = $this->getDocumentLink($url);

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


			return $list;			
			
		}

		function make_form(&$form, $language_id = CURRENT_LANGUAGE) {
			Application::loadLibrary('fields');
			Application::loadLibrary('olmi/editor');

			$form->addField(new THiddenField("id"));
			$form->addField(new TEditField("url", "", 85, 255));
			$form->addField(new TEditField("open_link", "", 85, 255));
			$form->addField(new THiddenField("id", NULL));
			$form->addField(new THiddenField("seq", NULL));
			$form->addField(new CollectionCheckBoxField("menu", $this->getMenuNames(), $this->menu));

			$form->addField(new TCheckboxField("active", "1", null, 'class="uniform"'));
			$form->addField(new TSelectField('category', '', $this->getDocumentCategories(),  'class="uniform"'));
			
			$form->addField(new TSelectField('parent_id', '', $this->get_parent_select_options($language_id), 'class="uniform"'));
			$form->addField(new TFileField("page_image", "", "", 255));
			$form->addField(new TCheckboxField("is_script", "1", false));
			$form->addField(new TCheckboxField("open_new_window", "1", false,  'class="uniform"'));
			$form->addField(new TEditField("url_image", "", 85, 255));
			$form->addField(new TEditField("color", "", 7, 7));

			$form->addField(new TEditField("title", "", 85, 255));
			$form->addField(new TEditField("meta_title", "", 85, 255));
			$form->addField(new TEditorField("content", "", null, 500));

			$form->addField(new TTextField("meta_desc", "", 85, 4));
			$form->addField(new TTextField("meta_key", "", 85, 4));

			$form->addField(new THiddenField("language_id", $language_id));

			return $form;
		}

		
		function get_parent_select_options($language_id) {
			$out = get_empty_select('--- Верхний уровень ---');
			foreach ($this->get_categories($language_id) as $cat) {
				$out[$cat->id] = $cat->title;
			}
			
			return $out;			
		}
		
		function get_categories($language_id = CURRENT_LANGUAGE) {
			$db = Application::getDb();
			$table = $this->get_table_name();

			$subquery = $this->get_content_subquery($language_id);

			$sql = "
                SELECT id, title
                FROM $table JOIN $subquery AS content
                ON content.document_id = $table.id
                WHERE category IN(0,1)
                GROUP BY document_id
                ORDER BY category, seq
            ";

			$categories = $db->executeSelectAllObjects($sql);

			return $categories;

		}

		function getDocumentCategories($argument = '') {
			$status = array(0 => "раздел", 2 => "страница");
			if ($argument == '') return $status;
			else return $status[$argument];
		}

		function loadToUrl($url, $language_id = CURRENT_LANGUAGE) {
			$db = Application::getDb();
			$table = $this->get_table_name();
			$url = addslashes(trim($url));

			$subquery = $this->get_content_subquery($language_id);

			$query = "
                SELECT *
                FROM $table JOIN $subquery AS content
                ON content.document_id = $table.id
                WHERE url = '$url'
                GROUP BY document_id
            ";
			$object = $db->executeSelectObject($query);
			if (!$object) return NULL;

			$content_table = $this->get_content_table_name();
			$lang_versions = $db->executeSelectColumn("
                SELECT language_id FROM $content_table
                WHERE document_id = $object->id
                AND language_id!=$language_id
                AND content!=''
            ");
			if (!$lang_versions) $lang_versions = array();

			$object->lang_versions = $lang_versions;

			$object->content = stripcslashes($object->content);

			return $object;
		}

		static function check_lang_version($language_id, $document_id) {
			$db = Application::getDb();

			$document = new self();
			$content_table = $document->get_content_table_name();
			$lang_versions = $db->executeSelectColumn("
                SELECT language_id FROM $content_table
                WHERE document_id = $document_id
                AND language_id = $language_id
                AND content != ''
            ");
			if (!$lang_versions) return false;
			else return true;
		}

		function getMenuNames() {
			return array(
				SITE_MENUS_TOP_MENU => 'Верхнее меню',
				SITE_MENUS_FOOTER_MENU => 'Меню в футере',
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
				$object->menu_str .= empty($menu_names[$item]) ? 'Неизвестно' : $menu_names[$item];
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
			$this->active = (int)$this->active;
			
			$db = Application::getDb();
			$table = $this->get_table_name();
			$table = $this->get_table_name();
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

		function delete() {
			$db = Application::getDb();
			$table = $this->get_table_name();
			
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

		/*function normalizeSeq($parent_id) {
			$db = Application::getDb();

			$table = $this->get_table_name();
			$sql = "
                SELECT `id`
                FROM {$table}
                WHERE `parent_id` = '{$parent_id}'
                ORDER BY `seq` ASC
                ";

			$rows = $db->executeSelectAll($sql);
			$i = 0;
			foreach ($rows as $row) {
				$update = "
                    UPDATE {$table}
                    SET `seq` = '{$i}'
                    WHERE `id` = '{$row['id']}'
                    ";

				$db->execute($update);
				$i++;
			}
		}*/
	}

