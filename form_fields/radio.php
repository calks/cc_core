<?php

	Application::loadLibrary('olmi/field');

	class coreRadioFormField extends TRadioField {
		
		public function __construct($name, $params) {
			parent::TRadioField(
				$name,
				isset($params['value']) ? $params['value'] : array(), 
				isset($params['options']) ? $params['options'] : array(),
				isset($params['attributes']) ? $params['attributes'] : ''
			);	
		}
		
	}