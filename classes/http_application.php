<?php

	class coreHttpApplicationClass extends coreApplicationClass {
	
	
		protected $module_name;
		protected $module_params;
		protected $module;
				
		public function render() {
		
			$this->route();
			
			$user_session = $this->getUserSession();
			$user_logged = $user_session->getUserAccount();			
			
			if ($this->module_name) {
				$this->module = Application::getResourceInstance('module', $this->module_name);
												
				if (coreAccessControlLibrary::accessAllowed($user_logged, $this->module)) {																				
					$content = call_user_func(array($this->module, 'run'), $this->module_params);										
				}
				else {
					$this->stackError(Application::gettext('You should login as admin'));
					$user_session->logout();
					Redirector::redirect(Application::getSeoUrl('/login?back=' . Router::getSourceUrl()));
				}
			} else {
				$content = Application::runModule('page404');
			}
			
						
			$page = $this->getPage();			
			$this->displayPage($page, $content);
		
		}
		
		
		protected function route() {
			
			Application::loadLibrary('olmi/request');
			Application::loadLibrary('core/router');
								
			$url = ltrim($_SERVER['REQUEST_URI'], '/');

			$user_logged = $this->getUserSession()->getUserAccount();			
			
			Router::setDefaultModuleName($user_logged ? 'index' : 'login');
			
			Router::route($url);			
					
			$this->module_name = Router::getModuleName();
			$this->module_params = Router::getModuleParams();
			
		}
		
		protected function displayPage($page, $content) {
			$html_head = $page->getHtmlHead();
			$smarty = Application::getSmarty();
			
			$smarty->assign('html_head', $html_head);
			$smarty->assign('content', $content);
		
			$template_path = coreResourceLibrary::getTemplatePath('index');
			
			$smarty->display($template_path);
		}
		
	
	}