<?php

	class coreRegisterModule extends coreBaseModule {
		
		protected $task;		
		protected $user_form;
		
		
		public function run($params=array()) {
			
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
				$smarty->assign('reg_form_action', Application::getSeoUrl("/{$this->getName()}/save"));
				$smarty->assign('login_link', Application::getSeoUrl("/login"));
				$smarty->assign('message_stack_block', Application::getBlock('message_stack'));
				
				$template_path = $this->getTemplatePath();
				return $smarty->fetch($template_path);				
			}
			else {
				return $this->terminate();
			}						
		}
				
		protected function getUserForm() {
			Application::loadLibrary('olmi/form');
			$form = new BaseForm();
			
			$form->addField(coreFormElementsLibrary::get('edit', 'name'));
			$form->addField(coreFormElementsLibrary::get('edit', 'family_name'));
			$form->addField(coreFormElementsLibrary::get('edit', 'email'));
			$form->addField(coreFormElementsLibrary::get('password', 'password'));
			$form->addField(coreFormElementsLibrary::get('password', 'password_confirmation'));
			
			return $form;
		}
		
		protected function taskDisplayForm($params=array()) {
		
		}
		
		
		protected function getFormErrors() {
			$errors = array();
			
			if(!$this->user_form->getValue('name')) {
				$errors['name'] = "Вы не ввели имя";				
			}
			
			if(!$this->user_form->getValue('family_name')) {
				$errors['family_name'] = "Вы не ввели фамилию";
			}
			
			$email = $this->user_form->getValue('email');
			if (!$email) {
				$errors['email'] = "Вы не ввели Email";
			}
			elseif (!email_valid($email)) {
				$errors['email'] = "Вы ввели неправильный Email";
			}
			elseif (!$this->isEmailVacant($email)) {
				$errors['email'] = "На указанный Email уже зарегистрирован аккаунт";
			}
			
			$pass = $this->user_form->getValue('password');
			$pass_confirmation = $this->user_form->getValue('password_confirmation');
			
			if (!$pass) {
				$errors['password'] = "Вы не ввели пароль";
			}
			elseif ($pass !== $pass_confirmation) {
				$errors['password'] = "Пароль и подтверждение не совпадают";
			}
			
			return $errors;
		}
		
		protected function isEmailVacant($email) {
			$user = Application::getEntityInstance('user');
			return (int)$user->getIdByEmail($email) == 0;
		}
		
		protected function updateFormFromRequest() {
			$this->user_form->loadFromRequest($_REQUEST);
			foreach ($this->user_form->fields as $f) {
				if (!in_array($f->Name, array('password', 'password_confirmation'))) {
					$f->setValue(trim($f->getValue()));
				}
			}
		}
		
		
		protected function taskSave($params=array()) {
			$this->updateFormFromRequest();
			$form_errors = $this->getFormErrors();
			
			if (!$form_errors) {				
				$user = $this->createUser();
				$registered_user_id = $user->save();
				if ($registered_user_id) {
					return $this->onSuccess($registered_user_id);
				}
				else {
					Application::stackError("Не удалось завершить регистрацию");
				}
			}
			else {
				Application::stackError(implode('<br />', $form_errors));
			}
		}
		
		
		protected function createUser() {
			$user = Application::getEntityInstance('user');
			$user->name = $this->user_form->getValue('name');
			$user->family_name = $this->user_form->getValue('family_name');
			$user->email = $this->user_form->getValue('email');
			$user->login = $user->email;
			$user->setPassword($this->user_form->getValue('password'));
			
			$user->roles[] = USER_ROLE_CONSUMER;
			$user->active = 1;
			return $user;
		}
		
		
		protected function onSuccess($registered_user_id) {
			$user_session = Application::getUserSession();
			$user_session->forceLogin($registered_user_id);
			Application::stackMessage("Вы успешно зарегистрировались на сайте");
			Redirector::redirect(Application::getSeoUrl("/profile"));
		}
		
		
		protected function ifLoggedIn() {
			Redirector::redirect(Application::getSeoUrl("/profile"));
		}
		
		
		
	}
	
	
	