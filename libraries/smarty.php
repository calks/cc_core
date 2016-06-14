<?php

	require_once 'smarty/Smarty.Class.php';
	
	class coreSmartyLibrary extends Smarty {
		
		
		public function convertPaths($source, Smarty_Internal_Template $template) {
			
			preg_match_all('/(?P<directive>\{\s*include.*file\s*=\s*(?P<include_path>\S+)(\s+.*)?})/isU', $source, $matches, PREG_SET_ORDER);
			if (!$matches) return $source;
			
			foreach ($matches as $m) {
				$directive = $m['directive'];
				$include_path = $m['include_path'];
				$directive_replacement = str_replace($include_path, "coreResourceLibrary::getAbsolutePath($include_path)", $directive);
				$source = str_replace($directive, $directive_replacement, $source);
			}
			
			return $source;		
		}
		
		public function __construct() {
			parent::__construct();
			$this->registerFilter('pre', array($this, 'convertPaths'));
		}
		
		public function fetch($template = null, $cache_id = null, $compile_id = null, $parent = null, $display = false, $merge_tpl_vars = true, $no_output_filter = false) {
			/*$display_errors = ini_get('display_errors');
			ini_set('display_errors', 0);*/
			$this->error_reporting = E_ERROR;
			if (!is_object($template)) {
				$template = coreResourceLibrary::getAbsolutePath($template);
			}
			$out = parent::fetch($template, $cache_id, $compile_id, $parent, $display, $merge_tpl_vars, $no_output_filter);
			//ini_set('display_errors', $display_errors);
			return $out;
		}
		
		
		
	}