<?php

	class coreBlocksModule extends coreBaseModule {
	
		public function run() {
			if (!$this->isAjax()) return $this->terminate();			
			
			$this->response_data['blocks'] = array();
			$this->response_data['static_list'] = array();
						
			$request = $this->getSanitizedRequest();
			if (!$request) return $this->returnResponse();
						
			foreach ($request as $block_name => $params) {
				$block = Application::getBlock($block_name);
				$block->runTask($params['task'], $params['data']);
				
				$this->response_data['blocks'][$block_name] = $block->composeAjaxResponse();
			}
			
			$page = Application::getPage();
			$this->response_data['static_list'] = $page->getStaticList();
			
			$this->returnResponse();
		}
		
		
		protected function getSanitizedRequest() {
			$request_raw = isset($_REQUEST['request']) ? $_REQUEST['request'] : null;
			if (!$request_raw) return array();
			if (!is_array($request_raw)) return array();
			
			$out = array();
			foreach($request_raw as $block_name => $params) {
				if (!Application::resourceExists($block_name, APP_RESOURCE_TYPE_BLOCK)) continue;
				$out[$block_name] = array(
					'task' => isset($params['task']) ? $params['task'] : null,
					'data' => isset($params['data']) ? $params['data'] : null				
				);
			}
			return $out;
		}
		
	}