<?php

	require_once 'base.php';
	require_once JPATH_ROOT . '/administrator/components/com_booking/classes/students.php';
	
	class emailListSettingsField extends settingsField {
		
		public function check() {
			if (!$this->is_mandatory && !$this->value) return;
			$errors = array();
			$emails = explode(',', $this->value);
			foreach($emails as $email) {
				$email = trim($email);
				if (!$email) $errors[] = "пустое значение";
				elseif (!StudentsBookingDataObject::emailValid($email)) $errors[] = "&laquo;$email&raquo; - неправильный Email";
			}
			
			return $errors;
		}
		
		public function render() {
			echo BookingFormHelper::textareaField($this->name, $this->value);
		}
		
		
	}
