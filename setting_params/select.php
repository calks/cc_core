<?php

	
	class coreSelectSettingParam extends coreBaseSettingParam {
		
		public function getError() {
			return null;
		}
		
		public function renderField() {
			$field = coreFormElementsLibrary::get('select', $this->getFieldName());
			$field->setOptions($this->constraints['options']);
			$field->setValue($this->param_value);
			return $field->getAsHtml();	
		}
		
		
	}