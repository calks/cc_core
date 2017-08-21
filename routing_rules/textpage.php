<?php

	class coreTextpageRoutingRule {
		
		
		public function seoToInternal(URL $seo_url) {
			
			$address = $seo_url->getAddress();
		
			$address_parts = explode('/', $address);
			$first_part = @array_shift($address_parts);
			
			if ($first_part != 'textpage') return false;
			
			$internal_url = new URL('textpage');
						
			$page_url_slug = @array_shift($address_parts);
			if ($page_url_slug) {
				$internal_url->addGetParam('page_url', $page_url_slug);
			}

			return $internal_url;
		}
		
		
		public function internalToSeo(URL $internal_url) {			
			$get_params = $internal_url->getGetParams();
			
			$address = array('textpage');
			
			$get_params = $internal_url->getGetParams();			
			$page_url_slug = isset($get_params['page_url']) ? $get_params['page_url'] : null;
			
			if ($page_url_slug) {
				$address[] = $page_url_slug;
			}
			
			$seo_url = new URL(implode('/', $address));
			
			return $seo_url;
		}
		
		
	}