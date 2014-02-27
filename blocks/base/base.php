<?php

	class coreBaseBlock extends coreBaseModule {
		
		public function render() {
			$smarty = Application::getSmarty();
			$template_path = $this->getTemplatePath();
			return $smarty->fetch($template_path);
		}
		
		protected function terminate() {
			return '';
		}
		
        public function __construct() {
            $this->run_mode = 'block';
        }

        protected function getModuleType() {
            return 'block';
        }
		
		
	}
