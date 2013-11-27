<?php

	class coreLoginModule extends coreBaseModule {
		
		protected $action;
		protected $user_session;
		protected $login_form;
		protected $errors;
		protected $back_url;
		
		public function run($params=array()) {
			
			$this->action = @array_shift($params);
			if (!$this->action) $this->action = 'login';
			$this->user_session = Application::getUserSession();
			$this->errors = array();
			
			$this->back_url = Request::get('back');
						
			$method_name = coreNameUtilsLibrary::underscoredToCamel('task_' . $this->action);			
			if (!method_exists($this, $method_name)) return $this->terminate();

			call_user_func(array($this, $method_name), $params);
			
			$smarty = Application::getSmarty();
			$smarty->assign('errors', $this->errors);
			$smarty->assign('message_stack_block', Application::getBlock('message_stack'));
			
			
			$template_path = $this->getTemplatePath($this->action);
						
			return $smarty->fetch($template_path);			
		}
		
		protected function taskLogin() {
			if ($this->user_session->userLogged()) {
				return $this->ifLoggedIn();	
			}
			
			$this->login_form = array(
				'login' => isset($_POST['login_form']['login']) ? $_POST['login_form']['login'] : '',
				'pass' => isset($_POST['login_form']['pass']) ? $_POST['login_form']['pass'] : '' 
			);
			
						
			if (Request::isPostMethod()) {
				$login = $this->login_form['login']; 
				$pass = $this->login_form['pass'];
				
				if($this->user_session->auth($login, $pass)) {
					return $this->onSuccessLogin();
				}
				else Application::stackError("Неправильный Email и/или пароль"); 
			}
			
			
			$smarty = Application::getSmarty();
			$smarty->assign('login_form', $this->login_form);
			
			$recover_link = "/{$this->getName()}/recover";			
			if ($this->back_url) $recover_link .= '?back=' . rawurlencode($this->back_url);
			$smarty->assign('recover_link', Application::getSeoUrl($recover_link));
			
			$register_link = "/register";			
			if ($this->back_url) $register_link .= '?back=' . rawurlencode($this->back_url);
			$smarty->assign('register_link', Application::getSeoUrl($register_link));
			
			$form_action = "/{$this->getName()}";
			if ($this->back_url) $form_action .= '?back=' . rawurlencode($this->back_url);			
			$smarty->assign('form_action', Application::getSeoUrl($form_action));
			
		}
		
		
		protected function taskRecover() {
			if ($this->user_session->userLogged()) {
				return $this->ifLoggedIn();	
			}
			
			$email = '';
			
						
			if (Request::isPostMethod()) {
				
				$email = Request::get('email');
				$email_ok = true;
				
				if (!$email) {
					Application::stackError("Вы не ввели Email");					
				}
				elseif (!email_valid($email)) {
					Application::stackError("Вы ввели неправильный Email");					
				}
				else {
					$user = Application::getEntityInstance('user');
					$user_id = $user->getIdByEmail($email);
					$user = $user->load($user_id);
					
					if (!$user_id) {
						Application::stackWarning("Пользователь с таким Email не найден");
					}
					else {
						$new_pass = $this->generatePassword();
						if (!$this->sendNewPassword($user_id, $new_pass)) {
							Application::stackError("Не удалось сбросить пароль");
						}
						else {
							$user->setPassword($new_pass);
							$user->save();
							Application::stackMessage("Письмо c новым паролем отправлено на адрес $email");
							$redirect_url = Application::getSeoUrl("/{$this->getName()}/$this->action");
							Redirector::redirect($redirect_url);
						}
					}
				}
			}
			
			
			$smarty = Application::getSmarty();
			$smarty->assign('email', $email);
			
			$login_link = "/{$this->getName()}";
			if ($this->back_url) $login_link .= '?back=' . rawurlencode($this->back_url);
			$smarty->assign('login_link', Application::getSeoUrl($login_link));
			
			$form_action = "/{$this->getName()}/$this->action";
			if ($this->back_url) $form_action .= '?back=' . rawurlencode($this->back_url);			
			$smarty->assign('form_action', Application::getSeoUrl($form_action));

		}
		
		
		protected function generatePassword() {
			return substr(md5(uniqid()), 10);
		}
		
		
		protected function sendNewPassword($user_id, $password) {
			$user = Application::getEntityInstance('user');
			$user = $user->load($user_id);
			
			if (!$user) return false;
			
			$user_name = "$user->name $user->family_name";
			$user_email = $user->email;
			
			$smarty = Application::getSmarty();
			$default_content = $smarty->fetch($this->getTemplatePath('recover_email'));
			
			$email_template = mailPkgTemplateLibrary::get('Новый пароль после сброса');
			$email_template->setDefaultContent($default_content);
			$email_template->setLegend(array(
				'new_pass' => 'Новый пароль',				
			));
			$email_template->setReplacements(array(
				'new_pass' => $password,
				'user_name' => $user_name,
				'user_email' => $user_email
			));

			
			$body = $email_template->getFilledContent();
			
			$mailer = Application::getMailer();
 			
			$mailer->setBody($body);				
			$mailer->setSubject("Сброс пароля");

			$mailer->AddAddress($user_email, $user_name);
				
			return @$mailer->Send();
		}
		
		protected function taskLogout() {			
			$this->user_session->logout();
			return $this->onSuccessLogout();
		}
		
		protected function ifLoggedIn() {
			Redirector::redirect($this->back_url ? $this->back_url : Application::getSeoUrl("/profile"));
		}
		
		protected function onSuccessLogin() {
			Redirector::redirect($this->back_url ? $this->back_url : Application::getSeoUrl("/profile"));
		}
		
		protected function onSuccessLogout() {
			Redirector::redirect(Application::getSeoUrl("/{$this->getName()}"));
		}
		
		
	}