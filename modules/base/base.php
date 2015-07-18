<?php

	class coreBaseModule extends coreControllerLibrary {
		
		protected $task;
		
		
		protected function commonLogic(&$params) {
			
		}
		
		public function run($params=array()) {
			
			$this->commonLogic($params);
			
			$task_from_params = @array_shift($params);
			$task_from_request = Request::get('task');
			$this->task = $task_from_request ? $task_from_request : $task_from_params;
			if (!$this->task) $this->task = 'default';

			$this->runTask($this->task, $params);	
			
			return $this->returnResponse();			
		}
		
		
		protected function runTask($task, $params) {			
			if (!$this->isAjax()) {
				$smarty = Application::getSmarty();
				$smarty->assign('message_stack_block', Application::getBlock('message_stack'));
			}
			
			$method_name = coreNameUtilsLibrary::underscoredToCamel('task_' . $this->task);
			if (method_exists($this, $method_name)) {
				call_user_func(array($this, $method_name), $params);				
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
		

        protected function terminate() {        	
        	if ($this->isAjax()) {        		
        		Application::stackError('Ошибка в запросе');
        	}
        	else {
        		$this->html = Application::runModule('page404');
        	}               
        }

		protected function getResponse() {
			if ($this->isAjax()) {				
				$out = $this->composeAjaxResponse();
				$callback = Request::get('callback');
				if ($callback) {					
					return $callback . '(' . json_encode($out) . ');';
				}
				else {
					return json_encode($out);	
				}												
			}
			else return $this->html;
		}        
        
		protected function returnResponse() {
			$response = $this->getResponse();
			if ($this->isAjax()) {
				die($response);
			}
			else return $response;
		}
        


		
	}