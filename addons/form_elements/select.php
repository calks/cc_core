<?php

	Application::loadLibrary('olmi/field');

	class coreFormElementsAddonSelectField extends TSelectField {
		
		public function __construct($name, $params) {
			parent::TSelectField(
				$name,
				'',
				isset($params['options']) ? $params['options'] : array(),
				isset($params['attributes']) ? $params['attributes'] : ''
			);	
		}
		
	}