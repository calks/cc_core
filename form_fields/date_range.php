<?php

	class coreDateRangeFormField extends coreBaseFormField {
	
		protected $date_from_field;
		protected $date_to_field;
		
		
		public function __construct($field_name) {
			$this->date_from_field = coreFormElementsLibrary::get('date', $field_name . '[from]');
			$this->date_to_field = coreFormElementsLibrary::get('date', $field_name . '[to]');
			parent::__construct($field_name);
		}
		
		public function SetValue($value) {
			$from_mysql = isset($value['from']) ? $value['from'] : null;			
			$this->date_from_field->setValue($from_mysql);
			
			$to_mysql = isset($value['to']) ? $value['to'] : null;
			$this->date_to_field->setValue($to_mysql);
			
			return $this;
		}
		
		public function GetValue() {
			return array(
				'from' => $this->date_from_field->getValue(),
				'to' => $this->date_to_field->getValue()
			);
		}
		
		public function SetFromPost($POST) {

			$this->date_from_field->SetFromPost($POST);
			$this->date_to_field->SetFromPost($POST);
			$this->value = array(
				'from' => $this->date_from_field->getValue(),
				'to' => $this->date_to_field->getValue()
			);
		}		
		
		public function __call($name, $arguments){			
			call_user_func_array(array($this->date_from_field, $name), $param_arr);
			call_user_func_array(array($this->date_to_field, $name), $param_arr);
			return parent::__call($name, $arguments);			
		}
		
		public function getAsHtml() {
			return $this->render();
		}
		
		public function render() {
			
			$from_field_html = $this->date_from_field->render();
			$to_field_html = $this->date_to_field->render();
			
			$this->addClass('date-range-field');
			$attr_string = $this->getAttributesString();
			
			$from = $this->gettext('from');
			$to = $this->gettext('to');
			
			return "
				<span $attr_string>$from $from_field_html $to $to_field_html</span>
			";
			
		}
		
	
	}