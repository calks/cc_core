<?php 

		
	class corePasswordFormField extends coreTextFormField {
		
		public function __construct($field_name) {
			parent::__construct($field_name);
			$this->allowHtml();
		}
		
		public function render() {
			$this->attr(array(
				'size' => $this->size,
				'maxlength' => $this->maxlength
			));			
			$attr_string = $this->getAttributesString();
			$value = $this->getSafeAttrValue($this->value);		
			return "
				<input type=\"password\" name=\"$this->field_name\" $attr_string value=\"$value\" />
			";
		}
	}
