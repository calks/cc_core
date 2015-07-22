<?php

	class coreGettextLibrary {
		
		protected static function getTranslationSubresources($resource) {
			if (CURRENT_LANGUAGE == LANGUAGES_ENGLISH) return array();
			$language_code = coreRealWordEntitiesLibrary::getLanguageCode(CURRENT_LANGUAGE);
			if (!$language_code) return array();
			if ($resource) {
				return $resource->findEffectiveSubresources('translation', $language_code);
			} 
			else {
				return coreResourceLibrary::findEffective('translation', $language_code);
			}
		}
		
		public static function gettext($resource, $message) {

			foreach (self::getTranslationSubresources($resource) as $ts) {
				$translation_class = $ts->class;
				$translation_object = new $translation_class();
				$translations = $translation_object->getTranslations();
				if (isset($translations[$message])) {
					$message = $translations[$message];
					break;
				}       				        				
			}
			        		
        	$sprintf_params = func_get_args();        	
        	array_shift($sprintf_params);
        	$sprintf_params[0] = $message;        	
        	return call_user_func_array('sprintf', $sprintf_params);
		}
		
		
		public static function ngettext($resource, $message, $message_plural, $n) {
		
			foreach (self::getTranslationSubresources($resource) as $ts) {
				$translation_class = $ts->class;
				$translation_object = new $translation_class();
				$translations = $translation_object->getTranslations();
				if (isset($translations[$message]) && is_array($translations[$message])) {
					$plural_index = $translation_object->getPluralIndex($n);
					if (isset($translations[$message][$plural_index])) {
						return $translations[$message][$plural_index];
					}
				}       				        				
			}
			
			return abs($n) == 1 ? $message : $message_plural;
		}
		
	
	}