<?php

	
	
	class coreSettingsAddonStringParam extends coreSettingsAddonBaseParam {
		
		public function check() {			 
			return array();
		}
		
		
		public function render() {
			echo BookingFormHelper::editField($this->name, $this->value, 100, 255);
		}
		
		
	}