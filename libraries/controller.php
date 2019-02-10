<?php

	abstract class coreControllerLibrary extends coreResourceObjectLibrary {
	
		protected $html;
		protected $response_data = array();
		protected $user_logged;

		
		abstract protected function terminate();
		
		abstract protected function runTask($task, $data);
		
		abstract protected function getResponse();
		
		protected function isAjax() {
			return (bool)coreRequestLibrary::get('ajax');
		}
		
		public function __construct() {
			$user_session = Application::getUserSession();
			$this->user_logged = $user_session->getUserAccount();
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
		

		protected function stopWithError($error_message) {
			Application::stackError($message);
			$this->returnResponse();
		}
		
		
		protected function returnError($message) {
			Application::stackError($message);
			$this->returnResponse();
		}
		
		
	
	}