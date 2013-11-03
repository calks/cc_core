<?php

	class coreLoaderLibrary {
		
		static $fundamental_blocks = null;
		static $resource_directories = array();
		static $class_directories = array();
						
		public function __construct() {
			spl_autoload_register(array($this, 'loader'));
		}
		
		protected function getObjectTypes() {
			return array(
				APP_RESOURCE_TYPE_ENTITY => 'Entity', 
				APP_RESOURCE_TYPE_MODULE => 'Module', 
				APP_RESOURCE_TYPE_BLOCK => 'Block', 
				APP_RESOURCE_TYPE_FILTER => 'Filter',
				APP_RESOURCE_TYPE_LIBRARY => 'Library',
				APP_RESOURCE_TYPE_ADDON => 'Addon'				
			);
		}
		
		
		protected function getFundamentalBlocks() {
			if (is_null(self::$fundamental_blocks)) {
				
				// add core directory
				self::$fundamental_blocks = array(
					'core' => array(),
					'application' => array(),
					'package' => array()				
				);
				
				self::$fundamental_blocks['core'] = array('core' => '/core'); 
				
				// add applications directory
				$applications_directory = Application::getSitePath() . '/applications';
				$dir = opendir($applications_directory);
				while($file = readdir($dir)) {
					if (in_array($file, array('.', '..'))) continue;
					if (!is_dir($applications_directory . '/' . $file)) continue;
					self::$fundamental_blocks['application'][$file] = "/applications/$file";
				}				
				closedir($dir);
				
				// add packages directory
				$packages_directory = Application::getSitePath() . '/packages';				
				if (is_dir($packages_directory)) {					
					$dir = opendir($packages_directory);
					while($file = readdir($dir)) {
						if (in_array($file, array('.', '..'))) continue;
						if (!is_dir($packages_directory . '/' . $file)) continue;
						self::$fundamental_blocks['package'][$file] = "/packages/$file";
					}
					
					closedir($dir);
				}								
			}
			return self::$fundamental_blocks;
		}
		
		
		protected function getObjectDirectories() {
			return array(
				APP_RESOURCE_TYPE_ENTITY => 'entities', 
				APP_RESOURCE_TYPE_MODULE => 'modules', 
				APP_RESOURCE_TYPE_BLOCK => 'blocks', 
				APP_RESOURCE_TYPE_FILTER => 'filters',
				APP_RESOURCE_TYPE_LIBRARY => 'libraries',
				APP_RESOURCE_TYPE_ADDON => 'addons'
			);
		}
		
		
		protected function loader($class) {
			//echo "coreLoader::loader('$class')<br>";
			
			require_once Application::getSitePath() . '/core/libraries/name_utils.php';
			require_once Application::getSitePath() . '/core/libraries/resource.php';
			
			$path = coreNameUtilsLibrary::relativePathFromClass($class);
			$path = coreResourceLibrary::getAbsolutePath($path);

			if (is_file($path)) {
				require_once $path;	
			}					
	
			
			$class_name = $class;
			
			$object_types = $this->getObjectTypes();
			$object_type = null;
			
			foreach($object_types as $type_code=>$type_str) {				
				if (strpos($class_name, $type_str) == strlen($class_name) - strlen($type_str)) {
					$object_type = $type_code;
					break;
				}
			}
			
			if (!$object_type) return;
						
			$directories = $this->getObjectDirectories();
			
			$directory = isset($directories[$object_type]) ? $directories[$object_type] : null;
			if (!$directory) return;

			$class_name = substr($class_name, 0, strlen($class_name) - strlen($type_str));
			
			
						
			$fundamental_blocks = $this->getFundamentalBlocks();
			
			
			$fundamental_block_path = null;
			foreach ($fundamental_blocks as $block_type => $blocks) {
				foreach ($blocks as $block_name=>$block_path) {
					if ($block_type=='core') {
						$underscored_block_name = $block_name; 
					}
					else {
						$underscored_block_name = $block_name . ($block_type=='application' ? '_app' : '_pkg');
					}
										
					$prefix = coreNameUtilsLibrary::underscoredToCamel($underscored_block_name);
										
					$classname_begin = substr($class_name, 0, strlen($prefix));
					if ($classname_begin === $prefix) {
						$fundamental_block_path = $block_path;
						$class_name = substr($class_name, strlen($prefix));
						break;
					}					
				}				
			}			
			
			if ($fundamental_block_path) {
				
				$file = coreNameUtilsLibrary::camelToUnderscored($class_name);
								
				$relative_directory = $fundamental_block_path . '/' . $directory;
				if (in_array($type_code, array('module', 'block', 'addon'))) $relative_directory .= "/$file";
				
				$path = Application::getSitePath() . $relative_directory . '/' . $file . '.php';
				
				///echo $class_name. "<br />";
				
				if (!isset(self::$resource_directories[$type_code])) self::$resource_directories[$type_code] = array();
				if (is_file($path)) self::$resource_directories[$type_code][$file] = $relative_directory;
				self::$class_directories[$class] = $relative_directory;
				if (is_file($path)) {
					require_once $path;	
				}
			}			
		}
		
		public function getDirectoryForClass($class) {
			return isset(self::$class_directories[$class]) ? self::$class_directories[$class] : null;
		}
		
		public function getDirectory($resource_name, $resource_type) {
			return isset(self::$resource_directories[$resource_type][$resource_name]) ? self::$resource_directories[$resource_type][$resource_name] : null; 
		}
		
	}
	
	
	
	