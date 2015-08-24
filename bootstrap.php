<?php


	define('APP_RESOURCE_TYPE_BLOCK', 'block');
	define('APP_RESOURCE_TYPE_FILTER', 'filter');
	define('APP_RESOURCE_TYPE_LIBRARY', 'library');
	define('APP_RESOURCE_TYPE_MODULE', 'module');
	define('APP_RESOURCE_TYPE_ENTITY', 'entity');	
	define('APP_RESOURCE_TYPE_STATIC', 'static');
	define('APP_RESOURCE_TYPE_TEMPLATE', 'template');


	define('APP_RESOURCE_CONTAINER_PACKAGES', 4);
	define('APP_RESOURCE_CONTAINER_CORE', 5);
	
	
	define('CORE_MISSING_RESOURCE_ERROR', 100);
	define('CORE_DIRECTORY_CREATION_FAILED', 101);		
	
	class coreException extends Exception {
	
	
	}
	
	require 'libraries/application.php';
	
	spl_autoload_register(array('Application', 'loader'));