<?php

	Application::loadLibrary('olmi/field');

	class coreTextareaFormField extends coreBaseFormField {
		
		
		
		public function getAsHtml() {
			$attr_string = $this->getAttributesString();
			return "
				<textarea name=\"$this->field_name\" $attr_string>$this->value</textarea>
			";
		}
		
		public function __construct($name, $params) {
			
			parent::__construct($name);	
		}
		
	}