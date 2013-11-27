<?php

	class coreXmlLibrary {
		
		protected static $last_error = '';
		
		public static function getFromString($string) {
			$element = false;
			
			try {
				@$element = new SimpleXMLElement($string);	
			}
			catch (Exception $e) {
				self::$last_error = $e->getMessage();
			}
			
			return $element;
		}
		
		
		public static function getLastError() {
			return self::$last_error;
		}
		
	}
	
	