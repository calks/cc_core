<?php

	class coreProfileModule extends coreBaseModule {
		
		protected $user;
		protected $task;
		
		public function run($params=array()) {
			
			$this->task = @array_shift($params);

			$user_session = Application::getUserSession();			
			
			if (!$user_session->userLogged()) {
				return $this->ifNotLoggedIn();
			}
			
			$this->user = $user_session->getUserAccount();		
			
			$smarty = Application::getSmarty();
			
			if (!$this->task) $this->task = 'info';
			$method_name = 'task' . ucfirst($this->task);
			if (method_exists($this, $method_name)) {
				call_user_func(array($this, $method_name), $params);
			}
			else {
				return $this->terminate();
			}
			
			$page_template = $this->getTemplatePath($this->task);
			
			$smarty->assign('user', $this->user);
			$page_content = $smarty->fetch($page_template);
			
			$smarty->assign('menu', $this->getMenuHtml());
			$smarty->assign('message_stack_block', Application::getBlock('message_stack'));
			$smarty->assign('page_content', $page_content);

			$main_template = $this->getTemplatePath('wrap');
			return $smarty->fetch($main_template);			
		}
		
		
		protected function getMenuItems() {
			$items = array();
			
			$items['info'] = array(
				'name' => 'Карточка',
				'link' => Application::getSeoUrl("/{$this->getName()}/info"),
				'active' => $this->task == 'info'			
			);
			
			$items['logout'] = array(
				'name' => 'Выход',
				'link' => Application::getSeoUrl("/login/logout"),
				'active' => false 
			);
			
			return $items;
		}
		
		protected function getMenuHtml() {
			
			if (!$this->user) return null;
			
			$smarty = Application::getSmarty();
			
			$smarty->assign('items', $this->getMenuItems());
			$smarty->assign('task', $this->task);
			$template_path = $this->getTemplatePath('menu');
			return $smarty->fetch($template_path);
			
		}
		
		protected function getProfileForm() {			
			
			$form_class = coreResourceLibrary::getEffectiveClass('form', 'profile_edit');
			$form = new $form_class();
			$form->setAction(Application::getSeoUrl("/{$this->getName()}"));
			$form->setMethod('post');
			
			return $form;
		}
		
		protected function emailIsUnique($email) {
			$db = Application::getDb();
			$table = $this->user->getTableName();
			$id = (int)$this->user->id;
			if (!$id) return false;
			$email = addslashes($email);

			$sql = "
				SELECT COUNT(*)
				FROM $table
				WHERE email='$email' AND id!=$id
			";
			
			return !(bool)$db->executeScalar($sql);
		}
		
		protected function getProfileFormErrors($form) {
			$errors = array();
			
			if (!$form->getValue('name')) {
				$errors['firstname'] = "Нужно заполнить поле &laquo;Имя&raquo;";
			}
			
			if (!$form->getValue('family_name')) {
				$errors['lastname'] = "Нужно заполнить поле &laquo;Фамилия&raquo;";
			}

			$email = $form->getValue('email'); 
			if (!$email) {
				$errors['email'] = "Нужно заполнить поле &laquo;Email&raquo;";
			}
			else {
				if (!email_valid($email)) {
					$errors['email'] = "Вы ввели неправильный Email";	
				}
				elseif (!$this->emailIsUnique($email)) {
					$errors['email'] = "Введенный Email используется другим пользователем";
				}
			}
			
			$pass = $form->getValue('new_pass');
			$pass_confirmation = $form->getValue('new_pass_confirmation');
			
			if ($pass != $pass_confirmation) {
				$errors['new_pass_confirmation'] = "Подтверждение и пароль не совпадают";
			}
			
			return $errors;
		}
		
		
		
		protected function taskInfo($params=array()) {
			
			$smarty = Application::getSmarty();
			
			$form = $this->getProfileForm();
			$form->setValues($this->user);
			
			if (coreRequestLibrary::isPostMethod()) {
				$form->LoadFromRequest($_REQUEST);
				$form->validate();
				$form_errors = $form->getErrors();			
				
				if (!$form_errors) {
					$form->UpdateObject($this->user);
					$new_pass = $form->getValue('new_pass');
					$password_changed = false;
					if ($new_pass) {
						$this->user->setPassword($new_pass);
						$password_changed = true;
					}
					if ($this->user->save()) {
						Application::stackMessage($this->gettext('Profile saved successfully'));
						if ($password_changed) {
							Application::stackMessage($this->gettext('Password changed'));
						}
						Redirector::redirect(Application::getSeoUrl("/{$this->getName()}/$this->task"));		
					}
					else {
						Application::stackError($this->gettext('Failed to save profile'));
					}
				}
				else {
					foreach ($form_errors as $field_name => $errors) {
						Application::stackError(implode('<br />', $errors));
					}	
				}
			}

			$smarty->assign('profile_form', $form);
			$smarty->assign('form_action', Application::getSeoUrl("/{$this->getName()}/$this->task"));
		}
		
		
		protected function ifNotLoggedIn() {
			Redirector::redirect(Application::getSeoUrl("/login"));
		}
		
		

		
		
	}
	
	
	
	
	