<?php

	class coreSettingsModule extends coreBaseModule {
	
		protected function commonLogic(&$params=array()) {
			$user_session = Application::getUserSession();
			if (!$user_session->userLogged()) Redirector::redirect(Application::getSeoUrl('/login'));
			
			$user = $user_session->getUserAccount();
			if (!in_array(USER_ROLE_ADMIN, $user->roles)) {
				$user_session->logout();
				Redirector::redirect('/login');
			}
			
			parent::commonLogic($params);
		}
		
		
		protected function taskDefault($params=array()) {
			$tree = coreSettingsLibrary::getUpToDateTree();
					
			if (Request::isPostMethod()) {
				coreSettingsLibrary::updateTreeFromPost();
				$errors = coreSettingsLibrary::getErrors();
				if (!$errors) {
					if (!coreSettingsLibrary::saveTree()) {
						Application::stackError('Не удалось сохранить настройки');
					}
					else {
						Application::stackMessage("Настройки сохранены");
						Redirector::redirect(Application::getSeoUrl('/' . $this->getName()));
					}
				}
				else {
					foreach ($errors as $e) {
						Application::stackError($e);
					}
				}
			}
			
			$smarty = Application::getSmarty();
			
			$smarty->assign('tree', $tree);
			$smarty->assign('group_names', coreSettingsLibrary::getGroupNames());
			$smarty->assign('message_stack_block', Application::getBlock('message_stack'));
			
			return parent::taskDefault($params);
		}
		
		public function getPageTitle() {
			return 'Настройки';
		}
		
		public function getPageSubtitle() {
			return '';
		}
		
		
		public function getPageActions() {
			return array();
		}
		
		
	}