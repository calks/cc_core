<?php

	class coreBaseForm extends coreResourceObjectLibrary {
	
		protected $fields = array();
		
		protected $action = '';
		protected $method = 'get';
		protected $heading = '';
		
		protected $buttons = array();
		

		
		public function setHeading($heading) {
			$this->heading = $heading;
		}
		
		public function setAction($action) {
			$this->action = $action;
		}

		public function setMethod($method) {
			$this->method = $method;
		}		
		
		
		public function addField(coreBaseFormField $field) {
			$field_name = $field->getFieldName();
			$this->fields[$field_name] = array(
				'field' => $field
			);
		}
				
		public function replaceField(coreBaseFormField $field) {
			$field_name = $field->getFieldName();
			if ($this->hasField($field_name)) {
				$this->fields[$field_name]['field'] = $field;				
			}
		}
		
				
		public function hasField($field_name) {
			return isset($this->fields[$field_name]);
		}
		
		public function removeField($field_name) {
			if ($this->hasField($field_name)) {
				unset($this->fields[$field_name]);
			}		
		}
		
		
		public function getField($field_name) {
			if ($this->hasField($field_name)) {
				return $this->fields[$field_name]['field'];
			}
			else {
				return null;
			}
		}
		
		public function setFieldCaption($field_name, $caption) {
			if ($this->hasField($field_name)) { 
				$this->fields[$field_name]['caption'] = $caption;
			}
		}
		
		public function setFieldComment($field_name, $comment) {
			if ($this->hasField($field_name)) {
				$this->fields[$field_name]['comment'] = $comment;
			}
		}
		
		public function setFieldUnit($field_name, $unit) {
			if ($this->hasField($field_name)) {
				$this->fields[$field_name]['unit'] = $unit;
			}			
		}
		
		
		public function makeFieldReqiured($field_name) {
			if ($this->hasField($field_name)) {
				$this->fields[$field_name]['required'] = true;
			}
		}
		
		public function makeFieldOptional($field_name) {
			if ($this->hasField($field_name)) {
				$this->fields[$field_name]['required'] = false;
			}
		}
		
		public function validate() {
			foreach ($this->fields as $field_name=>&$field_data) {
				$value = $this->getValue($field_name);
				$is_required = isset($field_data['required']) && $field_data['required'];
				
				if ($is_required && $field_data['field']->isEmpty()) {					
					$caption = isset($field_data['caption']) ? $field_data['caption'] : str_replace('_', ' ', $field_name);
					$this->setFieldError($field_name, Application::gettext("Required field \"%s\" is empty", $caption));					
				}
				elseif (!$field_data['field']->isEmpty() && $field_data['field']->isMalformed()) {
					$caption = isset($field_data['caption']) ? $field_data['caption'] : str_replace('_', ' ', $field_name);					
					$this->setFieldError($field_name, Application::gettext("Field \"%s\" contains malformed value", $caption));
				}
			}
			
		}
		
		
		public function getErrors() {
			$errors = array();
			
			foreach ($this->fields as $field_name=>$field_data) {				
				if(isset($field_data['errors']) && $field_data['errors']) {
					$errors[$field_name] = $field_data['errors'];
				}
			}
			
			return $errors;
		}
		
		
		public function clearErrors() {
			foreach ($this->fields as $field_name=>$field_data) {
				$field_data['errors'] = array();				
			}			
		}
		
		public function getValue($field_name) {
			if ($this->hasField($field_name)) {
				return $this->getField($field_name)->getValue();
			}
			else {
				return null;
			}
		}
		
		public function setValue($field_name, $value) {
			if ($this->hasField($field_name)) {
				$this->getField($field_name)->setValue($value);
			}			
		}
		
		public function render($layout_name='default') {                    	
        	            
        	$layout_template_path = $this->findEffectiveSubresourcePath('layout', $layout_name, null, 'tpl');
        	$smarty = Application::getSmarty('form_' . $this->getName());
        	
        	$smarty->assign('action', $this->action);
        	$smarty->assign('method', $this->method);
        	$smarty->assign('heading', $this->heading);
        	$smarty->assign('fields', $this->fields);
        	$smarty->assign('form', $this);
        	if (!$layout_template_path) {
        		return "There is no $layout_name layout for {$this->getName()} form";
        	}
        	
        	return $smarty->fetch($layout_template_path);		
		}
		
		
		public function renderField($field_name) {
			if ($this->hasField($field_name)) {
				return $this->getField($field_name)->getAsHtml();
			}			
			
		}
	
		
		public function loadFromRequest($request) {			
			foreach ($this->fields as $field) {
				$field['field']->SetFromPost($request);
			}
		}
		
		
		public function setValues($struct) {
			foreach ($struct as $field_name=>$value) {
				$this->setValue($field_name, $value);
			}
		}
		
		public function setFieldsOrder($order) {
			if (!$order) return;
			$ordered_fields = array();
			foreach ($order as $field_name) {
				if (!$this->hasField($field_name)) continue;
				$ordered_fields[$field_name] = $this->fields[$field_name];
				unset($this->fields[$field_name]);
			}
			
			foreach ($this->fields as $field_name=>$field_data) {
				$ordered_fields[$field_name] = $field_data;
			}
			
			$this->fields = $ordered_fields;
			
		}
		
		
		protected function setFieldError($field_name, $error) {
			if ($this->hasField($field_name)) {
				$this->fields[$field_name]['errors'][] = $error;
			}
		}
		
		
	
	}