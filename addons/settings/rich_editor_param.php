<?php

	
	class coreSettingsAddonRichEditorParam extends coreSettingsAddonBaseParam {
		
		protected function getField() {
			$field = coreFormElementsLibrary::get('rich_editor', $this->getFieldName());
			$field->setWidth(700);
			return $field;
			//
		}
		
		
	}