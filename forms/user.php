<?php

	class coreUserForm extends coreEntityEditForm {
		
		public function initWithEntityFields($entity) {
			parent::initWithEntityFields($entity);
			
			$roles_field = coreFormElementsLibrary::get('checkbox_collection', 'roles');			
			$roles_field->setOptions($entity->getRoleSelect());
			
			$this->addField($roles_field);
			$this->setFieldCaption('roles', $entity->gettext('Roles'));
		
		}
	
		
	}