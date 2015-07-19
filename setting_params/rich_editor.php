<?php

	
	class coreRichEditorSettingParam extends coreBaseSettingParam {
		
		protected function getField() {
			$field = coreFormElementsLibrary::get('rich_editor', $this->getFieldName());
			$field->setWidth(700);
			return $field;
			//
		}
		
		
	}