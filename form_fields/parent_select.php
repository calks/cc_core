<?php

	class coreParentSelectFormField extends coreSelectFormField {
		
		public function GetAsHTML() {
			
			$attr_string = $this->getAttributesString();
			
			$out = "<select name=\"$this->field_name\" $attr_string>";
			
			foreach ($this->options as $value=>$caption) {
				$selected = $value==$this->value ? 'selected="selected"' : '';
				$value = $this->getSafeAttrValue($value);
				$replace = array(
					'[space]' => '&nbsp;',
					'[disabled]' => ''
				);
				$caption = str_replace(array_keys($replace), $replace, $caption);
				$out .= "<option $selected value=\"$value\">$caption</option>";	
			}
			
			$out .= "</select>";
			return $out;
		}			
	}