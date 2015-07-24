<?php

	abstract class coreBaseAccessRule {
		
		abstract public function accessAllowed($user, $resource, $action);
		
		public function restrictLoadParams($user, $entity, &$load_params) {
		
		}
	
	}