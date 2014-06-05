<?php

	class coreBaseModule {
		
		protected $html;
		protected $response_data = array();
		protected $user_logged;
		protected $task;
		
		
		protected function isAjax() {
			return (bool)Request::get('ajax');
		}
		
		
				
		protected function composeAjaxResponse() {
			$message_stack = Application::getMessageStack();
			$messages = $message_stack->getList();
			$message_stack->clear();

			$message_priority = array(
				'ok' => 0,
				'message' => 0,					
				'warning' => 1,
				'error' => 2
			);
			
			$status = 'ok';
			foreach($messages as $m) {					
				if($message_priority[$m['type']] > $message_priority[$status]) {
					$status = $m['type'];
				}
			}
			
			$out = array(
				'content' => $this->html,				
				'status' => $status,
				'messages' => $messages
			);
			
			foreach ($this->response_data as $k=>$v) {
				$out[$k] = $v;
			}
			
			return $out;
		
		}
		
		protected function returnResponse() {
			if ($this->isAjax()) {				
				$out = $this->composeAjaxResponse();
				$callback = Request::get('callback');
				if ($callback) {					
					die($callback . '(' . json_encode($out) . ');');
				}
				else {
					die(json_encode($out));	
				}
												
			}
			else return $this->html;
		}
		
		protected function returnError($message) {
			Application::stackError($message);
			$this->returnResponse();
		}
		
		
		protected function commonLogic(&$params) {
			
		}
		
		public function run($params=array()) {
			
			$user_session = Application::getUserSession();
			$this->user_logged = $user_session->getUserAccount();			
			$this->commonLogic($params);
			
			$task_from_params = @array_shift($params);
			$task_from_request = Request::get('task');
			$this->task = $task_from_request ? $task_from_request : $task_from_params;
			if (!$this->task) $this->task = 'default';
			
						
			
			if (!$this->isAjax()) {
				$smarty = Application::getSmarty();
				$smarty->assign('message_stack_block', Application::getBlock('message_stack'));
			}
			
			$method_name = coreNameUtilsLibrary::underscoredToCamel('task_' . $this->task);
			if (method_exists($this, $method_name)) {
				call_user_func(array($this, $method_name), $params);
				return $this->returnResponse();
			}
			else {				
				$this->terminate();
			}
			
		}

		
		protected function taskDefault($params=array()) {
			$template_path = $this->getTemplatePath();
			if ($template_path) {
				$smarty = Application::getSmarty();
				$this->html = $smarty->fetch($template_path);  
			}
			
			return $this->returnResponse();
		}
		
		
        protected function getModuleType() {
            return 'module';
        }

        public function getName() {
        	
        	$class = get_class($this);
            //$class = strtolower($class);
            $module_type = $this->getModuleType();
            
            if (preg_match('/(?P<container_complex_name>(?P<container_name>[a-zA-Z0-9]+)(?P<container_type>App|Pkg)|core)(?P<module_name>[a-zA-Z0-9]+)'.ucfirst($module_type).'/', $class, $matches)) {            	
            	return coreNameUtilsLibrary::camelToUnderscored($matches['module_name']);
            }
            
            $class = str_replace(ucfirst($module_type), '', $class);

            $application_name = Application::getApplicationName();
            $application_name_camel_case = str_replace(' ', '', ucwords(str_replace('_', ' ', $application_name))); 
            
            $class = str_replace($application_name, '', $class);
            $class = str_replace($application_name_camel_case, '', $class);
            
            $out = '';
            while($letter = substr($class, 0, 1)) {
            	if ($out && $letter != strtolower($letter)) $out .= '_';
            	$out .= strtolower($letter);
            	$class = substr($class, 1);
            }
            
            return $out;
        }

        protected function terminate() {
        	if ($this->isAjax()) {        		
        		Application::stackError('Ошибка в запросе');
        		$this->returnResponse();
        	}
        	else {
        		$this->html = Application::runModule('page404');
        		return $this->html;
        	}               
        }

        
        public function getStaticFileUrl($path_relative_to_module) {        	
        	$path_relative_to_module = trim($path_relative_to_module, ' /');        	
        	return coreResourceLibrary::getFirstFilePath($this->getModuleType(), $this->getName(), '/static/' . $path_relative_to_module);
        }
        
        public function getTemplatePath($template_name = '') {
            if (!$template_name) $template_name = $this->getName();
            return coreResourceLibrary::getFirstFilePath($this->getModuleType(), $this->getName(), "/templates/$template_name.tpl");       
        }

		
	}