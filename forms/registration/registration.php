<?php

	class coreRegistrationForm extends coreBaseForm {
	
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
			$this->setFieldCaption('password', $this->gettext('Password'));
			$this->setFieldCaption('password_confirmation', $this->gettext('Password Confirmation'));
		}
		
		
		protected function isEmailVacant($email) {
			$user = Application::getEntityInstance('user');
			return (int)$user->getIdByEmail($email) == 0;
		}
		
		
		public function validate() {
			parent::validate();
			
			$email = $this->getValue('email');
			if ($email) {				
				if (!email_valid($email)) {
					$this->fields['email']['errors'][] = $this->gettext('Email is malformed');
				}
				elseif (!$this->isEmailVacant($email)) {
					$this->fields['email']['errors'][] = $this->gettext('Email is used by another user');
				}
			}
			
			$pass = $this->getValue('password');
			$pass_confirmation = $this->getValue('password_confirmation');
			
			if (!$pass) {
				$this->fields['password']['errors'][] = $this->gettext("You have not specified password");
			}
			elseif ($pass !== $pass_confirmation) {
				$this->fields['password']['errors'][] = $this->gettext('Password does not match with confirmation');
			}
		
		}
		
		public function render($layout_name='default') {
			$smarty = Application::getSmarty($layout_name);
			$login_link = '/login';
			$smarty->assign('login_link', Application::getSeoUrl($login_link));
			return parent::render($layout_name);
		}
		
		
		
		
		
		
	}