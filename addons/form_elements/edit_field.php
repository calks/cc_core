<?php

	Application::loadLibrary('olmi/field');

	class coreFormElementsAddonEditField extends TEditField {
		
		
		protected function addClasses(&$params, $classes) {
			$attributes = isset($params['attributes']) ? $params['attributes'] : '';
			
			if (strpos($attributes, 'class=') !== false) {
				$attributes = preg_replace('/(class=)(\'|")/', 'class=$2'.$classes.' ', $attributes);				
			} 
			else {
				$attributes .= ' class="'.$classes.'"';
			}
			
			$params['attributes'] = $attributes;
			
		}
		
		
		public function __construct($name, $params) {
			
			parent::TEditField(
				$name,
				isset($params['value']) ? $params['value'] : '',
				isset($params['size']) ? $params['size'] : 30,
				isset($params['maxlength']) ? $params['maxlength'] : 255,
				isset($params['attributes']) ? $params['attributes'] : ''
			);	
		}
		
	}