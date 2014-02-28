<?php

	Application::loadLibrary('core/admin_module');

	class coreCrudBaseModule extends coreAdminBaseModule {
		
		public function run($params=array()) {
			$smarty = Application::getSmarty();
			$smarty->assign('message_stack', Application::getBlock('message_stack'));
			return parent::run($params);
		}
		
		
	}