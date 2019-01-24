<?php

	class coreBaseEntity extends coreResourceObjectLibrary {
	     
		public $id;
        
		const RELATION_ONE_TO_ONE = 1;
		const RELATION_ONE_TO_MANY = 2;
		const RELATION_MANY_TO_ONE = 3;
		const RELATION_MANY_TO_MANY = 4;
		
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
            	$joins = array();
            	foreach ($params['from'] as $join) {            		
            		$join_normalized = strtolower(preg_replace('/([`\s]+)/is', '', $join));            		
            		$join_hash = md5($join_normalized);
            		if (!in_array($join_hash, $joins)) {
            			$from[] = $join;
            			$joins[] = $join_hash;
            		}            		
            	}                
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
        
        
        protected function loadRelatedEntities(&$list, $related_entity_name, $relation_type, $list_field=null) {
        	if (!$list) return;
        	
        	if (!$list_field) {
        		$list_field = coreNameUtilsLibrary::getPluralNoun($related_entity_name);
        	}

        	switch ($relation_type) {
        	
        		case self::RELATION_ONE_TO_ONE:
        		case self::RELATION_ONE_TO_MANY:
        			$related_entity = Application::getEntityInstance($related_entity_name);
        			$related_entity_table = $related_entity->getTableName();
        			
        			
        			$is_related_by_entity_name_and_id = $related_entity->hasField('entity_name') && $related_entity->hasField('entity_id');
        			if ($is_related_by_entity_name_and_id) {
        				$entity_name = $this->getResourceName();        				
        				$load_params['where'][] = "`$related_entity_table`.`entity_name` = '$entity_name'";
        				$foreign_key = 'entity_id';
        			}
        			else {
        				$foreign_key = $this->getResourceName() . '_id';        				
        			}
        			
        			$mapping = array();
        			foreach ($list as $item) {
        				if ($relation_type == self::RELATION_ONE_TO_MANY) {
        					$item->$list_field = array();
        				}
        				else {
        					$item->$related_entity_name = null;
        				}
        				
        				$mapping[$item->id] = $item;
        			}
        			
        			$ids = array_keys($mapping);
        			$ids = implode(',', $ids);
        			
        			$load_params['where'][] = "`$related_entity_table`.`$foreign_key` IN ($ids)"; 
        			
        			$related_entity_list = $related_entity->load_list($load_params);
        			
        			foreach ($related_entity_list as $re) {
        				if ($relation_type == self::RELATION_ONE_TO_MANY) {
        					$re_list = &$mapping[$re->$foreign_key]->$list_field;
        					$re_list[] = $re;        					 
        				}
        				else {
        					$mapping[$re->$foreign_key]->$related_entity_name = $re; 
        				}
        			}
        			
        			break;
        		case self::RELATION_MANY_TO_MANY:
        			$related_entity = Application::getEntityInstance($related_entity_name);
        			$related_entity_table = $related_entity->getTableName();
        			$relation_table = $this->getTableName() . '_' . $related_entity_table;
        			 
        			$foreign_key_1 = $this->getResourceName() . '_id';
        			$foreign_key_2 = $related_entity_name . '_id';
        			
        			$mapping = array();
        			foreach ($list as $item) {
        				$item->$list_field = array();
        				$mapping[$item->id] = $item;        				
        			}
        			
        			
        			$item_ids = array_keys($mapping);
        			$item_ids = implode(',', $item_ids);
        			
        			$db = Application::getDb();
        			$relations = $db->executeSelectAllObjects("
        				SELECT
        					$foreign_key_1,
        					$foreign_key_2
        				FROM 
        					$relation_table
        				WHERE
        					$foreign_key_1 IN($item_ids)	
        			");
        			
        			$relations_mapping = array();
        			foreach ($relations as $r) {
        				$relations_mapping[$r->$foreign_key_2][] = $r->$foreign_key_1;
        			}
        			
        			$related_ids = array_keys($relations_mapping);
        			
        			
        			if ($related_ids) {
        				$related_ids = implode(',', $related_ids);
        				$related_load_params['where'][] = "$related_entity_table.id IN($related_ids)"; 
        				$related = $related_entity->load_list($related_load_params);
        				foreach ($related as $r) {
        					foreach($relations_mapping[$r->id] as $foreign_key_1_value) {
        						array_push($mapping[$foreign_key_1_value]->$list_field, $r);
        					}
        				}
        			}
        			 
        			break;        			
        		case self::RELATION_MANY_TO_ONE:
        			$related_entity = Application::getEntityInstance($related_entity_name);
        			$related_entity_table = $related_entity->getTableName();
        			$related_entity_pk = $related_entity->getPrimaryKeyField();
        			$table = $this->getTableName();
        			
        			// Пока только для связи по одному полю        			
        			$foreign_key = $related_entity_name . '_id';
        			
        			$mapping = array();
        			foreach ($list as $item) {
       					$item->$related_entity_name = null;
       					if ($item->$foreign_key) {
       						$mapping[$item->$foreign_key][] = $item;	
       					}
        			}
        			
        			$ids = array();
        			foreach ($mapping as $k=>$v) {        				
        				if ($k) $ids[] = $k; 	
        			}
        			
        			if ($ids) {
	        			$ids = implode(',', $ids);
        			
	        			$load_params['where'][] = "`$related_entity_table`.`$related_entity_pk` IN ($ids)"; 
	        			
	        			$related_entity_list = $related_entity->load_list($load_params);
	        			
	        			foreach ($related_entity_list as $re) {
	        				foreach ($mapping[$re->$related_entity_pk] as $item) {
	        					$item->$related_entity_name = $re;	
	        				}
	        			}
        			}
        			
        			break;
        			
        			
        			
        		default:
        			throw new coreException('unknown relation type');
        	
        	}
        	
        }
        
        
        protected function findUnrelatedEntityIds(&$related, $related_entity_name, $relation_type) {
        	if (!$this->id) return array();
        	        	
        	switch ($relation_type) {
        		case self::RELATION_ONE_TO_ONE:
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
        			

        			if ($related) {
        				
        				$related_ids = array();
        				
        				if ($relation_type == self::RELATION_ONE_TO_ONE) {
        					$related_ids = array($related->id);
        				}
        				else {
        					foreach ($related as $item) {
        						$related_ids[] = $item->id;
        					}
        				}

        				$related_ids = implode(',', $related_ids);
        				
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
        
        protected function deleteUnrelatedEntities($related, $related_entity_name, $relation_type) {        	
        	$unrelated_ids = $this->findUnrelatedEntityIds($related, $related_entity_name, $relation_type);
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
        
        
        protected function saveRelatedEntities(&$related, $related_entity_name, $relation_type) {
        	
        	if (!$this->id) return;
        	
        	switch ($relation_type) {
        	
        		case self::RELATION_ONE_TO_ONE:
        		case self::RELATION_ONE_TO_MANY:
        			
        			if ($relation_type == self::RELATION_ONE_TO_ONE) {
        				if (is_array($related)) {
        					throw new coreException("saveRelatedEntities: Single related entity expected, array given");
        				}
        				$related = array($related);
        			}
        			else {
        				if (!is_array($related)) {
        					throw new coreException("saveRelatedEntities: Array of related entities expected, single entity given");
        				}
        			}
        			
        			$related_entity = Application::getEntityInstance($related_entity_name);
        			$related_entity_table = $related_entity->getTableName();        			
        			
        			$is_related_by_entity_name_and_id = $related_entity->hasField('entity_name') && $related_entity->hasField('entity_id');
        			if ($is_related_by_entity_name_and_id) {
        				$entity_name = $this->getResourceName();
        				$entity_id = $this->id;
        				foreach ($related as $item) {
        					$item->entity_name = $entity_name;
        					$item->entity_id = $entity_id;
        				}
        				$delete_condition[] = "`entity_name`='$entity_name'";
        				$delete_condition[] = "`entity_id`=$entity_id";
        			}
        			else {
        				$foreign_key = $this->getName() . '_id';
        				 
        				foreach ($related as $item) {
        					$item->$foreign_key = $this->id;
        				}
        				
        				$delete_condition[] = "`$foreign_key`=$this->id";
        			}
        			
        			$delete_condition = implode(' AND ', $delete_condition);
        			
        			$db = Application::getDb();
        			$db->execute("
        				DELETE FROM $related_entity_table
        				WHERE $delete_condition
        			");
        			
        			$related_entity->save_list($related);
        			
        			if ($relation_type == self::RELATION_ONE_TO_ONE) {
        				$related = array_shift($related);
        			}
        			 
        			break;
        			
        		case self::RELATION_MANY_TO_MANY:        			
        			$related_entity = Application::getEntityInstance($related_entity_name);
        			$related_entity_table = $related_entity->getTableName();
        			$relation_table = $this->getTableName() . '_' . $related_entity_table;
        			
        			$foreign_key_1 = $this->getResourceName() . '_id';
        			$foreign_key_2 = $related_entity_name . '_id';
        			
        			$values = array();
        			foreach ($related as $r) {        				
        				$values[] = "($this->id, $r->id)";        				
        			}
        			
        			$db = Application::getDb();
        			$db->execute("
        				DELETE FROM $relation_table 
        				WHERE $foreign_key_1=$this->id
        			");
        			
        			if ($values) {
        				$values = implode(',', $values);
        				$db->execute("
        					INSERT INTO $relation_table ($foreign_key_1, $foreign_key_2) VALUES $values
        				");
        			}
        			        			
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

			if (@$params['show_sql']) echo '<pre>' . $sql . '</pre>'."\n";
			
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
            

            foreach ($fields as $field) {            	
                $sql_fields[] = "`$field`";                
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
            $sql_values = implode(",\n", $sql_values);
            $sql_update = $this->get_save_sql_update($list);
            
            
            $sql = "
            	INSERT INTO `$object_table` ($sql_fields) VALUES $sql_values
            	ON DUPLICATE KEY UPDATE $sql_update
            ";
                        
            return $sql;        	
        }
        
        
        protected function get_save_sql_update($list) {
        	$sql_update = array();
        	$pkey = $this->getPrimaryKeyField();
        	$fields = $this->get_save_fields();
			foreach ($fields as $field) {
                $sql_update[] = $field==$pkey ? "$pkey=LAST_INSERT_ID($pkey)" : "`$field`=VALUES(`$field`)";
            }
            
            $sql_update = implode(',', $sql_update);
            return $sql_update;
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