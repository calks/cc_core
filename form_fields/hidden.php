<?php

	class coreHiddenFormField extends coreBaseFormField {
		
		public function render() {
			$attr_string = $this->getAttributesString();
			$value = $this->getSafeAttrValue($this->value);
			return "
				<input type=\"hidden\" name=\"$this->field_name\" $attr_string value=\"$value\" />
			";
		}
		
	}