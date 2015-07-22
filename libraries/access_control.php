<?php

	class coreAccessControlLibrary {
		
		/*
		 * access is allowed then at least one rule returns true and no rules return false
		 * 
		 * */
		public static function accessAllowed($user, $resource, $action=null) {
			$access_rule_resources = coreResourceLibrary::findEffective('access_rule');
			if (!$access_rule_resources) return false;
			
			$allowed = null;
			foreach ($access_rule_resources as $arr) {
				$rule_class = $arr->class;
				if($rule_class == 'coreBaseAccessRule') continue;
				
				$rule = new $rule_class();
				$allowed_by_rule = $rule->accessAllowed($user, $resource, $action);
				if ($allowed_by_rule && is_null($allowed)) {
					$allowed = true;
				}
				elseif ($allowed_by_rule === false) {
					return false;
				}
			}
					
			return (bool)$allowed;
		}
		
		
	}