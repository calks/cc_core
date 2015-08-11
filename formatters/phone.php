<?php

	class corePhoneFormatter {
	
		public function format($phone, $preset_name='default') {
			$phone_parsed = $this->parsePhone($phone);			
			$this->preformatPhoneParts($phone_parsed);						
			return trim(implode(' ', $phone_parsed));
		}
		
		
		protected function preformatPhoneParts(&$phone_parsed) {

			if ($phone_parsed['country_code']) $phone_parsed['country_code'] = '+' . $phone_parsed['country_code'];			
			if ($phone_parsed['area_code']) {
				$area_code = $this->addDashes($phone_parsed['area_code']);
				$phone_parsed['area_code'] = $phone_parsed['country_code'] ? $area_code : "($area_code)";
			}
			$phone_parsed['phone'] = $this->addDashes($phone_parsed['phone']);
			
		}
		
		protected function parsePhone($phone) {		
			$out = array();
						
			preg_match('/(\+(?P<country_code>\d+))?(\s|-|\.)?\(?(?P<area_code>[\d\-\.]+)?\)?(\s|-|\.)?(?P<phone>[\d\-\.]+)$/isU', $phone, $matches);
			
			$out['country_code'] = isset($matches['country_code']) ? $this->getDigitsOnly($matches['country_code']) : null;
			$out['area_code'] = isset($matches['area_code']) ? $this->getDigitsOnly($matches['area_code']) : null;
			$out['phone'] = isset($matches['phone']) ? $this->getDigitsOnly($matches['phone']) : null; 
			
			return $out;
		
		}
		
		
		protected function addDashes($phone) {	
			
			$phone_length = strlen($phone);
			
			if ($phone_length <= 3) return $phone;
			
			$first_part_length = strlen($phone) > 4 ? 3 : 2;

			$out = substr($phone, 0, $first_part_length);
			
			if ($first_part_length < $phone_length) {
				$out .= '-' . $this->addDashes(substr($phone, $first_part_length));
			}
			
			return $out;
		}
		
		protected function getDigitsOnly($number_part) {
			return str_replace(array('-', '.', ' '), '', $number_part);		
		}
		
		
	
	}