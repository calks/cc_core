<?php

    class corePage404Module extends coreBaseModule {

        public function run($params=array()) {
            header("HTTP/1.0 404 Not Found");
            $page = Application::getPage();
            $page->setTitle($this->gettext('Page not found'));
            $page->setDescription('');
            $page->setKeywords('');
            $smarty = Application::getSmarty();
            $smarty->assign('module', $this);
            $template_path = $this->getTemplatePath();            
            return $smarty->fetch($template_path);
        }
    }
