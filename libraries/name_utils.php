<?php

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
		
		
		public static function relativePathFromClass($class) {
			
            $matched = preg_match('/(?P<container_complex_name>(?P<container_name>[a-zA-Z0-9]+)(?P<container_type>App|Pkg)|core)(?P<resource_name>[a-zA-Z0-9]+)(?P<resource_type>Entity|Module|Block|Addon|Library)(?P<sub_name>.*)/', $class, $matches);
            if (!$matched) return null;
            
            
            $container_complex_name = $matches['container_complex_name'];
            if ($container_complex_name == 'core') {
            	$out[] = 'core';
            }
            else {
            	$container_type = $matches['container_type'];
            	$out[] = $container_type=='Pkg' ? 'packages' : 'applications';
            	$container_name = $matches['container_name'];
            	$out[] = self::camelToUnderscored($container_name);
            }
            
            $resource_dir_to_type = self::resourceDirToType();
            $resource_type_to_dir = array_flip($resource_dir_to_type);
            $resource_type = strtolower($matches['resource_type']);
            
            if (!array_key_exists($resource_type, $resource_type_to_dir)) return null;
            $out[] = $resource_type_to_dir[$resource_type];
            
            $resource_name = $matches['resource_name'];
            $out[] = self::camelToUnderscored($resource_name);
            
            if (in_array($resource_type, array(APP_RESOURCE_TYPE_MODULE, APP_RESOURCE_TYPE_BLOCK))) {
            	$out[] = self::camelToUnderscored($resource_name);	
            }
            
            $sub_name = $matches['sub_name'];
            if ($sub_name) {
            	$out[] = self::camelToUnderscored($sub_name);
            }
            
            return '/' . implode('/', $out) . '.php';
            
		}
		
	}