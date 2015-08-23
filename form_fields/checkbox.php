<?php

	
	class coreCheckboxFormField extends coreBaseFormField {
		
		
		public function SetFromPost($POST) {
			$checked = isset($POST[$this->field_name]) ? true : false;						
			$this->SetValue($checked);			
		}
		
		
		public function getAsHtml() {
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