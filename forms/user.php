<?php

	class coreUserForm extends coreEntityEditForm {
		
		public function validate() {
			parent::validate();
			
			$email = $this->getValue('email');
			$user_id = $this->getValue('id');
			$user = Application::getEntityInstance('user');
			
			if ($email) {
				$existing_user_id = (int)$user->getIdByEmail($email);
				if ($existing_user_id && $existing_user_id != $user_id) {
					$this->setFieldError('email', $this->gettext('Email is used by another user'));
				}
			}
		}
		
	}