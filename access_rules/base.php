<?php

	abstract class coreBaseAccessRule {
		
		abstract public function accessAllowed($user, $resource, $action);
	
	}