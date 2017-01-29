<?php

	class coreEmailSettingParam extends coreBaseSettingParam {
		
		
		public function getError() {	
			if ($this->param_value) {
				if (!email_valid($this->param_value)) return "Email в неправильном формате";
			}			
		}
		
		/*public function setValueFromPost() {
			parent::setValueFromPost();
			$this->param_value = $this->param_value=='' ? null : (int)$this->param_value;
		}*/
		
		
		
	}