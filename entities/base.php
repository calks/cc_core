<?php

	class coreBaseEntity extends DataObject {
		function get_table_name() {
			return $this->getTableName();		
		}
		
		public function loadList($params=array()) {
			return parent::load_list($params);
		}
				
	}