<?php

	abstract class coreBaseSettingParam  {
		
		public $application_name;
		public $param_name;
		public $param_displayed_name;
		public $param_displayed_unit;
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
		
		protected function getField() {
			return coreFormElementsLibrary::get('text', $this->getFieldName());
		}
		
		public function renderField() {			
			$field = $this->getField(); 
			
			if (isset($this->constraints['field_params'])) {			
				foreach ($this->constraints['field_params'] as $k=>$v) {
					$setter = coreNameUtilsLibrary::underscoredToCamel("set_$k");  
					$field->$setter($v);					
				}				
				$field->attr($this->constraints['field_attr']);
			}
			$field->setValue($this->param_value);
			$out = $field->getAsHtml();
			if ($this->param_displayed_unit) $out .= " $this->param_displayed_unit"; 

			return $out;
		}
		
		public function setValueFromPost() {
			$post = isset($_POST['settings']) ? $_POST['settings'] : array();
			if (!isset($post[$this->group_name])) return;
			if (!isset($post[$this->group_name][$this->param_name])) return;
			$this->param_value = $post[$this->group_name][$this->param_name];
		}		
		
	
	}