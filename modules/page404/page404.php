<?php

    class corePage404Module extends Module {

        public function run($params=array()) {
            header("HTTP/1.0 404 Not Found");
            $page = Application::getPage();
            $page->setTitle('Страница не найдена');
            $page->setDescription('');
            $page->setKeywords('');
            $smarty = Application::getSmarty();
            $template_path = $this->getTemplatePath();            
            return $smarty->fetch($template_path);
        }
    }
