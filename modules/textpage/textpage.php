<?php

    

    class coreTextpageModule extends coreBaseModule {
    	
        public function run($params = array()) {
                    	
        	$url = isset($params['page_url']) ? $params['page_url'] : 'index';

            $document = Application::getEntityInstance('document');

            if (!$page = $document->loadToUrl($url, CURRENT_LANGUAGE)) {
                return Application::runModule('page404');
            }
            
            if (!$page->is_active) {
            	return Application::runModule('page404');
            }
            
            corePagePropertiesHelperLibrary::setTitleDescKeysFromObject($page);
            
            $breadcrumbs = Application::getBreadcrumbs();
            if ($page->parent_id ) {
                if ($parent_document = $document->load($page->parent_id )) {
                    $breadcrumbs->addNode(Application::getSeoUrl("/{$parent_document->url}" ), $parent_document->menu );
                }
            }

            $breadcrumbs->addNode(Application::getSeoUrl("/{$page->url}" ), $page->title );
            
            
            $smarty = Application::getSmarty();
            $smarty->assign('page', $page);
            $smarty->assign('breadcrumbs', Application::getBlock('breadcrumbs'));

            $template_path = $this->getTemplatePath();
            return $smarty->fetch($template_path);
        }
    }
