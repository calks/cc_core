<?php

	class coreLoginModule extends coreBaseModule {
		
		protected $action;
		protected $user_session;
		protected $login_form;
		protected $errors;
		
		public function run($params=array()) {
			
			$this->action = @array_shift($params);
			if (!$this->action) $this->action = 'login';
			$this->user_session = Application::getUserSession();
			$this->errors = array();
			
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
			//$smarty->assign('forgot_link', "/admin/{$this->getName()}?action=forgot");
			$smarty->assign('form_action', Application::getSeoUrl("/{$this->getName()}"));
			$smarty->assign('register_link', Application::getSeoUrl("/register"));
			
		}
		
		protected function taskLogout() {			
			$this->user_session->logout();
			return $this->onSuccessLogout();
		}
		
		protected function ifLoggedIn() {
			Redirector::redirect(Application::getSeoUrl("/profile"));
		}
		
		protected function onSuccessLogin() {
			Redirector::redirect(Application::getSeoUrl("/profile"));
		}
		
		protected function onSuccessLogout() {
			Redirector::redirect(Application::getSeoUrl("/{$this->getName()}"));
		}
		
		
	}