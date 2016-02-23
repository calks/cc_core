<?php

	class coreEntityEditForm extends coreBaseForm {
		
		public function initWithEntityFields(coreBaseEntity $entity) {
			$entity_fields = $this->getFieldList($entity);
			$entity_field_properties = $entity->getFieldProperties();

			foreach ($entity_fields as $field_name) {
				if (substr($field_name, 0, 1) === '_') continue;
				if (!isset($entity_field_properties[$field_name])) continue;								
				$field_properties = $entity_field_properties[$field_name];				
				$this->initField($field_name, $entity->$field_name, $field_properties);				
			}		
		}
		
		
		protected function initField($field_name, $field_value, $field_properties) {
			$caption = isset($field_properties['caption']) ? $field_properties['caption'] : str_replace('_', ' ', $field_name);
			$type = isset($field_properties['type']) ? $field_properties['type'] : 'text';
			$required = isset($field_properties['required']) ? $field_properties['required'] : false;

			$field = coreFormElementsLibrary::get($type, $field_name);				
			$field->setValue($field_value);
			
			$init = isset($field_properties['init']) ? $field_properties['init'] : array();
			foreach ($init as $method => $params) {
				$method = coreNameUtilsLibrary::underscoredToCamel($method);					
				call_user_func(array($field, $method), $params);
			}
			
			
			$this->addField($field);
			$this->setFieldCaption($field_name, $caption);
			if ($required) $this->makeFieldReqiured($field_name);		
		}
		
		public function updateEntity(coreBaseEntity $entity) {
			$entity_fields = $this->getFieldList($entity);
			
			foreach ($entity_fields as $field_name) {				
				if ($this->hasField($field_name)) {					
					$field_is_fixed = $this->getField($field_name)->getResourceName() == 'fixed_value';
					if ($field_is_fixed) continue;
					$entity->$field_name = $this->getValue($field_name);
				}
			}
		}
		
		public function updateObject($object) {
			return $this->updateEntity($object);
		}
		
		
		protected function getFieldList($entity) {
			return $entity->getFields();		
		}
		
	
	}