<?php

	class coreBaseEntity extends DataObject {
		function get_table_name() {
			return $this->getTableName();		
		}
				
	}