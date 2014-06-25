<?php

	
	class coreSettingsAddonSelectParam extends coreSettingsAddonBaseParam {
		
		public function getError() {
			return null;
		}
		
		public function renderField() {
			$field = coreFormElementsLibrary::get('select', $this->getFieldName(), array(
				'options' => $this->constraints['options']
			));
			$field->setValue($this->param_value);
			return $field->getAsHtml();	
		}
		
		
	}