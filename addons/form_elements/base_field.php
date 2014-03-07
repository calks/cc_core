<?php

	abstract class coreFormElementsAddonBaseField {
		
		protected $field_name;
		protected $value;
		protected $attributes;
		protected $css_classes;
		
		public function __construct($name) {
			$this->field_name = $name;
			$this->value = null;
			$this->attributes = array();
			$this->css_classes = array();
		}
		
		
		public function getName() {
			$class_name = get_class($this);
			preg_match('/.*Addon(?P<name>.*)Field/U', $class_name, $matches);
			return coreNameUtilsLibrary::camelToUnderscored($matches['name']);
		}
		
		
		public function __call($name, $arguments){			
			if (strpos($name, 'get') === 0) {
				$property_name = coreNameUtilsLibrary::camelToUnderscored(substr($name, 3));
				return $this->getProperty($property_name);	
			}
			if (strpos($name, 'set') === 0) {
				$property_name = coreNameUtilsLibrary::camelToUnderscored(substr($name, 3));
				if (!isset($arguments[0])) {
					throw new Exception("Property value not supplied for form element", 999);
					return $this;
				}
				$property_value = $arguments[0];  
				return $this->setProperty($property_name, $property_value);	
			}
			
			throw new Exception("no method", 999);
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
		

		abstract public function getAsHtml();
		
		
	
	}