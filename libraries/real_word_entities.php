<?php

	class coreRealWordEntitiesLibrary {
		
		protected static $languages = null;
		protected static $countries = null;
		
		public static function getLanguages($add_null_item=false, $key='id', $value='english_name') {
			
			if (is_null(self::$languages)) {
				$db = Application::getDb();
				self::$languages = $db->executeSelectAllObjects("
					SELECT * FROM languages
					WHERE code IN('ru', 'en', 'uk', 'other')
				");
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
					WHERE code_a2 IN('ru', 'ua', 'kz', 'europe', 'asia', 'other')
				");
			}
			
			$out = get_empty_select($add_null_item);
			foreach (self::$countries as $c) {
				$out[$c->$key] = $c->$value ? $c->$value : $c->english_name;
			}
			
			return $out;			
		}
		
		
	}