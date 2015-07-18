<?php

	require_once 'base.php';
	require_once JPATH_ROOT . '/administrator/components/com_booking/classes/students.php';
	
	class emailSettingsField extends settingsField {
		
		public function check() {
			if (!$this->is_mandatory && !$this->value) return;
			if (!$this->value) $errors[] = "пустое значение";
			elseif (!StudentsBookingDataObject::emailValid($this->value)) $errors[] = "неправильный Email";
					
			return $errors;
		}
		
		public function render() {
			echo BookingFormHelper::editField($this->name, $this->value, 80, 255);
		}
		
		
	}