<?php
	
	class coreFormElementsAddonDatetimeField extends coreFormElementsAddonBaseField {
	
		protected $date_field;
		protected $time_field;
		
		
		public function __construct($name) {
			parent::__construct($name);
			$this->date_field = coreFormElementsLibrary::get('date', $name . '_date');
			$this->time_field = coreFormElementsLibrary::get('time', $name . '_time');
		}		
		
		public function getAsHtml() {			
			return $this->date_field->getAsHtml() . ' в ' . $this->time_field->getAsHtml();		
		}
		
		
		public function SetFromPost($POST) {
			$this->date_field->setFromPost($POST);
			$this->time_field->setFromPost($POST);
			
			$date = coreFormattingLibrary::dateRussianToMysql($this->date_field->getValue());
			$time = $this->time_field->getValue();
			
			$this->value = trim("$date $time");		
			
		}
		
		
		public function setValue($value) {
			$parts = explode(' ', $value);
			foreach ($parts as $p) {
				if (strpos($p, ':') !== false) {
					$this->time_field->setValue($p);
				}
				else {
					$this->date_field->setValue(coreFormattingLibrary::dateMysqlToRussian($p));
				}
			}
		}
		
	
	}