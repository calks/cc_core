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
		
		
		
		public static function classFromRelativePath($relative_path) {
			
			$extension = pathinfo($relative_path, PATHINFO_EXTENSION);
			if (!$extension || $extension != 'php') return null;
			
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
			$resource_type = coreNameUtilsLibrary::getSingularNoun($resource_dir);
			
			$resource_name = @array_shift($path);
			$resource_name = pathinfo($resource_name, PATHINFO_FILENAME);
			$out[] = $resource_name;
			$out[] = $resource_type;
			
			$subresource_type = @array_shift($path);
			$subresource_name = @array_shift($path);
			$subresource_name = pathinfo($subresource_name, PATHINFO_FILENAME);
			
			if ($subresource_type && $subresource_name) {
				$out[] = $subresource_name;
				$out[] = coreNameUtilsLibrary::getSingularNoun($subresource_type);
			}
			
			
			return self::underscoredToCamel(implode('_', $out));			
		}
		
		
		protected static function parseResourceClass($class) {
			$out = array(				
				'container_type' => null,
				'container_name' => null,
				'resource_type' => null,
				'resource_name' => null,
				'subresource_type' => null,
				'subresource_name' => null						
			);

			$resource_types = coreResourceLibrary::getResourceTypeList();			
			$resource_type_regexp = implode('|', $resource_types);
			
			$regexp = '/^(?P<container_complex_name>(?P<container_name>[a-zA-Z0-9]+)(?P<container_type>App|Pkg)|core)(?P<resource_name>[a-zA-Z0-9]+)(?P<resource_type>'.$resource_type_regexp.')(?P<sub_name>.*)$/isU';
									
            $matched = preg_match($regexp, $class, $matches);
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
            
            $resource_type = $matches['resource_type'];
            $out['resource_type'] = self::camelToUnderscored($resource_type);
            
            $resource_name = $matches['resource_name'];
            $out['resource_name'] = self::camelToUnderscored($resource_name);
            
            
            $sub_name = $matches['sub_name'];
            if ($sub_name) {
            	$subresource_dir = coreNameUtilsLibrary::getPluralNoun($out['resource_type']) . '/' . $out['resource_name'];
            	$subresource_types = coreResourceLibrary::getResourceTypeList($subresource_dir);
            	$subresource_type_regexp = implode('|', $subresource_types);
            	preg_match('/^(?P<subresource_name>[a-zA-Z0-9]+)(?P<subresource_type>'.$subresource_type_regexp.')$/is', $sub_name, $sub_name_matches);
            	$subresource_type = isset($sub_name_matches['subresource_type']) ? $sub_name_matches['subresource_type'] : null; 
            	$subresource_name = isset($sub_name_matches['subresource_name']) ? $sub_name_matches['subresource_name'] : null;
 
            	if ($subresource_type && $subresource_name) {
            		$out['subresource_type'] = self::camelToUnderscored($subresource_type);
            		$out['subresource_name'] = self::camelToUnderscored($subresource_name);
            	}
            }
            
            return $out;			
		}
		
		
		public static function getResourceName($resource_class) {
			$class_parsed = self::parseResourceClass($resource_class);
			return $class_parsed['resource_name'];
		}
		
		public static function getResourceType($resource_class) {
			$class_parsed = self::parseResourceClass($resource_class);
			return $class_parsed['resource_type'];
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
			
			$out[] = coreNameUtilsLibrary::getPluralNoun($class_parsed['resource_type']);
			
			$out[] = $class_parsed['resource_name'];
			
			if ($class_parsed['subresource_name']) {
				$out[] = coreNameUtilsLibrary::getPluralNoun($class_parsed['subresource_type']);
				$out[] = $class_parsed['subresource_name'];
				return '/' . implode('/', $out) . '.php';
			}
			else {
				$path_in_common_dir = '/' . implode('/', $out) . '.php';
				$out[] = $class_parsed['resource_name'];
				$path_in_individual_dir = '/' . implode('/', $out) . '.php';
				
				return is_file(Application::getSitePath() . $path_in_common_dir) ? $path_in_common_dir : $path_in_individual_dir;					
			}
        
		}
		
		
		protected static function getEndingReplacements() {
			return array(
				'a' => 'as',
				'b' => 'bs',
				'c' => 'cs',
				'd' => 'ds',				
				'e' => 'es',
				'f' => 'fs',
				'g' => 'gs',
				'h' => 'hs',
				'i' => 'is',
				'j' => 'js',
				'k' => 'ks',
				'l' => 'ls',
				'm' => 'ms',
				'n' => 'ns',
				'o' => 'os',
				'p' => 'ps',
				'q' => 'qs',
				'r' => 'rs',
				's' => 'ses',
				't' => 'ts',
				'u' => 'us',
				'v' => 'vs',
				'w' => 'ws',
				'x' => 'xes',
				'y' => 'ies',
				'z' => 'zes',
				'static' => 'static'				
			);			
		}
		
		
		protected static function replaceWordEnding($word, $replacements) {
			$word_length = strlen($word);
			
			$replace_what = null;
			$replace_with = null;
			foreach ($replacements as $old_ending=>$new_ending) {
				$old_ending_length = strlen($old_ending);
				$ending_matched = strrpos($word, $old_ending) === $word_length-$old_ending_length;
				if (!$ending_matched) continue;
				if (is_null($replace_what) || strlen($replace_what) < $old_ending_length) {
					$replace_what = $old_ending;
					$replace_with = $new_ending;
				}				
			} 
			if (is_null($replace_what)) return $word;
			return substr($word, 0, $word_length-strlen($replace_what)) . $replace_with;
		}
		
		public static function getSingularNoun($plural_noun) {
			$replacements = array_flip(self::getEndingReplacements());
			return self::replaceWordEnding($plural_noun, $replacements);		
		}
		
		public static function getPluralNoun($singular_noun) {
			$replacements = self::getEndingReplacements();
			return self::replaceWordEnding($singular_noun, $replacements);
		}
		
		
	}