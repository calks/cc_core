<?php

	Application::loadLibrary('olmi/field');

	class coreFormElementsAddonCheckboxField extends TCheckboxField {
		
		
		public function __construct($name, $params) {
			
			parent::TCheckboxField(
				$name,
				isset($params['value']) ? $params['value'] : '',
				null,				
				isset($params['attributes']) ? $params['attributes'] : ''
			);	
		}
		
	}