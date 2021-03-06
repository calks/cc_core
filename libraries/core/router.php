<?php

	Application::loadLibrary('seo/rewrite');

	class Router {

		private static $source_url;
		private static $rewritten_url;
		private static $module_name;
		private static $module_params;
		private static $request_params;
		private static $defaultModuleName = 'page404';

		public static function route($url) {
			self::$source_url = $url;
			self::$rewritten_url = coreUrlRewriterLibrary::seoToInternal($url);
			self::$module_params = array();
			self::$request_params = array();
			self::$module_name = self::$defaultModuleName;

			$url = new URL(self::$rewritten_url);
			$address = $url->getAddress();
			
			if (!$url_parts = explode('/', $address)) {
				return;
			}
			
			$possible_module_name = array_shift($url_parts);			

			$module_class = coreResourceLibrary::getEffectiveClass('module', $possible_module_name);
			if ($module_class) {
				self::$module_name = $possible_module_name;
			}
			else {
				array_unshift($url_parts, $possible_module_name);
			}

			self::$module_params = $url_parts;
			foreach ($url->getGetParams() as $name=>$value) {
				self::$module_params[$name] = $value;
			}

		}

		public static function getModuleName() {
			return self::$module_name;
		}

		public static function getModuleParams() {
			return self::$module_params;
		}

		public static function getRequestParams() {
			return self::$request_params;
		}

		public static function getSourceUrl() {
			return self::$source_url;
		}

		public static function getRewrittenUrl() {
			return self::$rewritten_url;
		}

		public static function getRequestParam($name, $default = null) {
			return isset(self::$request_params[$name]) ? self::$request_params[$name] : $default;
		}

		public static function setDefaultModuleName($moduleName) {
			self::$defaultModuleName = $moduleName;
		}

	}
