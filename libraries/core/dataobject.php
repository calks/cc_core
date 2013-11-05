<?php

    Application::loadLibrary('olmi/class');

    abstract class DataObject extends CMSObject {
        const TABLE_NAME = '';

        public $id;
        protected static $internal_table_abr = array();

        public function getTableName() {
        	return $this->get_table_name();
        	
        }
        
        
        protected function delete_path($path) {
        	if (is_file($path)) {
        		if (!@unlink($path)) die("DataObject::delete_path(): Can't delete file $path");
        	}
        	elseif(is_dir($path)) {
        		$dir = opendir($path);
        		if (!$dir) die("DataObject::delete_path(): Can't read directory $path");
        		while ($file = readdir($dir)) {
        			if (in_array($file, array('.', '..'))) continue;
        			$this->delete_path($path . '/' . $file);
        		}
        		closedir($dir);
        		if (!rmdir($path)) die("DataObject::delete_path(): Can't delete directory $path");
        	}       	
        }
        
        
        public function delete($_id=null) {
        	if (class_exists('filePkgHelperLibrary')) {
        		filePkgHelperLibrary::deleteFiles($this);	
        	}
        	if (class_exists('imagePkgHelperLibrary')) {
        		imagePkgHelperLibrary::deleteFiles($this, 'image');	
        	}
        	        	
        	return parent::delete($_id);
        }
        
        public static function getTableAlias($table) {
            if (!$table) {
                return '';
            }

            if (isset(self::$internal_table_abr[$table])) {
                return self::$internal_table_abr[$table];
            }

            $table_parts = explode('_', $table);
            foreach ($table_parts as & $part) {
                $part = substr($part, 0, 1);
            }
            $base_abr = $abr = implode('_', $table_parts);

            $counter = 1;
            while (false !== array_search($abr, self::$internal_table_abr)) {
                $counter++;
                $abr = $base_abr.'_'.$counter;
            }

            self::$internal_table_abr[$table] = $abr;

            return $abr;
        }

        public function __construct() {
        }

        public function get_table_abr($table = '') {
            if (!$table) {
                $table = $this->get_table_name();
            }

            return self::getTableAlias($table);
        }

        public function get_table_name() {
            return get_class($this);
        }

        public function order_by() {
            $table = self::getTableName();
            return "{$table}.`id`";
        }

        protected function wrap_term($term) {
            if (!$term = trim($term)) {
                return '';
            }

            $special_symbols = array(' ', '(', ')');
            foreach ($special_symbols as $symbol) {
                if (false !== strpos($term, $symbol)) {
                    return $term;
                }
            }

            $term = explode('.', $term);

            foreach ($term as & $t) {
                if ($t == '*' || in_array($t, self::$internal_table_abr)) {
                    continue;
                }
                $t = "`$t`";
            }

            return implode('.', $term);
        }

        protected function get_list_fields($params = array()) {
            return array_keys(get_class_vars(get_class($this)));
        }

        protected function load_list_get_fields($params = array()) {
            $fieldlist_mode = isset($params['fieldlist_mode']) ? $params['fieldlist_mode'] : '';

            $table = $this->getTableName();
            $table_abr = $this->get_table_abr($this->get_table_name());
            if ($fieldlist_mode != 'specified_only') {
                $fields = $this->get_list_fields();
                foreach ($fields as & $field) {
                    $field = "$table.$field";
                }
            }
            else {
                $fields = array();
            }


            if (isset($params['fields'])) {
                $fields = array_merge($fields, $params['fields']);
            }

            $out = array();
            foreach ($fields as $f) {
                $f = strtolower($f);
                if (false !== strpos($f, 'internal_') || 0 === strpos($f, "{$table}._") ) {
                    continue;
                }
                $out[] = $this->wrap_term($f);
            }

            return implode(",\n", $out);
        }

        protected function get_save_fields($params = array()) {
            $fields = array_keys(get_class_vars(get_class($this)));

            $out = array();
            foreach ($fields as & $f) {
                // field prefixed with "internal_" are not to be stored in DB
                if (false !== strpos($f, 'internal_') || 0 === strpos($f, '_')) {
                    continue;
                }
                $out[] = $f;
            }

            return $out;
        }

        protected function load_list_get_sql_part($params, $name, $keyword, $glue, $default = '') {
            if ($default) {
                $default = " $keyword $default ";
            }
            if (empty($params[$name])) {
                return $default;
            }

            if (!is_array($params[$name])) {
                return " $keyword {$params[$name]} ";
            } else {
                $str = implode($glue, $params[$name]);
                return " $keyword $str ";
            }
        }

        protected function load_list_get_limit($params) {
            $limit = @(int) ($params['limit']);
            $offset = @(int) ($params['offset']);
            $limit_offset = '';
            if ($limit) {
                $limit_offset .= " LIMIT $limit ";
                if ($offset) {
                    $limit_offset .= " OFFSET $offset ";
                }
            }
            return $limit_offset;
        }

        protected function load_list_get_from($params) {
            $table = $this->get_table_name();
            $abr = $this->get_table_abr($table);
            
            $table = explode('.', $table);
            foreach($table as &$t) $t = "`$t`";
            $table = implode('.', $table); 

            $from = array("$table" );

            if (isset($params['from'])) {
                $from = array_merge($from, $params['from']);
            }

            $from = implode("\n", $from);
            return $from;
        }

        protected function load_list_get_where($params) {
            if (empty($params['where'])) {
                return '';
            }

            $where = $params['where'];
            if (!is_array($where)) {
                return " WHERE $where ";
            }

            $new_where = array();

            foreach ($where as $k => $v) {
                if (is_int($k)) {
                    $new_where[] = $v;
                } else {
                    $k = $this->wrap_term($k);
                    if (is_null($v)) {
                        $new_where[] = "$k IS NULL";
                    } elseif (is_array($v)) {
                        if (!empty($v)) {
                            foreach ($v as & $val) {
                                $val = "'".addslashes($val)."'";
                            }
                            $v = implode(', ', $v);
                            $new_where[] = "$k IN ($v)";
                        }
                    } else {
                        $v = addslashes($v);
                        $new_where[] = "$k='".addslashes($v)."'";
                    }
                }
            }

            return $new_where ? 'WHERE '.implode(' AND ', $new_where) : '';
        }

        public function load_list($params = array()) {
            $db = Application::getDb();

            $mode = isset($params['mode']) ? $params['mode'] : '';

            $fields = $this->load_list_get_fields($params);
            $from = $this->load_list_get_from($params);
            $where = $this->load_list_get_where($params);

            $limit_offset = $this->load_list_get_limit($params);
            $order_by = $this->load_list_get_sql_part($params, 'order_by', ' ORDER BY ', ', ', $this->order_by());
            $group_by = $this->load_list_get_sql_part($params, 'group_by', ' GROUP BY ', ', ');
            $having = $this->load_list_get_sql_part($params, 'having', ' HAVING ', ' AND ');

            $sql = "
            SELECT $fields
            FROM $from
            $where $group_by $having $order_by $limit_offset";

            //echo '<pre>' . $sql . '</pre>'."\n\n";

            $raw_list = $db->executeSelectAllObjects($sql);
            if (!$raw_list) {
                return array();
            }

            $fields = array_keys($fields = get_object_vars($raw_list[0]));
            $list = array();
            $class = get_class($this);

            foreach ($raw_list as $object) {
                $obj = new $class();
                foreach ($fields as $f) {
                    $obj->$f = $object->$f;
                }
                $list[] = $obj;
            }

            return $list;
        }

        public function count_list($params = array()) {
            //var_dump( $params ); die();
            $db = Application::getDb();

            $fields = $this->load_list_get_fields($params);
            $from = $this->load_list_get_from($params);
            $where = $this->load_list_get_where($params);
            $group_by = $this->load_list_get_sql_part($params, 'group_by', ' GROUP BY ', ', ');
            $having = $this->load_list_get_sql_part($params, 'having', ' HAVING ', ' AND ');

            $sql = "
            SELECT COUNT(*) FROM (
                SELECT $fields
                FROM $from
                $where $group_by $having
            ) AS dataset";

            return $db->executeScalar($sql);
        }

        public function load($id, $params = array()) {
            if (!$id = (int) ($id)) {
                return null;
            }

            $table = $this->get_table_name();
            $abr = $this->get_table_abr($table);

            $params['where'][] = "$table.id=$id";
            $params['group_by'] = "$table.id";
            $list = $this->load_list($params);

            return $list ? $list[0] : null;
        }

        public function save() {
            $object_table = $this->get_table_name();
            $fields = $this->get_save_fields();
            if (!$this->id ) {
                $this->id = null;
            }

            $insert_fields = array();
            $insert_values = array();
            $update = array();

            foreach ($fields as $field) {
                $value = $this->$field;
                $value = is_null($value) ? "NULL" : "'".addslashes($value)."'";
                $insert_fields[] = "`$field`";
                $insert_values[] = $value;
                $update[] = ($field == 'id') ? 'id=LAST_INSERT_ID(id)' : "`$field`=$value";
            }

            $insert_fields = implode(', ', $insert_fields);
            $insert_values = implode(', ', $insert_values);
            $update = implode(', ', $update);

            $sql = "INSERT INTO `$object_table` ($insert_fields) VALUES($insert_values)";
            if ($this->id ) $sql .= " ON DUPLICATE KEY UPDATE $update";

            $db = Application::getDb();
            $db->execute($sql);

            $this->id = $db->getLastAutoIncrementValue();
            return $this->id;
        }

        public function getName() {
            $class = get_class($this);
            
            if (preg_match('/(?P<container_complex_name>(?P<container_name>[a-zA-Z0-9]+)(?P<container_type>App|Pkg)|core)(?P<entity_name>[a-zA-Z0-9]+)Entity/', $class, $matches)) {            	
            	return coreNameUtilsLibrary::camelToUnderscored($matches['entity_name']);
            }
            
            return strtolower(str_replace(Application::getApplicationName().'_', '', get_class($this)));             
        }

        public function get_values($fields) {
            $out = array();
            foreach ($fields as $f) {
                $out[$f] = isset($this->$f) ? $this->$f : null;
            }
            return $out;
        }

        public function set_values($saved_values) {
            foreach ($saved_values as $field => $value) {
                $this->$field = $value;
            }
        }

    }
