<?php

	require_once 'smarty/Smarty.Class.php';
	
	class coreSmartyLibrary extends Smarty {
		
	    /*function _smarty_include($params) {
	    	
	    	if (!isset($params['smarty_include_tpl_file'])) return parent::_smarty_include($params);
	    	$file = $params['smarty_include_tpl_file'];
	    	if (is_file($file)) return parent::_smarty_include($params);
	    	
	    	$file_abs = Application::getSitePath() . $file;
	    	if (!is_file($file_abs)) return parent::_smarty_include($params);
	    	
	    	$params['smarty_include_tpl_file'] = $file_abs;
	    	return parent::_smarty_include($params);	       
	    }
		*/
		
		public function fetch($template = null, $cache_id = null, $compile_id = null, $parent = null, $display = false, $merge_tpl_vars = true, $no_output_filter = false) {
			$display_errors = ini_get('display_errors');
			ini_set('display_errors', 0);
			if (!is_object($template)) {
				$template = coreResourceLibrary::getAbsolutePath($template);
			}
			$out = parent::fetch($template, $cache_id, $compile_id, $parent, $display, $merge_tpl_vars, $no_output_filter);
			ini_set('display_errors', $display_errors);
			return $out;
		}
		
		
		
	}