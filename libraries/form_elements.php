<?php
	
	class coreFormElementsLibrary {
		
		public static function get($element_type, $name, $params=array()) {
			$element_class = self::getElementClass($element_type);	
			$element = new $element_class($name, $params);
			return $element;
		}
		

		protected static function getElementClass($element_type) {
						
			$fields_available = coreResourceLibrary::findEffective('form_field', $element_type);
			
			if (!$fields_available) {
				die("$element_type form field not found");
			}
			$file_path = coreResourceLibrary::getAbsolutePath($fields_available[$element_type]->path);
			//die($file_path);
			require_once $file_path;
			return $fields_available[$element_type]->class;
	
		}
		
		
	}