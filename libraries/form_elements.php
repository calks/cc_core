<?php
	
	class coreFormElementsLibrary {
		
		public static function get($element_type, $name, $params=array()) {
			$element_class = self::getElementClass($element_type);	
			$element = new $element_class($name, $params);
			return $element;
		}
		

		protected static function getElementClass($element_type) {
			
			$addon_name = $element_type . '_field';
			$addons_available = coreResourceLibrary::findEffective(APP_RESOURCE_TYPE_ADDON, 'form_elements', "/$addon_name.php");
			if (!$addons_available) {
				die("No $element_type form element");
			}
			$file_path = coreResourceLibrary::getAbsolutePath($addons_available[$addon_name]->path);
			//die($file_path);
			require_once $file_path;
			return $addons_available[$addon_name]->class;
	
		}
		
		
	}