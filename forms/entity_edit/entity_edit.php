<?php

	class coreEntityEditForm extends coreBaseForm {
		
		public function initWithEntityFields(coreBaseEntity $entity) {
			$entity_fields = $entity->getFields();
			$entity_field_properties = $entity->getFieldProperties();

			foreach ($entity_fields as $field_name) {
				$field_properties = isset($entity_field_properties[$field_name]) ? $entity_field_properties[$field_name] : array();
				$caption = isset($field_properties['caption']) ? $field_properties['caption'] : str_replace('_', ' ', $field_name);
				$type = isset($field_properties['type']) ? $field_properties['type'] : 'text';
				$required = isset($field_properties['required']) ? $field_properties['required'] : false;
				
				$field = coreFormElementsLibrary::get($type, $field_name);				
				$field->setValue($entity->$field_name);
				$this->addField($field);
				$this->setFieldCaption($field_name, $caption);
				if ($required) $this->makeFieldReqiured($field_name);
			}		
		}
		
		
		public function updateEntity(coreBaseEntity $entity) {
			$entity_fields = $entity->getFields();
			foreach ($entity_fields as $field_name) {
				if ($this->hasField($field_name)) {
					$entity->$field_name = $this->getValue($field_name);
				}
			}
		}
		
		public function updateObject($object) {
			return $this->updateEntity($object);
		}
		
	
	
	}