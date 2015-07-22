<?php

	abstract class coreBaseTranslation {
		
		
		public function getPluralIndex($n) {
			switch (CURRENT_LANGUAGE) {
				case LANGUAGES_RUSSIAN:
					switch(abs($n%10)) {
						case 1:
							return 0;
							break;
						case 2:
						case 3:
						case 4:
							return 1;
							break;						
						default:
							return 2;
							break;
					}						
				break;
				default:
					return abs($n) == 1 ? 0 : 1;
			} 
		}
	
		abstract public function getTranslations();
				
	
	}