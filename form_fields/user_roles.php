<?php

	class coreUserRolesFormField extends coreCheckboxCollectionFormField {
		
		
		public function __construct($field_name) {
			parent::__construct($field_name);
			$user = Application::getEntityInstance('user');
			$this->setOptions($user->getRoleSelect());
		}
		
		public function SetValue($value) {
			if (is_array($value)) {
				$value = array_keys($value);
			}
			else {
				$value = array();
			}
			return parent::SetValue($value);
		}
		
		public function GetValue() {
			$value = parent::GetValue();
			$out = array();
			
			$options_reloaded = false;
			
			foreach ($value as $v) {
				if (!isset($this->options[$v]) && !$options_reloaded) {
					$user = Application::getEntityInstance('user');
					$this->setOptions($user->getRoleSelect());
					$options_reloaded = true;
				}
				$caption = isset($this->options[$v]) ? $this->options[$v] : $this->options[$v];
				$out[$v] = $caption; 
			}
			
			return $out;
		}
		
		
	}