<?php

	class coreSelectFormField extends coreBaseFormField {
		
		protected $options;
		
		public function __construct($name) {
			parent::__construct($name);
			$this->options = array();
		}
		
		public function getAsHtml() {
			$attr_string = $this->getAttributesString();
			
			$out = "<select name=\"$this->field_name\" $attr_string>";
			foreach ($this->options as $value=>$caption) {
				$selected = $value==$this->value ? 'selected="selected"' : '';
				$value = $this->getSafeAttrValue($value);
				$out .= "<option $selected value=\"$value\">$caption</option>";	
			}
			$out .= "</select>";
			
			return $out;
		}
		
		
	}