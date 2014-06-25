<?php

	abstract class coreSettingsAddonBaseParam  {
		
		public $application_name;
		public $param_name;
		public $param_displayed_name;
		public $group_name;
		public $group_displayed_name;
		public $param_type;
		public $param_value;		
		public $is_mandatory;
		public $constraints;
		public $seq;
		
		
		
		protected function getFieldName() {
			return 'settings' . '[' . $this->group_name . '][' . $this->param_name . ']';
		}
		
		public function getError() {
			return null;
		}
		
		public function renderField() {
			$field = coreFormElementsLibrary::get('edit', $this->getFieldName(), array(
				'size' => 80
			));
			$field->setValue($this->param_value);
			return $field->getAsHtml();	
		}
		
		public function setValueFromPost() {
			$post = isset($_POST['settings']) ? $_POST['settings'] : array();
			if (!isset($post[$this->group_name])) return;
			if (!isset($post[$this->group_name][$this->param_name])) return;
			$this->param_value = $post[$this->group_name][$this->param_name];
		}		
		
	
	}