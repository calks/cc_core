<?php

	Application::loadLibrary('olmi/field');

	class coreFormElementsAddonEditField extends coreFormElementsAddonBaseField {
		
		protected $size = 30;
		protected $maxlength = 255;
		
		protected function addClasses(&$params, $classes) {
			$classes = explode(' ', $classes);			
			foreach ($classes as $class) $this->addClass($class);
		}
		
		public function getAsHtml() {
			$this->attr(array(
				'size' => $this->size,
				'maxlength' => $this->maxlength
			));
			$attr_string = $this->getAttributesString();
			$value = $this->getSafeAttrValue($this->value);
			return "
				<input type=\"text\" name=\"$this->field_name\" $attr_string value=\"$value\" />
			";
		}
				
		protected function processLegacyParams($params) {
			if (isset($params['value'])) $this->setValue($params['value']);
			if (isset($params['maxlength'])) $ths->setMaxlength($params['maxlength']);
			if (isset($params['size'])) $this->setSize($params['size']);
			if (isset($params['attributes'])) {
				$attributes = $params['attributes'];
				preg_match_all('/(?P<full_string>(?P<attr_name>[a-z0-9\-_]+)\s?=\s?(\'|")(?P<attr_value>.*)(\'|"))/isU', $attributes, $matches, PREG_SET_ORDER);								
				foreach ($matches as $m) {
					$attr_name = $this->normalizeName($m['attr_name']);
					$attr_value = $m['attr_value'];
					if ($attr_name == 'class') $this->addClasses($params, $attr_value);
					else $this->attr($attr_name, $attr_value);
				}
			}
		}
		
		public function __construct($name, $params) {
			
			parent::__construct($name);
			$this->processLegacyParams($params);
		}
		
	}