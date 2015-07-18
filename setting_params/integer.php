<?php

	
	class coreSettingsAddonIntegerParam extends coreSettingsAddonBaseParam {
		
		public function getError() {	
			if (!is_null($this->param_value)) {		 
				if(isset($this->constraints['min']) && $this->param_value < $this->constraints['min']) {
					return "Значение не должно быть меньше " . $this->constraints['min'];
				}
				if(isset($this->constraints['max']) && $this->param_value > $this->constraints['max']) {
					return "Значение не должно быть больше " . $this->constraints['max'];
				}
			}			
		}
		
		public function setValueFromPost() {
			parent::setValueFromPost();
			$this->param_value = $this->param_value=='' ? null : (int)$this->param_value;
		}
		
	}