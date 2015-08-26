<?php

	class coreFixedValueFormField extends coreBaseFormField {
	
				
		public function SetValue($value) {			
			return $this;
		}
		
		
		public function SetFixedValue($value) {
			parent::SetValue($value);			
			return $this;
		}
		
		
		public function getAsHtml() {
			$attr_string = $this->getAttributesString();			
			return "
				<span $attr_string>$this->value</span>
			";
		}
		
		
	
	}