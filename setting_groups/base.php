<?php

	class coreBaseSettingGroup {
		
		public function getParamsTree() {
			return array();
		}
		
		public function getGroupNames() {
			return array();
		}
		
		protected function arrayToObjects($tree) {
			$out = array();
			
			//foreach ($treeparams as $param_name=>$param_data) {
			foreach ($params as $param_name=>$param_data) {
				$obj = new stdClass();
				$obj->param_name = $param_name;
				$obj->param_displayed_name = isset($param_data['displayed_name']) ? $param_data['displayed_name'] : $param_name;
				
				/*print_r($param_data);
				$param_addon = $this->getParamAddon($param_data['type']);
				if (!$param_addon) continue;*/
			}
			die();
		}
	
	}