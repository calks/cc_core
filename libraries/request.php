<?php

	class coreRequestLibrary {
	
		public static function isPostMethod() {
			return strcasecmp($_SERVER["REQUEST_METHOD"], "POST") == 0;
		}
	
		public static function isHTTP11() {
			return strcasecmp($_SERVER["SERVER_PROTOCOL"], "HTTP/1.1") == 0;
		}
	
		public static function get($name, $default = NULL) {
			if (array_key_exists($name, $_REQUEST)) {
				$value = $_REQUEST[$name];
				if (!is_array($value)) {
					$is_url_encoded = preg_match('/%[a-h0-9]{2}/is', $value);
					if ($is_url_encoded) $value = rawurldecode($value);
				}
				return $value;
			}
			else {
				return $default;
			}
		}
	
		public static function isFieldValueSet($field_name, $request) {
			$uid = md5(uniqid());
			return self::getFieldValue($field_name, $request, $uid) != $uid;
		}
	
		public static function getFieldValue($field_name, $request, $default = null) {
			$field_name_parts = explode('[', $field_name);
			$ptr = &$request;
			
			while($field_name_parts) {
				$key = array_shift($field_name_parts);
				$key = trim($key, ']');
				
				if (!isset($ptr[$key])) {
					return $default;
				}
				else {
					$ptr = &$ptr[$key];
				}
			}
			
			return $ptr;
		}
	
		public static function getMethod() {
			return $_SERVER["REQUEST_METHOD"];
		}
	
		public static function getScriptPathOnly() {
			$path = $_SERVER["PHP_SELF"];
			$slashPos = strrpos($path, '/');
			if ($slashPos === FALSE) {
				return $path;
			}
			else {
				return substr($path, 0, $slashPos + 1);
			}
		}
	
		public static function getHostName() {
			return $_SERVER['HTTP_HOST'];
		}
	
	}
