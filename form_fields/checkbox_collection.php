<?php

	Application::loadLibrary('olmi/field');

	class coreCheckboxCollectionFormField extends CollectionCheckBoxField {
		
		public function __construct($name, $params) {
			parent::CollectionCheckBoxField(
				$name, 
				isset($params['options']) ? $params['options'] : array(),
				isset($params['value']) ? $params['value'] : array(),
				isset($params['limiter']) ? $params['limiter'] : null,
				isset($params['mode_view']) ? $params['mode_view'] : 'horizontally'				
			);	
		}
		
	}