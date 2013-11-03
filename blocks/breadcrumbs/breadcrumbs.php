<?php

	class coreBreadcrumbsBlock extends coreBaseBlock {
		public function run($params = array()) {
						
			if (in_array(Router::getSourceUrl(), array('start.html', ''))) {
				return $this->terminate();
			}
	
			if (Request::get('content_only')) {
				return;
			}
	
			$smarty = Application::getSmarty();
			$breadcrumbs = Application::getBreadcrumbs();
	
			$template_path = $this->getTemplatePath();
			$smarty->assign('path', $breadcrumbs->getPath());
	
			return $smarty->fetch($template_path);
		}
	}
