<?php

	class coreBaseEntity extends coreResourceObjectLibrary {
	     
		public $id;
        
		const RELATION_ONE_TO_MANY = 1;
		const RELATION_MANY_TO_ONE = 2;
		const RELATION_MANY_TO_MANY = 3;
		
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
			$form->addField(coreFormElementsLibrary::get('hidden', $this->getPrimaryKeyField()));
			return $form;
		}
		
		
		public function getFieldProperties() {
			$out = array();
			
			$pk_field = $this->getPrimaryKeyField();
			
			if ($pk_field) {
				$out[$pk_field] = array(
					'type' => 'hidden'					
				);
			}
			
			return $out;
			
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
					$errors[] = $this->gettext("You should fill in &laquo;%s&raquo;", $err_out);
				}
			}
			//if (sizeof($errors) == 0) {
				$unique = $this->unique_fields();
				$unique_keys = array_keys($unique);

				$extrasql = "";
				$pkey = $this->getPrimaryKeyField();
				if ($this->$pkey) $extrasql = " and $pkey <> ".$this->$pkey;
				foreach ($this as $key => $value) {
					if (in_array($key, $unique_keys)) {
						$query = "select count(*) from ".$table." where ".$key."='".addslashes($value)."' ".$extrasql;						
						if ($db->executeScalar($query) > 0) {
							if (isset($unique[$key]) && $unique[$key] != "") $err_out = $unique[$key];
							else $err_out = $key;

							$errors[] = $this->gettext("Value for &laquo;%s&raquo; field is already used", $err_out);
						}

					}
				}
			//}
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
        	$db->execute("DELETE FROM `$table` WHERE $pkey=$pkey_value");
        	return (bool)mysql_errno()==0;
        }
        

        public function __construct() {

        }


        public function order_by() {
            $table = $this->getTableName();
            if ($this->hasField('seq')) {
            	return "`$table`.`seq`";
            }
            else {
            	$pkey = $this->getPrimaryKeyField();
            	return "`$table`.`$pkey`";
            }            
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
            
            $fields = array_unique($fields);

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

            $from = array_unique($from);
            
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

            $new_where = array_unique($new_where);
            
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
            
            if (@$params['show_sql']) echo '<pre>' . $sql . '</pre>'."\n";


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
        
        
        protected function loadRelatedEntities(&$list, $related_entity_name, $relation_type) {
        	if (!$list) return;

        	switch ($relation_type) {
        	
        		case self::RELATION_ONE_TO_MANY:
        			$related_entity = Application::getEntityInstance($related_entity_name);
        			$related_entity_table = $related_entity->getTableName();
        			$list_field = coreNameUtilsLibrary::getPluralNoun($related_entity_name);

        			
        			$is_related_by_entity_name_and_id = $related_entity->hasField('entity_name') && $related_entity->hasField('entity_id');
        			if ($is_related_by_entity_name_and_id) {
        				$entity_name = $this->getResourceName();        				
        				$load_params['where'][] = "`$related_entity_table`.`entity_name` = '$entity_name'";
        				$foreign_key = 'entity_id';
        			}
        			else {
        				$foreign_key = $this->getName() . '_id';        				
        			}
        			
        			$mapping = array();
        			foreach ($list as $item) {
        				$item->$list_field = array();
        				$mapping[$item->id] = $item;
        			}
        			
        			$ids = array_keys($mapping);
        			$ids = implode(',', $ids);
        			
        			$load_params['where'][] = "`$related_entity_table`.`$foreign_key` IN ($ids)"; 
        			
        			$related_entity_list = $related_entity->load_list($load_params);
        			
        			foreach ($related_entity_list as $re) {
        				$re_list = &$mapping[$re->$foreign_key]->$list_field;
        				$re_list[] = $re; 
        			}
        			
        			break;
        		default:
        			throw new coreException('unknown relation type');
        	
        	}
        	
        }
        
        
        protected function findUnrelatedEntityIds(&$related_list, $related_entity_name, $relation_type) {
        	if (!$this->id) return array();
        	        	
        	switch ($relation_type) {        	
        		case self::RELATION_ONE_TO_MANY:
        			$related_entity = Application::getEntityInstance($related_entity_name);
        			$related_entity_table = $related_entity->getTableName();
        			
        			
        			$is_related_by_entity_name_and_id = $related_entity->hasField('entity_name') && $related_entity->hasField('entity_id');
        			if ($is_related_by_entity_name_and_id) {
        				$entity_name = $this->getResourceName();
        				$entity_id = $this->id;        				
        				$where[] = "`$related_entity_table`.`entity_name` = '$entity_name'";
        				$where[] = "`$related_entity_table`.`entity_id` = $entity_id";
        				$foreign_key = 'entity_id';
        			}
        			else {
        				$foreign_key = $this->getName() . '_id';
        				$where[] = "`$related_entity_table`.`$foreign_key` = $this->id";
        			}
        			

        			if ($related_list) {
        				
        				$related_ids = array();
        				foreach ($related_list as $item) {
        					$related_ids[] = $item->id;
        				}

        				$related_ids = implode(',', $related_ids);
        				print_r($related_ids);
        				$where[] = "`$related_entity_table`.`id` NOT IN($related_ids)";
        			}

        			$where = implode(' AND ', $where);
        			$db = Application::getDb();
        			
        			return $db->executeSelectColumn("
        				SELECT 
        					id
        				FROM 
        					$related_entity_table
        				WHERE
        					$where      			
        			"); 
        			
					break;
        		default:
        			throw new coreException('unknown relation type');
        			
        	}
        	
        }
        
        protected function deleteUnrelatedEntities($related_list, $related_entity_name, $relation_type) {        	
        	$unrelated_ids = $this->findUnrelatedEntityIds($related_list, $related_entity_name, $relation_type);
        	if ($unrelated_ids) {
        		$related_entity = Application::getEntityInstance($related_entity_name);
        		$related_entity_table = $related_entity->getTableName();
        		$unrelated_ids = implode(',', $unrelated_ids);
        		$load_params['where'][] = "`$related_entity_table`.id IN($unrelated_ids)"; 
        		$unrelated_list = $related_entity->load_list($load_params);
        		foreach ($unrelated_list as $item) {
        			$item->delete();
        		}
        	}
        }        
        
        
        protected function saveRelatedEntities(&$related_list, $related_entity_name, $relation_type) {
        	
        	if (!$this->id) return;
        	
        	switch ($relation_type) {
        	
        		case self::RELATION_ONE_TO_MANY:
        			$related_entity = Application::getEntityInstance($related_entity_name);
        			$related_entity_table = $related_entity->getTableName();        			
        			
        			$is_related_by_entity_name_and_id = $related_entity->hasField('entity_name') && $related_entity->hasField('entity_id');
        			if ($is_related_by_entity_name_and_id) {
        				$entity_name = $this->getResourceName();
        				$entity_id = $this->id;
        				foreach ($related_list as $item) {
        					$item->entity_name = $entity_name;
        					$item->entity_id = $entity_id;
        				}
        			}
        			else {
        				$foreign_key = $this->getName() . '_id';
        				 
        				foreach ($related_list as $item) {
        					$item->$foreign_key = $this->id;
        				}
        			}
        			
        			$related_entity->save_list($related_list);        			
        			break;
        		default:
        			throw new coreException('unknown relation type');
        			
        	}
        
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

                //echo "<pre>$sql</pre>";
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

        
        protected function get_save_sql($list) {        	
            $object_table = $this->getTableName();
            $fields = $this->get_save_fields();
            $pkey = $this->getPrimaryKeyField();

            $sql_fields = array();            
            $sql_update = array();

            foreach ($fields as $field) {            	
                $sql_fields[] = "`$field`";
                $sql_update[] = $field==$pkey ? "$pkey=LAST_INSERT_ID($pkey)" : "`$field`=VALUES(`$field`)";
            }
            
            $sql_values = array();
            foreach ($list as $item) {
            	            	
            	$value_row = array();
	            if (!$item->$pkey) {
	                $item->$pkey = null;
	            }            	
            	foreach ($fields as $field) {
	            	$value = $item->$field;                
    	            $value = is_null($value) ? "NULL" : "'".addslashes($value)."'";
    	            $value_row[] = $value;
            	}
            	
            	$value_row = implode(',', $value_row);
				$sql_values[] = "($value_row)";
            }
            

            $sql_fields = implode(',', $sql_fields);
            $sql_values = implode(',', $sql_values);
            $sql_update = implode(',', $sql_update);
            
            
            $sql = "
            	INSERT INTO `$object_table` ($sql_fields) VALUES $sql_values
            	ON DUPLICATE KEY UPDATE $sql_update
            ";
                        
            return $sql;        	
        }
        
        public function save() {
        	$pkey = $this->getPrimaryKeyField();
        	$sql = $this->get_save_sql(array($this));

            $db = Application::getDb();
            $db->execute($sql);

            $this->$pkey = $db->getLastAutoIncrementValue();
            
            return $this->$pkey;
        }
        
        public function save_list(&$list) {
        	if (!$list) return true;

        	$pkey = $this->getPrimaryKeyField();
        	
        	$new_items_indexes = array();
        	foreach ($list as $idx=>$item) {        		
				if (!$item->$pkey) $new_items_indexes[] = $idx;
        	}
        	
        	$sql = $this->get_save_sql($list);
        	
            $db = Application::getDb();
            $db->execute($sql);
            
            $succeed = mysql_errno() == 0;
            if (!$succeed) return false;

            if (!$new_items_indexes) return true;
            
            $reload_params = array();
            $new_items_count = count($new_items_indexes);
            $first_inserted_item_pkey = $db->getLastAutoIncrementValue();
            $object_table = $this->getTableName();
            $reload_params['where'][] = "`$object_table`.`$pkey` >= $first_inserted_item_pkey";
            $reload_params['limit'] = $new_items_count;
            //$reload_params['show_sql'] = 1;
            
            $just_inserted = $this->load_list($reload_params);
            if (count($just_inserted) != $new_items_count) {
            	die('save_list(): reload failed');
            }

            foreach ($just_inserted as $idx=>$item) {
            	$list[$new_items_indexes[$idx]] = $item;
            }
            
            return true;
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