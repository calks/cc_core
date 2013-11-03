<?php
	
	class coreFormElementsLibrary {
		
		public static function get($element_type, $name, $params=array()) {
			$element_class = self::getElementClass($element_type);	
			$element = new $element_class($name, $params);
			return $element;
		}
		

		protected static function getElementClass($element_type) {
			
			$addon_name = $element_type . '_field';
			$addons_available = coreResourceLibrary::getAvailableFiles(APP_RESOURCE_TYPE_ADDON, 'form_elements', "/$addon_name.php");
			if (!$addons_available) {
				die("No $element_type form element");
			}
			$file_path = coreResourceLibrary::getAbsolutePath($addons_available[$addon_name]->path);
			//die($file_path);
			require_once $file_path;
			return $addons_available[$addon_name]->class;
			
			
			$addon_exists = Application::resourceExists('form_elements', APP_RESOURCE_TYPE_ADDON);
			
			if (!$addon_exists) {
				die("form_elements addon not found");
			}
			
			$class_file_path = Application::getFilePathForResource('form_elements', APP_RESOURCE_TYPE_ADDON, "$element_type.php");
						
			if(!$class_file_path) {
				die("No class file for form element $element_type");
			}
			
			require_once($class_file_path);
			
			$addon_relative_path = str_replace(array(Application::getSitePath(), "/$element_type.php"), '', $class_file_path);
			
			$class_directories = coreLoaderLibrary::$class_directories; 
			
			$addon_class = array_search($addon_relative_path, $class_directories);
			
			$element_class = $addon_class . ucfirst(coreNameUtilsLibrary::underscoredToCamel($element_type . '_field'));
			
			if (!class_exists($element_class)) {
				die("No class for form element $element_type");
			}
			
			return $element_class;
		}
		
		
	}