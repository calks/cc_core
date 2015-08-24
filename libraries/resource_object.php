<?php

	abstract class coreResourceObjectLibrary {
	
		
		public function getResourceType() {
			$resource_class = get_class($this);
			return coreNameUtilsLibrary::getResourceType($resource_class);
		}

		public function getResourceName() {
			return $this->getName();
		}
		
        public function getName() {
        	return coreNameUtilsLibrary::getResourceName(get_class($this));
        }
		
        
        public function findAllSubresources($subresource_type, $subresource_name=null, $subresource_sub_path=null, $extension='php') {
        	$resource_type = $this->getResourceType();
        	$resource_name = $this->getName();        	
        	
        	$resource_hierarchy = array();
        	$resource_hierarchy[] = array('type' => $resource_type, 'name'=>$resource_name);
        	
            $parents = class_parents($this);            
            foreach ($parents as $p) {
            	$parent_name = coreNameUtilsLibrary::getResourceName($p);
            	$parent_type = coreNameUtilsLibrary::getResourceType($p);
            	if ($parent_name==$resource_name && $parent_type==$resource_type) continue;            	
            	$resource_hierarchy[] = array('type' => $parent_type, 'name'=>$parent_name);
            }        	

        	$sub_path = array();
			$sub_path[] = coreNameUtilsLibrary::getPluralNoun($subresource_type);
			if ($subresource_name) $sub_path[] = $subresource_name; 
			$subresource_sub_path = trim($subresource_sub_path, ' /');
			if ($subresource_sub_path) $sub_path[] = $subresource_sub_path;
			$sub_path = '/' . implode('/', $sub_path);
    
            $out = array();
            foreach ($resource_hierarchy as $resource) {
            	$subresources = coreResourceLibrary::findAll($resource['type'], $resource['name'], $sub_path, $extension);
            	foreach ($subresources as $sr_name => $sr_list) {
            		if (!isset($out[$sr_name])) $out[$sr_name] = array();
            		foreach ($sr_list as $sr) $out[$sr_name][] = $sr;
            	}
            }

            return $out;
        }
        
        
        public function findEffectiveSubresources($subresource_type, $subresource_name=null, $subresource_sub_path=null, $extension='php') {
        	$all_subresources = $this->findAllSubresources($subresource_type, $subresource_name, $subresource_sub_path, $extension);
        	$out = array();
        	
        	foreach ($all_subresources as $sr_name=>$sr_data) {
        		$out[$sr_name] = $sr_data[0]; 
        	}
        	
        	return $out;
        }
        
        
        public function findEffectiveSubresourceClass($subresource_type, $subresource_name, $subresource_sub_path=null) {
        	$subresources = $this->findEffectiveSubresources($subresource_type, $subresource_name, $subresource_sub_path);
        	return isset($subresources[$subresource_name]) ? $subresources[$subresource_name]->class : null;        	
        }
        
        
        public function findEffectiveSubresourcePath($subresource_type, $subresource_name, $subresource_sub_path=null, $extension='php') {
        	$subresources = $this->findEffectiveSubresources($subresource_type, $subresource_name, $subresource_sub_path, $extension);
        	$key_to_check = array_pop(explode('/', $subresource_name));        	
        	return isset($subresources[$key_to_check]) ? $subresources[$key_to_check]->path : null;        	
        }        
        
                
        public function gettext($message) { 
        	$params = func_get_args();
        	array_unshift($params, $this);        	
        	return call_user_func_array(array('coreGettextLibrary', 'gettext'), $params);
        }
        
		
        public static function ngettext($message, $message_plural, $n) {
        	return coreGettextLibrary::ngettext($this, $message, $message_plural, $n);
        }
        
        
	
	}
	
	
	
	
	
	
	
	