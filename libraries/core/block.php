<?php

    /*
     * For now, the most significant difference between
     * blocks and modules is that blocks can't be run
     * by passing their names through request string.
     *
     * In other words, blocks are modules placed in other
     * directory, outside of router's scope
     */

    Application::loadLibrary('core/module');

    class Block extends Module {

        public function __construct() {
            $this->run_mode = 'block';
        }

        protected function getModuleType() {
            return 'block';
        }

        public function run($params = array()) {
        	return '';
        }
        
		public function render($params = array()) {
			return $this->run($params);	
		}
        


    }
