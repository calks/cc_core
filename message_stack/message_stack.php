<?php

	class adminMiscPkgMessageStackBlock extends coreBaseBlock {

		public function __construct() {
			$page = Application::getPage();			
			$page->addScript($this->getStaticFileUrl('/message_stack.js'));			
		}
		
		public function render($params=array()) {			
			$template_path = $this->getTemplatePath();			
			$smarty = Application::getSmarty();
			
			$message = Request::get('message');
			$smarty->assign('message', $message);
			
			return $smarty->fetch($template_path);			
		}		
		
	}