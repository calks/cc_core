<?php

	Application::loadLibrary('olmi/field');

	class coreFormElementsAddonCheckboxCollectionField extends CollectionCheckBoxField {
		
		public function __construct($name, $params) {
			parent::CollectionCheckBoxField(
				$name, 
				isset($params['options']) ? $params['options'] : array(),
				isset($params['value']) ? $params['value'] : array()
			);	
		}
		
	}