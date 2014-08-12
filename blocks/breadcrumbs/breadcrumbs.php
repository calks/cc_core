<?php

	class coreBreadcrumbsBlock extends coreBaseBlock {
		public function render() {
	
			$smarty = Application::getSmarty();
			$breadcrumbs = Application::getBreadcrumbs();
	
			$template_path = $this->getTemplatePath();
			
			$path = $breadcrumbs->getPath();
			if (!$path) return $this->terminate();
			
			$smarty->assign('path', $path);
			
			return parent::render();
		}
	}
