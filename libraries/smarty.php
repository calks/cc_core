<?php

	require_once 'smarty/Smarty.Class.php';
	
	class coreSmartyLibrary extends Smarty {
		
	    function _smarty_include($params) {
	    	
	    	if (!isset($params['smarty_include_tpl_file'])) return parent::_smarty_include($params);
	    	$file = $params['smarty_include_tpl_file'];
	    	if (is_file($file)) return parent::_smarty_include($params);
	    	
	    	$file_abs = Application::getSitePath() . $file;
	    	if (!is_file($file_abs)) return parent::_smarty_include($params);
	    	
	    	$params['smarty_include_tpl_file'] = $file_abs;
	    	return parent::_smarty_include($params);	       
	    }
		
		function fetch($template_path, $cache_id = null, $compile_id = null, $display = false) {
			$template_path = coreResourceLibrary::getAbsolutePath($template_path);
			//echo $template_path ."<br>";
			return parent::fetch($template_path, $cache_id, $compile_id, $display);
		}
		
		
	}