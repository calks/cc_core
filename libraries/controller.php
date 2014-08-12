<?php

	abstract class coreControllerLibrary extends coreResourceObjectLibrary {
	
		protected $html;
		protected $response_data = array();
		protected $user_logged;

		
		abstract protected function terminate();
		
		abstract protected function runTask($task, $data);
		
		abstract protected function getResponse();
		
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
		
        public function getStaticFileUrl($path_relative_to_module) {
        	$path_relative_to_module = trim($path_relative_to_module, ' /');     
        	$own_url = coreResourceLibrary::getFirstFilePath($this->getResourceType(), $this->getName(), '/static/' . $path_relative_to_module); 
            if ($own_url) return $own_url;

            $parents = class_parents($this);
            $own_name = $this->getName();
            foreach ($parents as $p) {
            	$parent_name = coreNameUtilsLibrary::getResourceName($p);
            	if ($parent_name == $own_name) continue;
            	$parent_url = coreResourceLibrary::getFirstFilePath($this->getResourceType(), $parent_name, '/static/' . $path_relative_to_module);
            	if ($parent_url) return $parent_url;
            }
            
            return null;
        	
        }
        
        public function getTemplatePath($template_name = '') {        	        	
            $template_name_supplied = $template_name != '';        	
        	if (!$template_name_supplied) $template_name = $this->getName();            
            $own_template = coreResourceLibrary::getFirstFilePath($this->getResourceType(), $this->getName(), "/templates/$template_name.tpl");            
            if ($own_template) return $own_template;

            $parents = class_parents($this);            
            $own_name = $this->getName();
            foreach ($parents as $p) {
            	$parent_name = coreNameUtilsLibrary::getResourceName($p);
            	if ($parent_name == $own_name) continue;            	
            	if (!$template_name_supplied) $template_name = $parent_name;
            	$parent_template = coreResourceLibrary::getFirstFilePath($this->getResourceType(), $parent_name, "/templates/$template_name.tpl");
            	if ($parent_template) return $parent_template;
            }
            
            return null;
        }
		
		
	
	}