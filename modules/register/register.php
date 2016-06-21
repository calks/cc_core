<?php

	class coreRegisterModule extends coreBaseModule {
		
		protected $task;		
		protected $user_form;
		protected $back_url;
		
		
		public function run($params=array()) {
			
			$this->back_url = Request::get('back');
			
			$user_session = Application::getUserSession();
			if ($user_session->userLogged()) {
				return $this->ifLoggedIn();
			}
			
			$this->task = @array_shift($params);
			if (!$this->task) $this->task = 'display_form';
			$method_name = 'task' . ucfirst(coreNameUtilsLibrary::underscoredToCamel($this->task));
			if (method_exists($this, $method_name)) {				

				$this->user_form = $this->getUserForm();
				
				$smarty = Application::getSmarty();	
				
				call_user_func(array($this, $method_name), $params);
				
				$smarty->assign('task', $this->task);
				
				$smarty->assign('user_form', $this->user_form);
				
				$login_link = "/login";
				if ($this->back_url) $login_link .= '?back=' . rawurlencode($this->back_url);
				$smarty->assign('login_link', Application::getSeoUrl($login_link));
				
				$form_action = "/{$this->getName()}/save";
				if ($this->back_url) $form_action .= '?back=' . rawurlencode($this->back_url);			
				$smarty->assign('reg_form_action', Application::getSeoUrl($form_action));
				
				
				$smarty->assign('message_stack_block', Application::getBlock('message_stack'));
				
				$template_path = $this->getTemplatePath();
				return $smarty->fetch($template_path);				
			}
			else {
				return $this->terminate();
			}						
		}
				
		protected function getUserForm() {
			
			$form_class = coreResourceLibrary::getEffectiveClass('form', 'registration');
			$form = new $form_class();
			$form->setAction(Application::getSeoUrl("/{$this->getName()}/save"));
			$form->setMethod('post');
			
			return $form;
		}
		
		protected function taskDisplayForm($params=array()) {
		
		}
		
		protected function updateFormFromRequest() {
			$this->user_form->loadFromRequest($_REQUEST);
			/*foreach ($this->user_form->fields as $f) {
				if (!in_array($f->Name, array('password', 'password_confirmation'))) {
					$f->setValue(trim($f->getValue()));
				}
			}*/
		}
		
		
		protected function taskSave($params=array()) {
			$this->updateFormFromRequest();
			$this->user_form->validate();
			$form_errors = $this->user_form->getErrors();			
			if (!$form_errors) {				
				$user = $this->createUser();
				$registered_user_id = $user->save();
				if ($registered_user_id) {
					return $this->onSuccess($registered_user_id);
				}
				else {
					Application::stackError($this->gettext("Failed to registr a user"));
				}
			}
			else {
				foreach ($form_errors as $field_name => $errors) {
					Application::stackError(implode('<br />', $errors));
				}
				
			}
		}
		
		
		protected function createUser() {
			$user = Application::getEntityInstance('user');
			$user->first_name = $this->user_form->getValue('first_name');
			$user->last_name = $this->user_form->getValue('last_name');
			$user->email = $this->user_form->getValue('email');
			$user->login = $user->email;
			$user->setPassword($this->user_form->getValue('password'));
			
			$user->addRole('registered');
			$user->is_active = 1;
			return $user;
		}
		
		
		protected function onSuccess($registered_user_id) {			
			$user_session = Application::getUserSession();
			$user_session->forceLogin($registered_user_id);
			Application::stackMessage($this->gettext('You have registered successfully'));			
			Redirector::redirect($this->back_url ? $this->back_url : Application::getSeoUrl("/profile"));
		}
		
		
		protected function ifLoggedIn() {			
			Redirector::redirect($this->back_url ? $this->back_url : Application::getSeoUrl("/profile"));
		}
		
		
		
	}
	
	
	