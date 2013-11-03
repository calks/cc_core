<?php

    include_once 'rule.php';
    include_once 'url.php';

    class UrlRewriter {

        static protected $rules;

        protected function &getRules() {
        	require_once Application::getSitePath() . '/core/libraries/name_utils.php';
			require_once Application::getSitePath() . '/core/libraries/resource.php';
        	if (!is_array(self::$rules)) {
        		$rules_available = coreResourceLibrary::getAvailableFiles(APP_RESOURCE_TYPE_ADDON, 'seo_rules');
        		self::$rules = array();
        		
        		foreach ($rules_available as $rule_name => $rule) {
					
        			require_once coreResourceLibrary::getAbsolutePath($rule->path);
        			self::$rules[$rule_name] = new $rule->class();
        		}        		
        	}
        	
        	return self::$rules;
        }

        
        public function seoToInternal($seo_url) {
            $seo_url = trim($seo_url, ' /');

            $rules =& self::getRules();
            
            $common_rule = isset($rules['common_rule']) ? $rules['common_rule'] : null;
            if ($common_rule) {
            	$url = new URL($seo_url);
            	$new_seo_url = $common_rule->seoToInternal($url);
            	if (false !== $new_seo_url) {
            		$seo_url = $new_seo_url->toString();
            	}
            }
            
            

            $url = new URL($seo_url);
            foreach ($rules as $rule_name => $rule) {
            	if ($rule_name == 'common_rule') continue;
                $internal_url = $rule->seoToInternal($url);
                if (false === $internal_url) {
                    continue;
                }

                // Rewriter can add some new GET-parameters,
                // so $_GET and $_REQUEST arrays must be updated before further using.
                $_GET = $internal_url->getGetParams();
                $_REQUEST = array_merge($_REQUEST, $_GET);

                return $internal_url->toString();
            }

            return $seo_url;
        }

        public function internalToSeo($internal_url) {
            //return $internal_url;
            
        	$internal_url = trim($internal_url, ' /');

            //if (!$internal_url) return '/';

            $parts = explode('/', $internal_url);
            $module_name = array_shift($parts);
            $url = implode('/', $parts); 

            $rules =& self::getRules();
            
            
            $rule_name = $module_name . '_rule';
            if (isset($rules[$rule_name])) {
                $rule = $rules[$rule_name];                
                $url = new URL($url);

                $seo_url = $rule->internalToSeo($url);
                if ($seo_url !== false) $internal_url = $seo_url->toString();
            }

            $common_rule = isset($rules['common_rule']) ? $rules['common_rule'] : null;
            if ($common_rule) {
            	$url = new URL($internal_url);
            	$new_url = $common_rule->internalToSeo($url);
            	if (false !== $new_url) {
            		$internal_url = $new_url->toString();
            	}
            }
            
            
            return '/' . $internal_url;
        }

    }

