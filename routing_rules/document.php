<?php

	class coreSeoRulesAddonDocumentRule extends RewriteRule {
		
        public function seoToInternal(URL $seo_url) {
            $address = $seo_url->getAddress();            
            $address_parts = explode('/', $address);
            
            $module_name = @array_shift($address_parts);            
            if (!$module_name) return false;
            if (Application::resourceExists($module_name, APP_RESOURCE_TYPE_MODULE)) return false;
            
            array_unshift($address_parts, $module_name);
            
            
            $document = Application::getEntityInstance('document');
            if ($document->loadToUrl(implode('/', $address_parts))) {
            	array_unshift($address_parts, 'document');
	            $seo_url->setAddress(implode('/', $address_parts));
	            return $seo_url;	
            }
            
            return false;
        }


        public function internalToSeo(URL $internal_url) {        	
        	/*$address = trim($internal_url->getAddress(), ' /');        	
        	
        	print_r($address);
        	
        	$new_address = $address ? "document/$address" : "document";
        	 
        	$internal_url->setAddress($new_address);*/
            return $internal_url;
        }
        		
		
	}