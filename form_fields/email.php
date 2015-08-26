<?php

	class coreEmailFormField extends coreTextFormField {
	
		public function isMalformed() {
			return !email_valid($this->value);
		}
		
	}