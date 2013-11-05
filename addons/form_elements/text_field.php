<?php

	Application::loadLibrary('olmi/field');

	class coreFormElementsAddonTextField extends TTextField {
		
		
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
			
			parent::TTextField(
				$name,
				isset($params['value']) ? $params['value'] : '',
				isset($params['cols']) ? $params['cols'] : 80,
				isset($params['rows']) ? $params['rows'] : 4,
				isset($params['attributes']) ? $params['attributes'] : ''
			);	
		}
		
	}