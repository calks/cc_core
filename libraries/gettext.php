<?php

	class coreGettextLibrary {
		
		protected static function getTranslationSubresources($resource) {
			if (CURRENT_LANGUAGE == LANGUAGES_ENGLISH) return array();
			$language_code = coreRealWordEntitiesLibrary::getLanguageCode(CURRENT_LANGUAGE);
			if (!$language_code) return array();
			if ($resource) {
				$preferred = $resource->findEffectiveSubresources('translation', $language_code);
				$alternative = $resource->findAllSubresources('translation', $language_code);
			} 
			else {
				$preferred = coreResourceLibrary::findEffective('translation', $language_code);
				$alternative = coreResourceLibrary::findAll('translation', $language_code);
			}
			
			$out = array();
			
			$preferred_class = null;
			if (isset($preferred[$language_code])) {
				$out[] = $preferred[$language_code];
				$preferred_class = $preferred[$language_code]->class;
			}
			
			if (isset($alternative[$language_code])) {
				foreach ($alternative[$language_code] as $alt) {
					if ($preferred_class && $alt->class == $preferred_class) continue;
					$out[] = $alt;	
				}
			}
			
			return $out;
			
		}
		
		public static function gettext($resource, $message, $checking_general_translations=false) {

			$translated = false;
			
			foreach (self::getTranslationSubresources($resource) as $ts) {				
				$translation_class = $ts->class;				
				$translation_object = new $translation_class();
				$translations = $translation_object->getTranslations();
				if (isset($translations[$message])) {
					$message = $translations[$message];
					$translated = true;
					break;
				}
			}
			
			if (!$translated && !$checking_general_translations) {
				return self::gettext(null, $message, true);
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