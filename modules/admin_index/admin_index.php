<?php

	class coreAdminIndexModule extends coreAdminBaseModule {
		
		public function run($params=array()) {
			
			$user_session = Application::getUserSession();
			
			if (!$user_session->userLogged()) Redirector::redirect(Application::getSeoUrl('/login'));
						
			$smarty = Application::getSmarty();
			$template_path = $this->getTemplatePath();
			return $smarty->fetch($template_path);
		}
		
		public function getPageTitle() {
			return 'Управление сайтом';
		}
		
		public function getPageSubtitle() {
			return '';
		}
		
		public function getPageActions() {
			return array();
		}
		
		
	}
	
	