<?php

	class coreFormElementsAddonHiddenField extends coreFormElementsAddonBaseField {
		
		public function getAsHtml() {
			$attr_string = $this->getAttributesString();
			$value = $this->getSafeAttrValue($this->value);
			return "
				<input type=\"hidden\" name=\"$this->field_name\" $attr_string value=\"$value\" />
			";
		}
		
	}