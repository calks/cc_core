<?php

	class coreBreadcrumbsBlock extends coreBaseBlock {
		public function render() {
	
			$smarty = Application::getSmarty();
			$breadcrumbs = Application::getBreadcrumbs();
	
			$template_path = $this->getTemplatePath();
			$smarty->assign('path', $breadcrumbs->getPath());
	
			return $smarty->fetch($template_path);
		}
	}
