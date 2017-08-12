<?php


	class coreStructuresLibrary {
		
		
		public static function getListAsLeveledList($list, $key_field='id', $parent_key_field='parent_id') {
			$tree = self::getListAsTree($list, $key_field, $parent_key_field);			
			return self::getTreeAsLeveledList($tree, $key_field);
		}
		
		public static function getTreeAsLeveledList($tree, $key_field='id', $children_field='children') {
			$out = array();
				
			if (!$tree) return $out;
		
			$out[] = $tree;
		
			$level = 1;
			do {
				$untraversed_nodes_count = 0;
				$new_out = array();
				foreach ($out as $k=>$v) {
					if (is_array($v)) {
						foreach ($v as $item) {
							$item->level = $level;
							$new_out[$item->$key_field] = $item;
							if ($item->$children_field) {
								$new_out['_' . $item->$key_field] = $item->$children_field;
								$untraversed_nodes_count++;
							}
							unset($item->$children_field);
						}
					}
					else {
						$new_out[$k] = $v;
					}
				}
					
				$out = $new_out;
				$level++;
					
			} while ($untraversed_nodes_count > 0);
				
			return $out;
		}
		
		
		public static function getListAsTree($list, $key_field='id', $parent_key_field='parent_id', $children_field='children') {
				
			$tree = array();
				
			$parent_to_child_mapping = array(
				null => &$tree
			);
				
			do {
				$added = 0;
				foreach ($list as $index=>$item) {					
					if (array_key_exists($item->$parent_key_field, $parent_to_child_mapping)) {
						$parent_to_child_mapping[$item->$parent_key_field][] = $item;						
						$parent_to_child_mapping[$item->$key_field] = &$item->$children_field;
						unset($list[$index]);
						$added++;
					}
				}
		
			} while ($added > 0);
				
			return $tree;
		}
		
	}