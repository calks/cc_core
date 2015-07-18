<?php

	class coreResourceLibrary {

		protected static $package_list;
		protected static $application_list;
		protected static $resource_type_list;
		protected static $path_cache = array();
		
		public static function getAbsolutePath($relative_path) {
			$site_root = Application::getSitePath();
			if (strpos($relative_path, $site_root) !== false) return $relative_path;
			return $site_root . $relative_path;
		}
		
		
		public static function getTemplatePath($template_name) {
			$templates = self::findEffective(APP_RESOURCE_TYPE_TEMPLATE, null, $template_name.'.tpl');			
			return isset($templates[$template_name]) ? $templates[$template_name]->path : null; 
		}

		public static function getStaticPath($relative_path) {
			$files = self::findEffective(APP_RESOURCE_TYPE_STATIC, null, $relative_path);								
			$file = @array_shift($files);
			return isset($file->path) ? $file->path : null; 
		} 		
				
		public static function getFirstFilePath($resource_type, $resource_name=null, $relative_path=null) {			
			$available_files = self::findEffective($resource_type, $resource_name, $relative_path);
			if (!$available_files) return null;
			$file = array_shift($available_files);
			return $file->path;			
		}
		
		
		protected static function getSubdirectories($dir_path) {
			$dir_path = self::getAbsolutePath($dir_path);
			$dir = @opendir($dir_path);
			if (!$dir) return array();
			$out = array();
			while ($file = readdir($dir)) {
				if (strpos($file, '.') === 0) continue;
				if (!is_dir($dir_path.'/'.$file)) continue;
				$out[] = $file;
			}
			closedir($dir);
			return $out;			
		}
		
		protected static function getPackageList() {
			if (is_null(self::$package_list)) {
				self::$package_list = self::getSubdirectories('/packages');				
			}

			return self::$package_list;
		}
		
		protected static function getApplicationList() {
			if (is_null(self::$application_list)) {
				self::$application_list = self::getSubdirectories('/applications');				
			}

			return self::$application_list;
		}
		
		public static function getResourceTypeList() {
			if (is_null(self::$resource_type_list)) {
				$dirs = array('/core');
				foreach (self::getPackageList() as $pkg_name) {
					$dirs[] = "/packages/$pkg_name";
				}
				foreach (self::getApplicationList() as $app_name) {
					$dirs[] = "/applications/$app_name";
				}
				
				self::$resource_type_list = array();
				foreach ($dirs as $d) {
					foreach (self::getSubdirectories($d) as $resource_dir) {
						$type_name = coreNameUtilsLibrary::getSingularNoun($resource_dir);
						$classname_part = ucfirst(coreNameUtilsLibrary::underscoredToCamel($type_name));
						self::$resource_type_list[$type_name] = $classname_part;					
					}
				}				
			}

			return self::$resource_type_list;
		}
		
		
		public static function findEffective($resource_type, $resource_name=null, $sub_path=null) {
			$all_files = self::findAll($resource_type, $resource_name, $sub_path);
			$out = array();
			foreach ($all_files as $name=>$items) {
				$out[$name] = $items[0];
			}
			return $out;		
		}
		
		
		protected static function addPossiblePaths(&$paths, $base, $resource_name, $sub_path) {			
			if (!$sub_path) $paths[] = $base;
			if ($sub_path && !$resource_name) $paths[] = "$base/$sub_path";
			if ($resource_name) $paths[] = "$base/$resource_name" . ($sub_path ? "/$sub_path" : '');
		}
		
		public static function getEffectiveClass($resource_type, $resource_name) {
			$effective_resources = self::findEffective($resource_type, $resource_name);
			return isset($effective_resources[$resource_name]) ? $effective_resources[$resource_name]->class : null; 
		}
		
		
		
		public static function findAll($resource_type, $resource_name=null, $sub_path=null) {
			
			$cache_key = md5("$resource_type.$resource_name.$sub_path");
			if (isset(self::$path_cache[$cache_key])) return self::$path_cache[$cache_key]; 
						
			$dir = coreNameUtilsLibrary::getPluralNoun($resource_type);
			
			$sub_path = trim($sub_path, ' /');
			
			$out = array();
			
			$resource_routing = Application::getResourceRouting();
			
			$routing_rule = isset($resource_routing[$resource_name]) ? $resource_routing[$resource_name] : $resource_routing['default'];
			$paths = array();
			foreach ($routing_rule as $rule) {
				if ($rule == APP_RESOURCE_CONTAINER_CORE) {
					self::addPossiblePaths($paths, "/core/$dir", $resource_name, $sub_path);
				}
				elseif ($rule == APP_RESOURCE_CONTAINER_PACKAGES) {
					$packages = self::getPackageList();
					foreach ($packages as $package) {
						self::addPossiblePaths($paths, "/packages/$package/$dir", $resource_name, $sub_path);
					}
				}
				elseif (strpos($rule, 'applications/') === 0) {
					$app_name = str_replace('applications/', '', $rule);
					self::addPossiblePaths($paths, "/applications/$app_name/$dir", $resource_name, $sub_path);
				}
				elseif (strpos($rule, 'packages/') === 0) {
					$pkg_name = str_replace('packages/', '', $rule);
					self::addPossiblePaths($paths, "/packages/$pkg_name/$dir", $resource_name, $sub_path);
				}
				else {
					die("Bad resource routing rule");
				}
			}	
			
			foreach ($paths as $path) {
				$absolute_path = Application::getSitePath() . $path;								
				if (is_file($absolute_path)) {					
					$entry = new stdClass();
					$entry->path = $path;
					$entry->class = coreNameUtilsLibrary::classFromRelativePath($path);
					$name = coreNameUtilsLibrary::removeExtension(basename($path));
					$out[$name][] = $entry;
				}
				elseif($resource_name && !$sub_path) {
					if(is_file("$absolute_path/$resource_name.php")) {
						$entry = new stdClass();
						$entry->path = "$absolute_path/$resource_name.php";
						$entry->class = coreNameUtilsLibrary::classFromRelativePath("$path/$resource_name.php");						
						$out[$resource_name][] = $entry;						
					}
				}
				else {					
					$d = @opendir($absolute_path);
					if (!$d) continue;
					while ($filename = readdir($d)) {
						if (in_array($filename, array('..', '.'))) continue;
						
						$absolute_path_individual_dir = null;						
						if (is_dir("$absolute_path/$filename")) {
							if (is_file("$absolute_path/$filename/$filename.php")) {
								$absolute_path_individual_dir = "$absolute_path/$filename/$filename.php";
							}
						};
						
						$name = coreNameUtilsLibrary::removeExtension(basename($filename));
						
						if (is_file("$absolute_path/$name.php") && $absolute_path_individual_dir) {
							throw new Exception("Resource '$name' of type '$resource_type' exists in two places: $path/$name.php and $path/$name/$name.php", 999);
						}

						if (!is_file("$absolute_path/$name.php") && !$absolute_path_individual_dir) {
							continue;
						}
						
						$entry = new stdClass();
						$entry->path = $absolute_path_individual_dir ? "$path/$filename/$filename.php" : "$path/$filename";
						$entry->class = coreNameUtilsLibrary::classFromRelativePath($path . '/' . $filename);						
						$out[$name][] = $entry;
					}
					closedir($d);
				}
			}
			
			self::$path_cache[$cache_key] = $out;
			return $out;			
		}

		
	}
	
	