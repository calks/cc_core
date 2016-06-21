<?php

	class coreBaseBlock extends coreControllerLibrary {
		
		protected $block_id;
		
		
		public function __construct() {
			parent::__construct();
			$this->block_id = $this->getName() . md5(uniqid());
		}
		
		public function getBlockId() {
			return $this->block_id;
		}
		
		public function render() {			
			$smarty = Application::getSmarty();
			$template_path = $this->getTemplatePath();
			$smarty->assign('block', $this);
			$this->html = $smarty->fetch($template_path);
			return $this->html;
		}
		
		protected function terminate() {
			return '';
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
