<?php

	class AdminHeaderBlock extends Block {
		
		public function run($params=array()) {
			
			$user_session = Application::getUserSession();
			if (!$user_session->userLogged()) return $this->terminate();

			$smarty = Application::getSmarty();
			
			$top_menu = Application::getBlockContent('admin_menu');
			$smarty->assign('top_menu', $top_menu);
					
			$smarty->assign('user_logged', $user_session->getUserAccount());
			$smarty->assign('logout_link', '/admin/admin_login?action=logout');			
			
			$template_path = $this->getTemplatePath();
			return $smarty->fetch($template_path);
			
		}
		
	}