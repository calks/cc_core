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
			Application::loadLibrary('olmi/form');
			$profile_form = new BaseForm();
			
			$profile_form->addField(new TEditField('name', '', 50, 255));
			$profile_form->addField(new TEditField('family_name', '', 50, 255));
			$profile_form->addField(new TEditField('email', '', 50, 255));
			$profile_form->addField(new TPasswordField('new_pass', '', 50, 100));
			$profile_form->addField(new TPasswordField('new_pass_confirmation', '', 50, 100));
			
			return $profile_form;			
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
			$form->loadFromObject($this->user);
			
			if (Request::isPostMethod()) {
				$form->LoadFromRequest($_REQUEST);
				$form_errors = $this->getProfileFormErrors($form);
				if (!$form_errors) {
					$form->UpdateObject($this->user);
					$new_pass = $form->getValue('new_pass');
					if ($new_pass) $this->user->setPassword($new_pass);
					if ($this->user->save()) {
						Application::stackMessage("Изменения сохранены");
						Redirector::redirect(Application::getSeoUrl("/{$this->getName()}/$this->task"));		
					}
					else {
						Application::stackError("Не удалось сохранить профиль");
					}
				}
				else {
					Application::stackError(implode('<br />', $form_errors));
				}
			}

			$smarty->assign('profile_form', $form);
			$smarty->assign('form_action', Application::getSeoUrl("/{$this->getName()}/$this->task"));
		}
		
		
		protected function ifNotLoggedIn() {
			Redirector::redirect(Application::getSeoUrl("/login"));
		}
		
		

		
		
	}
	
	
	
	
	