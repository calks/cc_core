<?php

	class coreProfileEditForm extends coreEntityEditForm {
		
		
		public function __construct() {
			$this->addField(coreFormElementsLibrary::get('text', 'first_name'));
			$this->addField(coreFormElementsLibrary::get('text', 'last_name'));
			$this->addField(coreFormElementsLibrary::get('text', 'email'));
			$this->addField(coreFormElementsLibrary::get('password', 'new_pass'));
			$this->addField(coreFormElementsLibrary::get('password', 'new_pass_confirmation'));
			
			$this->makeFieldReqiured('first_name');
			$this->makeFieldReqiured('last_name');
			$this->makeFieldReqiured('email');
			
			$this->setFieldCaption('first_name', $this->gettext('First Name'));
			$this->setFieldCaption('last_name', $this->gettext('Last Name'));
			$this->setFieldCaption('email', $this->gettext('Email'));
			$this->setFieldCaption('new_pass', $this->gettext('New Password'));
			$this->setFieldCaption('new_pass_confirmation', $this->gettext('New Password Confirmation'));
		}
		
		
		public function validate() {
			parent::validate();
			
			$password = $this->getValue('new_pass');
			$password_confirmation = $this->getValue('new_pass_confirmation');
			
			if ($password != $password_confirmation) {
				$this->fields['new_pass']['errors'][] = $this->gettext("Password does not match Password Confirmation");
			}
		
		}
		
	
	}