<?php

	class coreFormattingLibrary {
		
		public static function getNounForNumber($number, $one, $two, $five=null) {			
			$number = (int)$number;
			
			if (!$five) $five = $two;
			
			$number_str = (string)$number;
			$last_digits = strlen($number_str) <= 2 ? $number_str : substr($number_str, strlen($number_str) - 2);   
			$last_digits = (int)$last_digits;
			
			if ($last_digits > 10 && $last_digits < 20) {				
				return $five;
			}
			else {				
				switch(abs($number%10)) {
					case 1:
						return $one;
						break;
					case 2:
					case 3:
					case 4:
						return $two;
						break;						
					default:
						return $five;
						break;
				}
			}
		}
		
		public static function getWordForGender($gender, $male, $female, $neuter=null) {
			if (!$neuter) $neuter = $male;
			if ($gender == 'male') return $male;
			elseif($gender == 'female') return $female;
			else return $neuter; 
		}
		
		public static function formatDate($date, $include_time=true, $skip_this_year=true, $use_relative_names=true) {
			if (!$date || in_array($date, array('0000-00-00', '0000-00-00 00:00:00'))) {
				return 'никогда';
			}
			
			$month_list = array(
				1 => 'января',
				2 => 'февраля',
				3 => 'марта',
				4 => 'апреля',
				5 => 'мая',
				6 => 'июня',
				7 => 'июля',
				8 => 'августа',
				9 => 'сентября',
				10 => 'октября',
				11 => 'ноября',
				12 => 'декабря'
			);
			
			$date_unix = strtotime($date);
			
			$day = date("d", $date_unix);
			$month = (int)date("m", $date_unix); 
			$year = (int)date("Y", $date_unix);
			
			$out = '';
			
			if ($use_relative_names && date('d.m.Y', $date_unix) == date('d.m.Y')) {
				$out = 'сегодня';
			}
			elseif ($use_relative_names && date('d.m.Y', $date_unix) == date('d.m.Y', strtotime('-1 day'))) {
				$out = 'вчера';
			}
			elseif ($use_relative_names && date('d.m.Y', $date_unix) == date('d.m.Y', strtotime('+1 day'))) {
				$out = 'завтра';
			}			
			else {
				$out = $day . ' ' . $month_list[$month];
				if (!$skip_this_year || $year != date("Y")) $out .= ' ' . $year .'г.';
			}
			
			if ($include_time) {
				$out .= ' в ' . date('H:i', $date_unix);
			}
			
			return $out;
		}
		
		
		public static function formatCurrency($amount, $preset_name='default') {
			$formatters = coreResourceLibrary::findEffective('formatter', 'currency');
			$formatter = $formatters ? array_shift($formatters) : null;
			if (!$formatter) return $amount;
			
			$formatter = new $formatter->class();
			return $formatter->format($amount, $preset_name);
		}
		
		
		public static function formatPhone($phone, $preset_name='default') {
			$formatters = coreResourceLibrary::findEffective('formatter', 'phone');
			$formatter = $formatters ? array_shift($formatters) : null;
			if (!$formatter) return $phone;
			
			$formatter = new $formatter->class();
			return $formatter->format($phone, $preset_name);
		}
		
		
		
		public static function dateMysqlToRussian($mysql_date, $default=null) {
			return preg_replace('/(\d+)-(\d+)-(\d+)/', '$3.$2.$1', $mysql_date);
		}
		
		
		public static function dateFromMysql($mysql_date, $format) {
			if(!preg_match("/(?P<year>\d{4})-(?P<month>\d{2})-(?P<day>\d{2})/", $mysql_date, $matches)) return null;
			$replacements = array(				
				'yy' => $matches['year'],
				'Y' => $matches['year'],				
				'mm' => $matches['month'],
				'm' => $matches['month'],				
				'dd' => $matches['day'],	
				'd' => $matches['day']		
			);
			return str_replace(array_keys($replacements), $replacements, $format);
		}
		
		public static function dateToMysql($date, $format) {
			$format_replacements = array(
				'mm' => 'm',
				'yy' => 'Y',
				'dd' => 'd'
			);
			
			$format = str_replace(array_keys($format_replacements), $format_replacements, $format);
			
			$regex_replacements = array(
				'd' => '(?<day>\d{1,2})',	
				'Y' => '(?<year>\d{4})',				
				'm' => '(?<month>\d{1,2})',
				'.' => '\.',
				'/' => '\/',
				' ' => '\s'
			);
			
			$regex = '/' . str_replace(array_keys($regex_replacements), $regex_replacements, $format) . '/';
			
			if (!preg_match($regex, $date, $matches)) return null;
			$year = $matches['year'];
			$month = str_pad($matches['month'], 2, '0', STR_PAD_LEFT);
			$day = str_pad($matches['day'], 2, '0', STR_PAD_LEFT);
			
			return "$year-$month-$day";
			
		}
		
		
		public static function dateRussianToMysql($russian_date, $default=null) {
			$russian_date = trim($russian_date);
			if (!$russian_date) return $default;
			preg_match('/^(?P<day>\d{1,2})\.(?P<month>\d{1,2})\.(?P<year>\d{4})$/is', $russian_date, $matches);
			
			
			$day = isset($matches['day']) ? $matches['day'] : null;
			$month = isset($matches['month']) ? $matches['month'] : null;
			$year = isset($matches['year']) ? $matches['year'] : null;
			
			if (!$day || !$month || !$year) return $default;
			
			$day = str_pad($day, 2, '0', STR_PAD_LEFT);
			$month = str_pad($month, 2, '0', STR_PAD_LEFT);
			
			return "$year-$month-$day";
		}
		
		public static function secondsToDaysHoursMinutes($time_in_seconds) {
			$time_in_minutes = floor(abs($time_in_seconds)/60);

			$minutes = $time_in_minutes % 60;
			 
			$time_in_hours = floor($time_in_minutes/60);			
			$hours = $time_in_hours % 24;
			//echo $hours; die();
			
			$days = floor($time_in_hours/24);
			
			if (!$minutes && !$hours && !$days) {
				return 'меньше 1 минуты';
			}
			
			$out = array();
			if ($days) {
				$out[] = $days . ' ' . self::getNounForNumber($days, 'день', 'дня', 'дней');
			}
			if ($hours) {
				$out[] = $hours . ' ' . self::getNounForNumber($hours, 'час', 'часа', 'часов');
			}
			if ($minutes) {
				$out[] = $minutes . ' ' . self::getNounForNumber($minutes, 'минута', 'минуты', 'минут');
			}
			
			return implode(' ', $out);			
		}
		
		
		public static function plaintext($str, $preserve_linebreaks=false) {
			$linebreak_marker = '%'.md5(uniqid()).'%';
			$str = strip_tags($str);
			$str = str_replace(array("\r\n", "\n", "\r"), $linebreak_marker, $str);
			$str = preg_replace('/\s+/', ' ', $str);
			$str = str_replace($linebreak_marker, $preserve_linebreaks ? "\n" : ' ', $str);
			return $str;			
		}
				
		public static function truncate($str, $max_length, $keep_whole_words=true, $addition='...') {
			$str = trim(self::plaintext($str));
			if (mb_strlen($str) <= $max_length) return $str;
			if (!$max_length) return $str;
			
			if (!$keep_whole_words) {
				return mb_substr($str, 0, $max_length) . $addition;
			}
			
			$words = explode(" ", $str);
			$out = array();
			$out_length = 0;			
			
			while (($word = trim(@array_shift($words))) && ($out_length + 1 + mb_strlen($word) <= $max_length)) {
				$out[] = $word;
				$out_length += mb_strlen($word)+1;
			}

			return implode(' ', $out) . ' ' . $addition;
		}
		
		
		public static function removePunctuation($str) {
			$remove = array(
				'.', 
				',',
				';',
				':', 
				'+', 
				'(', 
				')',
				'`',
				'~',
				'%',
				'№',
				'#',
				'\\',
				'°',
				'«',
				'»',
				'"',
				"'",
				"!",
				"?"
			);
			$str = str_replace($remove, '', $str);							
			return $str;			
		}
	
		
		public static function transliterate($str, $reverse=false) {
    	    $one_letter = array(
        	    "А"=>"A","Б"=>"B","В"=>"V","Г"=>"G",
            	"Д"=>"D","Е"=>"E","Ё"=>"E","Ж"=>"J","З"=>"Z","И"=>"I",
	            "Й"=>"Y","К"=>"K","Л"=>"L","М"=>"M","Н"=>"N",
    	        "О"=>"O","П"=>"P","Р"=>"R","С"=>"S","Т"=>"T",
        	    "У"=>"U","Ф"=>"F","Х"=>"H",
	            "Э"=>"E","а"=>"a","б"=>"b",
    	        "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ё"=>"e","ж"=>"j",
        	    "з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
            	"м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
	            "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
    	        "ъ"=>"y","э"=>"e"
	        );

	        $multiple_letters = array(
				"Ц"=>"TS","Ч"=>"CH","Ш"=>"SH","Щ"=>"SCH","Ы"=>"YI",
	            "Ю"=>"YU","Я"=>"YA","ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch",
        	    "ы"=>"yi","ю"=>"yu","я"=>"ya"
	        );
	        
	        $european_letters = array(
	        	'ї'=>'e', 'і'=>'i', 'І'=>'I', 'є'=>'e', 'Є'=>'E'
	        );
			
	        $empty_letters = array(
	        	"Ъ"=>"","Ь"=>"","ь"=>""
	        );
	        
	        if ($reverse) {	        	
	        	$str = strtr($str,array_flip($one_letter));
	        	return strtr($str,array_flip($one_letter));	        	
	        }
	        else {
	        	$tr = array_merge($one_letter, $multiple_letters, $empty_letters, $european_letters);
	        	return strtr($str,$tr);
	        }
		}
		
		
		public static function getUrlSafeString($string) {
			$string = self::removePunctuation($string);
			$string = self::transliterate($string);
			$string = strtolower($string);
			$string = trim(preg_replace("/\s+/", '-', $string));
			return $string;
		}		
		
	}
	
	