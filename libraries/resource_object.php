<?php

	abstract class coreResourceObjectLibrary {
	
		
		abstract protected function getResourceType(); 
		
        public function getName() {
        	return coreNameUtilsLibrary::getResourceName(get_class($this));
        }
		
	
	}