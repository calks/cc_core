<?php

	class coreMessageStackBlock extends coreBaseBlock {

		public function render($params=array()) {			
					
			$smarty = Application::getSmarty();
			
			$message_stack = Application::getMessageStack();
			
			$message_from_request = Request::get('message');
			if ($message_from_request) {
				$message_stack->add($message_from_request, 'message');
			}
			
			$errors_from_smarty = isset($smarty->_tpl_vars['errors']) ? $smarty->_tpl_vars['errors'] : array();
			if($errors_from_smarty) {
				$smarty->assign('errors', array());
				foreach ($errors_from_smarty as $e) {
					$message_stack->add($e, 'error');
				}
			} 
			
			$messages = $message_stack->getList();
			$this->prepareMessages($messages);
			
			$message_stack->clear();
			
			$template_path = $this->getTemplatePath();
			$smarty->assign('stack_messages', $messages);
			
			return $smarty->fetch($template_path);			
		}	

		
		protected function prepareMessages(&$messages) {
			
		} 
		
	}