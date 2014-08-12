<?php

	if(!function_exists('lcfirst')) {
	    function lcfirst($str) {
	    	if (!$str) return $str;
	        $str[0] = strtolower($str[0]);
	        return (string)$str;
	    }
	}

	class coreNameUtilsLibrary {
		
		public static function underscoredToCamel ($underscored_name) {
			return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $underscored_name))));
		}
		
		public static function camelToUnderscored ($camel_case_name) {
			preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $camel_case_name, $matches);
			$ret = $matches[0];
			foreach ($ret as &$match) {
				$match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
			}
			return implode('_', $ret);						
		}
		
		public function removeExtension($name) {
			return preg_replace('/^(.*)\..*/', '$1', $name);
		}
		
		
		protected static function resourceDirToType() {
			return array(
				'entities' => APP_RESOURCE_TYPE_ENTITY,
				'modules' => APP_RESOURCE_TYPE_MODULE,
				'blocks' => APP_RESOURCE_TYPE_BLOCK,
				'addons' => APP_RESOURCE_TYPE_ADDON,			
				'libraries' => APP_RESOURCE_TYPE_LIBRARY,
			);
		}
		
		public static function classFromRelativePath($relative_path) {
			$path = explode('/', trim($relative_path, ' /'));
			$out = array();
			
			$block = @array_shift($path);
			
			if ($block == 'core') {
				$out[] = 'core';	
			}
			else {
				$block_type = $block=='applications' ?  'app' : 'pkg';
				$block = @array_shift($path);  
				$out[] = $block;
				$out[] = $block_type;
			}
			
			$resource_dir = @array_shift($path);
			$resource_dir_to_type = self::resourceDirToType();
			
			if (!array_key_exists($resource_dir, $resource_dir_to_type)) return '';
			$resource_type = $resource_dir_to_type[$resource_dir];
			
			$name = @array_shift($path);
			$name = self::removeExtension($name);
			$out[] = $name;
			$out[] = $resource_type;
			
			if ($resource_type == APP_RESOURCE_TYPE_ADDON) {
				while ($part = @array_shift($path)) {
					$out[] = self::removeExtension($part);	
				}
			}
			
			return self::underscoredToCamel(implode('_', $out));			
		}
		
		
		protected static function parseResourceClass($class) {
			$out = array(				
				'container_type' => null,
				'container_name' => null,
				'resource_type' => null,
				'resource_name' => null,
				'resource_sub_name' => null			
			);

            $matched = preg_match('/(?P<container_complex_name>(?P<container_name>[a-zA-Z0-9]+)(?P<container_type>App|Pkg)|core)(?P<resource_name>[a-zA-Z0-9]+)(?P<resource_type>Entity|Module|Block|Addon|Library)(?P<sub_name>.*)/', $class, $matches);
            if (!$matched) return $out;
            
            
            $container_complex_name = $matches['container_complex_name'];
            if ($container_complex_name == 'core') {
            	$out['container_type'] = 'core';
            	$out['container_name'] = 'core';
            }
            else {
            	$container_type = $matches['container_type'];
            	$out['container_type'] = $container_type=='Pkg' ? 'package' : 'application';
            	$container_name = $matches['container_name'];
            	$out['container_name'] = self::camelToUnderscored($container_name);
            }
            
            $resource_type = strtolower($matches['resource_type']);
            $out['resource_type'] = $resource_type;            
            
            $resource_name = $matches['resource_name'];
            $out['resource_name'] = self::camelToUnderscored($resource_name);
            
            
            $sub_name = $matches['sub_name'];
            if ($sub_name) {
            	$out['resource_sub_name'] = self::camelToUnderscored($sub_name);
            }
            
            return $out;			
		}
		
		
		public static function getResourceName($resource_class) {
			$class_parsed = self::parseResourceClass($resource_class);
			return $class_parsed['resource_name'];
		}
		
		
		public static function relativePathFromClass($class) {
			
			$class_parsed = self::parseResourceClass($class);
			if (!$class_parsed['container_type']) return null;
			
			$out = array();
			if ($class_parsed['container_type'] == 'core') {
				$out[] = 'core';
			}
			else {
            	$out[] = $class_parsed['container_type']=='package' ? 'packages' : 'applications';            	
            	$out[] = $class_parsed['container_name'];
			}
			
            $resource_dir_to_type = self::resourceDirToType();
            $resource_type_to_dir = array_flip($resource_dir_to_type);
            //$resource_type = strtolower($matches['resource_type']);
            
            if (!array_key_exists($class_parsed['resource_type'], $resource_type_to_dir)) return null;
            $out[] = $resource_type_to_dir[$class_parsed['resource_type']];
			
			$out[] = $class_parsed['resource_name'];
			
            if (in_array($class_parsed['resource_type'], array(APP_RESOURCE_TYPE_MODULE, APP_RESOURCE_TYPE_BLOCK))) {
            	$out[] = $class_parsed['resource_name'];	
            }
			
			
			if ($class_parsed['resource_sub_name']) {
            	$out[] = $class_parsed['resource_sub_name'];
            }
			
			return  '/' . implode('/', $out) . '.php';
        
		}
		
	}