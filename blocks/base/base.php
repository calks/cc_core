<?php

	class coreBaseBlock extends Block {
		
		public function render() {
			$smarty = Application::getSmarty();
			$template_path = $this->getTemplatePath();
			return $smarty->fetch($template_path);
		}
		
	}
