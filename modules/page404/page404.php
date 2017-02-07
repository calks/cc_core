<?php

    class corePage404Module extends coreBaseModule {

        public function taskDefault($params=array()) {
            header("HTTP/1.0 404 Not Found");
            $page = Application::getPage();
            $page->setTitle($this->gettext('Page not found'));
            $page->setDescription('');
            $page->setKeywords('');
            $smarty = Application::getSmarty();
            $smarty->assign('module', $this);
                        
            return parent::taskDefault($params);
        }
    }
