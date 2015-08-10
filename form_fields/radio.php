<?php

	class coreRadioFormField extends coreBaseFormField {
		
		protected $options;
		
		public function __construct($name) {
			parent::__construct($name);
			$this->options = array();
		}
		
		
		public function getAsHtml() {
			
			$this->addClass('input-group');
			$attr_string = $this->getAttributesString();
			
			$out .= "<span $attr_string>";
						
			foreach ($this->options as $value=>$caption) {
				$checked = $value==$this->value ? 'checked="checked"' : '';
				$value = $this->getSafeAttrValue($value);
				$out .= "<span class=\"option\"><input type=\"radio\" name=\"$this->field_name\" $checked value=\"$value\">&nbsp;$caption<br></span>";	
			}
			$out .= "</span>";
			
			return $out;
		}

		
	}