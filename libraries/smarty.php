<?php

	require_once 'smarty/Smarty.Class.php';
	
	class coreSmartyLibrary extends Smarty {
		
		function fetch($template_path, $cache_id = null, $compile_id = null, $display = false) {
			$template_path = coreResourceLibrary::getAbsolutePath($template_path);
			return parent::fetch($template_path, $cache_id, $compile_id, $display);
		}
		
		
	}