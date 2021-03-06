<?php

	abstract class coreBaseFormField extends coreResourceObjectLibrary {
		
		protected $field_name;
		protected $value;
		protected $attributes;
		protected $css_classes;
		protected $html_allowed;
		
		public function __construct($field_name) {
			$this->field_name = $this->normalizeName($field_name);
			$this->value = null;
			$this->attributes = array();
			$this->css_classes = array();
			$this->html_allowed = false;
		}
		
		public function getFieldName() {
			return $this->field_name;
		}
		
		public function allowHtml() {
			$this->html_allowed = true;
			return $this;
		}
		
		public function disallowHtml() {
			$this->html_allowed = false;
			return $this;
		}
		
		
		public function __call($name, $arguments){			
			if (strpos($name, 'get') === 0) {
				$property_name = coreNameUtilsLibrary::camelToUnderscored(substr($name, 3));
				return $this->getProperty($property_name);	
			}
			if (strpos($name, 'set') === 0) {				
				$property_name = coreNameUtilsLibrary::camelToUnderscored(substr($name, 3));
				if (!array_key_exists(0, $arguments)) {
					throw new Exception("Property value not supplied for form element", 999);
					return $this;
				}
				$property_value = $arguments[0];  
				return $this->setProperty($property_name, $property_value);	
			}
			
			throw new Exception("no $name method", 999);
			return $this;
			
		}
		
		protected function getAccesibleProperties() {			
			$class_vars = array_keys(get_class_vars(get_class($this)));			
			return $class_vars;
		}
		
		protected function getProperty($property_name){
			$properties = $this->getAccesibleProperties();
						
			if (!in_array($property_name, $properties)) {
				throw new Exception("Property $property_name is not accessible in {$this->getName()} form element", 999);
				return $this;
			}			
			return $this->$property_name;
		}
		
		protected function setProperty($property_name, $property_value){
			$properties = $this->getAccesibleProperties();
			if (!in_array($property_name, $properties)) {
				throw new Exception("Property $property_name is not accessible in {$this->getName()} form element", 999);
				return $this;
			}
			$this->$property_name = $property_value;
			return $this;
		}
		

		abstract public function render();
		
		
		public function isEditable() {
			return true;
		}
		
		public function hasValue() {
			return true;
		}		
		
		public function GetValue() {
			return $this->value;
		}

		
		protected function sanitizeValue(&$value) {
			if (is_array($value)) {
				foreach ($value as $k=>$v) {
					$this->sanitizeValue($value[$k]);
				}
			}
			elseif (is_object($value)) {
				foreach ($value as $k=>&$v) {
					$this->sanitizeValue($value->$k);
				}
			}
			else {
				if (!$this->html_allowed) {				
					$value = strip_tags($value);
				}
			}
		}
		
		public function SetValue($value) {
			$this->sanitizeValue($value);			
			$this->value = $value;
			return $this;
		}
		
		public function SetFromPost($POST) {						
			$value = coreRequestLibrary::getFieldValue($this->field_name, $POST);
			$this->SetValue($value);
		}
				
		protected function normalizeName($attr_name) {
			return strtolower(trim($attr_name));
		}
		
		public function attr() {			
			$args = func_get_args();			
			$args_count = count($args);
			
			if ($args_count < 1 || $args_count > 2) return $this;
			
			if ($args_count == 1) {
				if (!isset($args[0])) return $this;				
				if (!is_array($args[0])) return $this;
				foreach ($args[0] as $attr_name=>$attr_value) {
					$attr_name = $this->normalizeName($attr_name);
					$this->attributes[$attr_name] = $attr_value;
				}
			}
			elseif($args_count == 2) {				
				$attr_name = $this->normalizeName($args[0]);
				$attr_value = $args[1];
				$this->attributes[$attr_name] = $attr_value;				
			}
			
			return $this;
		}
		
		public function removeAttr($attr_name) {
			$attr_name = $this->normalizeName($attr_name);
			if (isset($this->attributes[$attr_name])) {
				unset($this->attributes[$attr_name]);
			}
			return $this;
		}
		
		public function addClass($css_class) {
			$css_class = $this->normalizeName($css_class);
			$this->css_classes[$css_class] = $css_class;
			return $this;
		}
		

		public function removeClass($css_class) {
			$css_class = $this->normalizeName($css_class);
			if (isset($this->css_classes[$css_class])) {
				unset($this->css_classes[$css_class]);
			}
			return $this;
		}
		
		protected function getSafeAttrValue($attr_value) {
			return htmlentities($attr_value, ENT_COMPAT, 'UTF-8');
		}
		
		protected function getAttributesString() {
			$out = array();			
			foreach ($this->attributes as $attr_name=>$attr_value) {				
				$attr_value_safe = $this->getSafeAttrValue($attr_value);				
				$out[] = "$attr_name=\"$attr_value_safe\"";
			}
			
			if ($this->css_classes) {
				$css_classes = implode(' ', $this->css_classes);
				$out[] = "class=\"$css_classes\"";
			}
			
			return implode(' ', $out);
		}
				
		public function isEmpty() {
			return !$this->value;
		}
				
		public function isMalformed() {
			return false;
		}
		
		public function getFormatErrors() {
			return array();
		}
		
		
	}