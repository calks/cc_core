<?php

	
	class coreCheckboxFormField extends coreBaseFormField {
		
		
		public function SetFromPost($POST) {
			$checked = Request::isFieldValueSet($this->field_name, $POST);						
			$this->SetValue($checked);			
		}
		
		
		public function render() {
			if ($this->value) {
				$this->attr('checked', 'checked');
			}
			else {
				$this->removeAttr('checked');
			}
			
			$attr_string = $this->getAttributesString();
			
			return "
				<input type=\"checkbox\" name=\"$this->field_name\" $attr_string value=\"1\" />
			";
		}
				
		
	}