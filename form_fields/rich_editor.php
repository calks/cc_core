<?php

	Application::loadLibrary('olmi/editor');

	class coreRichEditorFormField extends TEditorField {
		
		
		public function __construct($name, $params) {
			
			parent::__construct(
				$name,
				isset($params['value']) ? $params['value'] : '',
				isset($params['width']) ? $params['width'] : 600,
				isset($params['height']) ? $params['height'] : 300
			);	
		}
		
	}