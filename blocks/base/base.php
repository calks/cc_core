<?php

	class coreBaseBlock extends coreControllerLibrary {
		
		public function render() {			
			$smarty = Application::getSmarty();
			$template_path = $this->getTemplatePath();
			$this->html = $smarty->fetch($template_path);
			return $this->html;
		}
		
		protected function terminate() {
			return '';
		}

        protected function getResourceType() {
            return APP_RESOURCE_TYPE_BLOCK;
        }
		
        protected function taskRefresh($data=array()) {
        	$this->render();
        }
		
		public function runTask($task, $data=null) {
			$method_name = coreNameUtilsLibrary::underscoredToCamel('task_' . $task);
			if (method_exists($this, $method_name)) {
				call_user_func(array($this, $method_name), $data);				
			}
			else {
				Application::stackError("Ошибка в запросе");
			}
		}
		
		protected function getResponse() {
			return $this->composeAjaxResponse();
		}        
		
		
        
        

        
		
	}
