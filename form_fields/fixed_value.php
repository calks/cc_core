<?php

	class coreFixedValueFormField extends coreBaseFormField {
	
		
		
		public function SetValue($value) {			
			return $this;
		}
		
		
		public function getAsHtml() {
			$attr_string = $this->getAttributesString();			
			return "
				<span $attr_string>$value</span>
			";
		}
		
		
	
	}