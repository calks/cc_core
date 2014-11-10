<?php

	class coreResourceLibrary {

		protected static $package_list;
		
		public static function getAbsolutePath($relative_path) {
			$site_root = Application::getSitePath();
			if (strpos($relative_path, $site_root) !== false) return $relative_path;
			return $site_root . $relative_path;
		}
		
		/*public static function getFullUrl($relative_path) {
			$site_url = Application::getSiteUrl();
			if (strpos($relative_path, $site_url) !== false) return $relative_path;
			return $site_url . $relative_path;
		}*/
		
		public static function getTemplatePath($template_name) {
			$templates = self::getAvailableFiles(APP_RESOURCE_TYPE_TEMPLATE, null, $template_name.'.tpl');			
			return isset($templates[$template_name]) ? $templates[$template_name]->path : null; 
		}

		public static function getStaticPath($relative_path) {
			$files = self::getAvailableFiles(APP_RESOURCE_TYPE_STATIC, null, $relative_path);								
			$file = @array_shift($files); 
			return isset($file->path) ? $file->path : null; 
		} 		
				
		public static function getFirstFilePath($resource_type, $resource_name=null, $relative_path=null) {			
			$available_files = self::getAvailableFiles($resource_type, $resource_name, $relative_path);
			if (!$available_files) return null;
			$file = array_shift($available_files);
			return $file->path;			
		}
		
		protected static function getPackageList() {
			if (is_null(self::$package_list)) {
				self::$package_list = array();
				$packages_directory = Application::getSitePath().'/packages';
				if (is_dir($packages_directory)) {
					$dir = opendir($packages_directory);
					while ($file = readdir($dir)) {
						if (in_array($file, array('.', '..'))) continue;
						if (!is_dir($packages_directory.'/'.$file)) continue;
						self::$package_list[] = $file;
					}

					closedir($dir);
				}

			}

			return self::$package_list;
		}
		
		
		public static function getAvailableFiles($resource_type, $resource_name=null, $relative_path=null) {
			
			switch ($resource_type) {
				case APP_RESOURCE_TYPE_ENTITY:
					$dir = '/entities';
					break;
				case APP_RESOURCE_TYPE_FILTER:
					$dir = '/filters';
					break;					
				case APP_RESOURCE_TYPE_MODULE:
					$dir = '/modules';
					break;
				case APP_RESOURCE_TYPE_BLOCK:
					$dir = '/blocks';
					break; 
				case APP_RESOURCE_TYPE_ADDON:
					$dir = '/addons';
					break;
				case APP_RESOURCE_TYPE_STATIC:
					$dir = '/static';
					break; 
				case APP_RESOURCE_TYPE_TEMPLATE:
					$dir = '/templates';
					break;
				default:
					die('coreResourceLibrary::getAvailableFiles invalid resource type');	
			}
			
			$relative_path = trim($relative_path, ' /');
			
			$add_subfolder = !in_array($resource_type, array(APP_RESOURCE_TYPE_ENTITY, APP_RESOURCE_TYPE_FILTER));
			
			if ($resource_name && $add_subfolder) $dir .= '/' . $resource_name;
			if ($relative_path) $dir .= '/' . $relative_path;
			
			$out = array();
			
			$resource_routing = Application::getResourceRouting();
			
			$routing_rule = isset($resource_routing[$resource_name]) ? $resource_routing[$resource_name] : $resource_routing['default'];
			$paths = array();
			
			foreach ($routing_rule as $rule) {
				if ($rule == APP_RESOURCE_CONTAINER_CORE) {
					$paths[] = '/core' . $dir;
				}
				elseif ($rule == APP_RESOURCE_CONTAINER_FRONT_APPLICATION) {
					$paths[] = '/applications/front' . $dir;
				}
				elseif ($rule == APP_RESOURCE_CONTAINER_ADMIN_APPLICATION) {
					$paths[] = '/applications/admin' . $dir;
				}
				elseif ($rule == APP_RESOURCE_CONTAINER_PACKAGES) {
					$packages = self::getPackageList();
					foreach ($packages as $package) {
						$paths[] = '/packages/' . $package . $dir;
					}
				}
				elseif (strpos($rule, 'applications/') === 0) {
					$app_name = str_replace('applications/', '', $rule);
					$paths[] = '/applications/' . $app_name . $dir;
				}
				elseif (strpos($rule, 'packages/') === 0) {
					$pkg_name = str_replace('packages/', '', $rule);
					$paths[] = '/packages/' . $pkg_name . $dir;
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
					
					if (array_key_exists($name, $out)) continue;
					$out[$name] = $entry;
				}
				else {
					$d = @opendir($absolute_path);
					if (!$d) continue;
					while ($filename = readdir($d)) {
						if (in_array($filename, array('..', '.'))) continue;
						if (is_dir($absolute_path . '/' . $filename)) continue;
						$name = coreNameUtilsLibrary::removeExtension(basename($filename));
						if (array_key_exists($name, $out)) continue;
						$entry = new stdClass();
						$entry->path = $path . '/' . $filename;
						$entry->class = coreNameUtilsLibrary::classFromRelativePath($path . '/' . $filename);
						
						$out[$name] = $entry;
					}
				}
			}
			
			
			return $out;			
		}

		
	}
	
	