<?php

	
	require_once realpath(dirname(__FILE__)."/../..") . "/core/bootstrap.php";
	
	class Application {
		
		protected static $instances = array();
		protected static $default_application_name;

		public static function setDefaultApplicationName($application_name) {
			self::$default_application_name = $application_name;
		}
		
		
		public static function getInstance($application_name) {
			
			if (!isset(self::$instances[$application_name])) {
				self::loadLibrary('name_utils');
	
				$application_class = coreNameUtilsLibrary::underscoredToCamel($application_name . '_app_application_class');
				$site_root = realpath(dirname(__FILE__)."/../..");
				$application_path = "$site_root/applications/$application_name/classes/application.php";
				
				
				if (!is_file($application_path)) {
					throw new coreException("Application class $application_class difinition file ($application_path) not found", CORE_MISSING_RESOURCE_ERROR);
				}
				
				require_once "$site_root/core/classes/application.php";
				require_once $application_path;
				
				if (!class_exists($application_class)) {
					throw new coreException("Application class $application_class not found", CORE_MISSING_RESOURCE_ERROR);
				}
				
				self::$instances[$application_name] = new $application_class;
			}

			return self::$instances[$application_name];
		}
				
		public static function __callStatic($method, $args) {
			$app = self::getInstance(self::$default_application_name);
			return call_user_func_array(array($app, $method), $args);
		}
		
		
		public static function loader($class_name) {
			
			self::loadLibrary('name_utils');
			self::loadLibrary('resource');
			$path = coreNameUtilsLibrary::relativePathFromClass($class_name);
			$path = coreResourceLibrary::getAbsolutePath($path);

			if (is_file($path)) {
				require_once $path;	
			}			
		} 
		
		
		public static function getSitePath() {
			return realpath(dirname(__FILE__)."/../..");
		}
		
		
		
		public static function loadLibrary($library_name) {			
			$library_name = explode('/', $library_name);
			$site_root = realpath(dirname(__FILE__)."/../..");
			$path = $site_root.'/core/libraries';
			foreach ($library_name as $fragment) $path .= '/'.$fragment;
			$path .= '.php';
			
			if (is_file($path)) include_once($path);
			else return false;
			return true;
		}
		
			

	}
	
	
	
