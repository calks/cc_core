<?php

	class coreBaseEntity {
	     
		public $id;
        
        
		function hasField($fieldName) {
			return array_key_exists($fieldName, get_object_vars($this));
		}

		function getField($fieldName) {
			return $this->$fieldName;
		}

		function setField($fieldName, $value) {
			$this->$fieldName = $value;
		}


		function isPrimaryKey($fieldName) {
			return strcasecmp($fieldName, $this->getPrimaryKeyField()) == 0;
		}

		function setAutoincrementField($value) {
			$this->setField($this->getPrimaryKeyField(), $value);
		}


		function mandatory_fields() {
			return array();
		}

		function unique_fields() {
			return array();
		}

		function trim_fields() {
			return array();
		}

		function getPrimaryKeyField() {
			return "id";
		}

		function make_form(&$form) {
			$form->addField(new THiddenField($this->getPrimaryKeyField()));
			return $form;
		}

		function validate() {
			$db = Application::getDb();

			$table = $this->getTableName();
			$errors = array();

			$trim = $this->trim_fields();
			foreach ($trim as $key) {
				$this->$key = trim($this->$key);
			}

			$mandatory = $this->mandatory_fields();
			$mandatory_keys = array_keys($mandatory);
			foreach ($this as $key => $value) {
				if (in_array($key, $mandatory_keys) && $value == '') {
					if (isset($mandatory[$key]) && $mandatory[$key] != "") $err_out = $mandatory[$key];
					else $err_out = $key;
					$errors[] = "Не заполнено поле &laquo;$err_out&raquo;";
				}
			}
			if (sizeof($errors) == 0) {
				$unique = $this->unique_fields();
				$unique_keys = array_keys($unique);

				$extrasql = "";
				$pkey = $this->getPrimaryKeyField();
				if ($this->$pkey) $extrasql = " and $pkey <> ".$this->$pkey;
				foreach ($this as $key => $value) {
					if (in_array($key, $unique_keys)) {
						$query = "select count(*) from ".$table." where ".$key."='".addslashes($key)."' ".$extrasql;
						if ($db->executeScalar($query) > 0) {
							if (isset($unique[$key]) && $unique[$key] != "") $err_out = $unique[$key];
							else $err_out = $key;

							$errors[] = "Выбранное вами для поля ".$err_out." значение уже используется.";
						}

					}
				}
			}
			return $errors;
		}

		
		function getId() {
			$pkey = $this->getPrimaryKeyField();
			return $this->$pkey;
		}
		
		function getFields() {
			$result = get_class_vars(get_class($this));
			return array_keys($result);
		}
        
        
        public function getTableName() {
        	return $this->getName();
        }
        
        
        public function delete() {
        	if (class_exists('filePkgHelperLibrary')) {
        		filePkgHelperLibrary::deleteFiles($this);	
        	}
        	if (class_exists('imagePkgHelperLibrary')) {
        		imagePkgHelperLibrary::deleteFiles($this, 'image');	
        	}
        	        	
        	$db = Application::getDb();
        	$pkey = $this->getPrimaryKeyField();
        	$pkey_value = (int)$this->$pkey;
        	if (!$pkey_value) return true;
        	$table = $this->getTableName();
        	$db->execute("DELETE FROM $table WHERE $pkey=$pkey_value");
        	return (bool)mysql_errno()==0;
        }
        

        public function __construct() {

        }


        public function order_by() {
            $table = $this->getTableName();
            $pkey = $this->getPrimaryKeyField();
            return "`$table`.`$pkey`";
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
                if ($t == '*') {
                    continue;
                }
                $t = "`$t`";
            }

            return implode('.', $term);
        }

        protected function get_list_fields($params = array()) {
        	$pkey = $this->getPrimaryKeyField();
        	if ($pkey == 'id') {
        		return array_keys(get_class_vars(get_class($this)));	
        	}
        	else {
        		$out = array();
        		foreach (array_keys(get_class_vars(get_class($this))) as $f) {
        			if ($f != 'id') $out[] = $f;
        		}
        		return $out;
        	}
        	
        }

        protected function load_list_get_fields($params = array()) {
            $fieldlist_mode = isset($params['fieldlist_mode']) ? $params['fieldlist_mode'] : '';

            $table = $this->getTableName();

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
            $pkey = $this->getPrimaryKeyField();
            $throw_out_id = $pkey != 'id'; 
            foreach ($fields as & $f) {
            	if ($throw_out_id && $f == 'id') continue;
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
            $table = $this->getTableName();
            
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

            $table = $this->getTableName();
            $pkey = $this->getPrimaryKeyField();
            $params['where'][] = "`$table`.$pkey=$id";
            
            $list = $this->load_list($params);

            return $list ? $list[0] : null;
        }

        public function save() {
            $object_table = $this->getTableName();
            $fields = $this->get_save_fields();
            $pkey = $this->getPrimaryKeyField();
            if (!$this->$pkey) {
                $this->$pkey = null;
            }

            $insert_fields = array();
            $insert_values = array();
            $update = array();

            foreach ($fields as $field) {            	
                $value = $this->$field;
                $value = is_null($value) ? "NULL" : "'".addslashes($value)."'";
                $insert_fields[] = "`$field`";
                $insert_values[] = $value;
                $update[] = ($field == $pkey) ? "$pkey=LAST_INSERT_ID($pkey)" : "`$field`=$value";
            }

            $insert_fields = implode(', ', $insert_fields);
            $insert_values = implode(', ', $insert_values);
            $update = implode(', ', $update);

            $sql = "INSERT INTO `$object_table` ($insert_fields) VALUES($insert_values)";
            if ($this->$pkey) $sql .= " ON DUPLICATE KEY UPDATE $update";

            $db = Application::getDb();
            $db->execute($sql);

            $this->$pkey = $db->getLastAutoIncrementValue();
            return $this->$pkey;
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