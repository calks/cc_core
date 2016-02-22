<?php

	class coreDocumentModule extends coreBaseModule {
	
		public function run($params=array()) {
			$document = Application::getEntityInstance('document'); 
			$document = $document->loadToUrl(implode('/', $params));
			
			if (!$document) return $this->terminate();
			
			$breadcrumbs = Application::getBreadcrumbs();
			$breadcrumbs->addNode(Application::getSeoUrl("/"), "Главная" );
			$breadcrumbs->addNode(Application::getSeoUrl("/$document->url"), $document->title);
			
			corePagePropertiesHelperLibrary::setTitleDescKeysFromObject($document);			
			$smarty = Application::getSmarty();
			$smarty->assign('document', $document);
			$template_path = $this->getTemplatePath();
			return $smarty->fetch($template_path);
		
		}
	
	}