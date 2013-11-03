<?php

	class coreLoginModule extends coreBaseModule {
		
		protected $action;
		protected $user_session;
		protected $login_form;
		protected $errors;
		
		public function getPageTitle() {
			return 'Вход в систему';
		}
		
		public function getPageSubtitle() {
			return '';
		}
		
		public function getPageActions() {
			return array();
		}
		
		
		public function run($params=array()) {
			$this->action = Request::get('action', 'login');
			$this->user_session = Application::getUserSession();
			$this->errors = array();
			
			$method_name = 'task' . ucfirst($this->action);
			if (!method_exists($this, $method_name)) return $this->terminate();

			call_user_func(array($this, $method_name), $params);
			
			$smarty = Application::getSmarty();
			$smarty->assign('errors', $this->errors);
			$smarty->assign('message_stack', Application::getBlock('message_stack'));
			
			
			$template_path = $this->getTemplatePath($this->action);
						
			//die($template_path);
			return $smarty->fetch($template_path);			
		}
		
		protected function taskLogin() {
			if ($this->user_session->userLogged()) Redirector::redirect('/');
			
			$this->login_form = array(
				'login' => isset($_POST['login_form']['login']) ? $_POST['login_form']['login'] : '',
				'pass' => isset($_POST['login_form']['pass']) ? $_POST['login_form']['pass'] : '' 
			);
			
						
			if (Request::isPostMethod()) {
				$login = $this->login_form['login']; 
				$pass = $this->login_form['pass'];
				
				if($this->user_session->auth($login, $pass)) {
					Redirector::redirect('/');
				}
				else $this->errors[] = "Неправильный Email/телефон и/или пароль"; 
			}
			
			$smarty = Application::getSmarty();
			$smarty->assign('login_form', $this->login_form);
			//$smarty->assign('forgot_link', "/admin/{$this->getName()}?action=forgot");
			$smarty->assign('form_action', Application::getSeoUrl("/{$this->getName()}"));
			$smarty->assign('register_link', Application::getSeoUrl("/register"));
			
		}
		
		protected function taskLogout() {
			$this->user_session->logout();
			Redirector::redirect(Application::getSeoUrl("/{$this->getName()}"));
		}
		
		
	}