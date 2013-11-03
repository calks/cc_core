<?php

	class adminMiscPkgPageHeadingBlock extends coreBaseBlock {
		
		protected $title;
		protected $subtitle;
		protected $actions = array();
		
		public function setTitle($title) {
			$this->title = $title;
		}
		
		public function setSubtitle($subtitle) {
			$this->subtitle = $subtitle;
		}
				
		public function addAction($caption, $url, $icon) {
			$action = new stdClass();
			$action->caption = $caption;
			$action->icon = $icon;
			$action->url = $url;
			
			$this->actions[] = $action;
		}
				
		public function render($params=array()) {			
			$template_path = $this->getTemplatePath();			
			$smarty = Application::getSmarty();

			$smarty->assign('title', $this->title);
			$smarty->assign('subtitle', $this->subtitle);
			$smarty->assign('actions', $this->actions);
			
			return $smarty->fetch($template_path);			
		}		
		
	}