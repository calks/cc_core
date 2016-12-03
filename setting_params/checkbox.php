<?php

	
	class coreCheckboxSettingParam extends coreBaseSettingParam {
		
		public function getError() {
			return null;
		}
		
		public function renderField() {
			$field = coreFormElementsLibrary::get('checkbox', $this->getFieldName());
			$field->setValue($this->param_value);
			return $field->render();	
		}
		
		public function setValueFromPost() {
			$this->param_value = false;
			$post = isset($_POST['settings']) ? $_POST['settings'] : array();
			if (!isset($post[$this->group_name])) return;
			if (!isset($post[$this->group_name][$this->param_name])) return;
			$this->param_value = true;
		}
		
				
		
	}