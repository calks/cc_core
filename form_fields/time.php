<?php

	class coreTimeFormField extends coreTextFormField {
	
		public function __construct($name, $params) {
			$this->setSize(10);
			parent::__construct($name, $params);
			$this->attr('pattern', '([0-1]?\d|2[0-3]):[0-5]\d');						
			$this->attr('placeholder', 'hh:mm');
		}
		
		
		public function SetFromPost($POST) {
			$raw_value = isset($_POST[$this->field_name]) ? $_POST[$this->field_name] : null;
			if (preg_match('/^(?P<hours>([0-1]?\d|2[0-3])):(?P<minutes>[0-5]\d)$/', $raw_value, $matches)) {				
				$hours = str_pad((int)$matches['hours'], 2, '0', STR_PAD_LEFT);
				$minutes = str_pad((int)$matches['minutes'], 2, '0', STR_PAD_LEFT);
				$this->setValue("$hours:$minutes");
			}
			else {
				$this->setValue(null);
			}
		}
		
		
		public function setValue($value) {
			$value_unix = @strtotime($value);			
			$this->value = $value_unix ? date("H:i", $value_unix) : null;
		}
	
	}