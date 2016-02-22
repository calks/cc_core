<?php

	class coreProfileEditForm extends coreEntityEditForm {
		
		
		public function __construct() {
			$this->addField(coreFormElementsLibrary::get('text', 'first_name'));
			$this->addField(coreFormElementsLibrary::get('text', 'last_name'));
			$this->addField(coreFormElementsLibrary::get('text', 'email'));
			$this->addField(coreFormElementsLibrary::get('password', 'password'));
			$this->addField(coreFormElementsLibrary::get('password', 'password_confirmation'));
			
			$this->makeFieldReqiured('first_name');
			$this->makeFieldReqiured('last_name');
			$this->makeFieldReqiured('email');
			
			$this->setFieldCaption('first_name', $this->gettext('First Name'));
			$this->setFieldCaption('last_name', $this->gettext('Last Name'));
			$this->setFieldCaption('email', $this->gettext('Email'));
			$this->setFieldCaption('password', $this->gettext('New Password'));
			$this->setFieldCaption('password_confirmation', $this->gettext('New Password Confirmation'));
		}
		
	
	}