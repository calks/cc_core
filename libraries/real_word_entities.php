<?php

	class coreRealWordEntitiesLibrary {
		
		protected static $languages = null;
		protected static $countries = null;
		
		
		public static function getLanguageCode($language_id) {
			$languages = self::getLanguages(false, 'id', 'code');
			return isset($languages[$language_id]) ? $languages[$language_id] : null;	
		}
		
		public static function getLanguageId($language_code) {
			$languages = self::getLanguages(false, 'code', 'id');
			return isset($languages[$language_code]) ? $languages[$language_code] : null;	
		}
		
		
		public static function getLanguages($add_null_item=false, $key='id', $value='english_name') {
			
			if (is_null(self::$languages)) {
				$languages_entity = Application::getEntityInstance('languages');
				$languages_table = $languages_entity->getTableName();
				
				$languages_params['where'][] = "`$languages_table`.`code` IN('ru', 'en', 'uk', 'other')";
								
				$db = Application::getDb();
				self::$languages = $languages_entity->load_list($languages_params);
			}
			
			$out = get_empty_select($add_null_item);
			foreach (self::$languages as $l) {
				$out[$l->$key] = $l->$value ? $l->$value : $l->english_name;
			}
			
			return $out;			
		}
		
		
		public static function getCountries($add_null_item=false, $key='id', $value='english_name') {
			if (is_null(self::$countries)) {
				$db = Application::getDb();
				self::$countries = $db->executeSelectAllObjects("
					SELECT * FROM countries
					WHERE code_a2 IN(/*'ru', 'ua', 'kz',*/ 'europe', 'asia', 'other')
				");
			}
			
			$out = get_empty_select($add_null_item);
			foreach (self::$countries as $c) {
				$out[$c->$key] = $c->$value ? $c->$value : $c->english_name;
			}
			
			return $out;			
		}
		
		
	}