<?php 

	require_once Application::getSitePath() . '/core/addons/form_elements/edit.php';
	
	class coreFormElementsAddonPasswordField extends coreFormElementsAddonEditField {
		function getInputType() {
			return 'password';
		}
	}
