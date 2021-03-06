<?php

	class coreBaseFilter extends coreBaseForm {

		protected $session_key;

		public function __construct() {
			
			$this->setMethod('post');
			if (is_null($this->fields )) $this->fields = array();
			
			$this->add_fields();
			
			$this->setFieldsCommonName($this->getFieldsGroupName());
			
			if ($this->isSearchQueryPosted()) {
				$this->LoadFromRequest($_REQUEST);
				$this->saveToSession();
			}
			else {				
				$this->loadFromSession(Application::getApplicationName());
			}

			$sort_link_options = $this->getSortLinkOptions();
			$current_sort_option = $this->getValue('search_order_field');
			if ($sort_link_options && !array_key_exists($current_sort_option, $sort_link_options)) {
				$sort_link_options_keys = array_keys($sort_link_options);
				$this->setValue('search_order_field', array_shift($sort_link_options_keys));
				$this->setValue('search_order_direction', null);
			}

			if (isset($_GET['search_order_field'])) {
				$this->setValue('search_order_field', $_GET['search_order_field']);
			}

			if (isset($_GET['search_order_direction'])) {
				$this->setValue('search_order_direction', $_GET['search_order_direction']);
			}
			
		}
		
		
		public function setValue($field_name, $value, $save_to_session=true) {
			$out = parent::setValue($field_name, $value);
			if ($save_to_session) $this->saveToSession();
			return $out;		
		}

		protected function isSearchQueryPosted() {			
			return isset($_REQUEST[$this->getFieldsGroupName()]);
		}

		
		public function setEntityLoadParams(&$params) {
			$order_option = $this->getValue('search_order_field');

			$order_direction = $this->getValue('search_order_direction');
			if (!in_array($order_direction, array('asc', 'desc'))) {
				$order_direction = 'asc';
			}

			if ($order_option) {
				$order_options = $this->getSortLinkOptions();
				$order_field = isset($order_options[$order_option]) ? $order_options[$order_option] : null;
				if ($order_field) {
					$order_field = addslashes(trim($order_field));
					$params['order_by'][] = "$order_field $order_direction";
				}
			}
		}

		
		public function set_params(&$params) {
			return $this->setEntityLoadParams($params);	
		}
		
		public function reset_values() {			
			unset($this->fields);
			$this->add_fields();			
			$this->saveToSession();
		}

		function trimField() {
			foreach ($this->fields as $key => $value) {
				$field_value = $this->getValue($key);
				if (is_array($field_value)) continue;
				$this->setValue($key, trim($field_value));
			}
		}

		protected function add_fields() {
			return $this->initFieldSet();
		}
		
		protected function initFieldSet() {
			$this->addField(coreFormElementsLibrary::get('hidden', 'search_order_field'));
			$this->addField(coreFormElementsLibrary::get('hidden', 'search_order_direction'));
		}
		

		public function printGetSearch() {			
			$this->trimField();
			$w = array();
			foreach ($this->fields as $field_name => $field_params) {
				$field_object = $field_params['field'];				
				$value = $field_object->getValue();				
				if (is_array($value)) {
					foreach ($value as $v) $w[] = "{$field_name}[]=$v";
				}
				elseif ($value) {
					$w[] = "{$field_object->getFieldName()}=$value";
				}
			}
	
			asort($w);
	
			if (count($w) == 0) {
				return "";
			}
			else {
				return join("&", $w);
			}
		}
	
		public function initWithGetSearch($get_str) {
			$this->reset_values();
			$input = array();
			parse_str($get_str, $input);
			$this->LoadFromRequest($input);
			$this->saveToSession();
		}
	
		protected function saveToSession() {			
			$session_key = $this->getSessionKey();
			foreach (array_keys($this->fields) as $field) {				
				$_SESSION[$session_key][$field] = $this->getValue($field);
			}
		}
	
		protected function loadFromSession() {			
			$session_key = $this->getSessionKey();
			foreach (array_keys($this->fields) as $field) {				
				if (!isset($_SESSION[$session_key][$field])) continue;
				$this->setValue($field, $_SESSION[$session_key][$field], false);
			}			
		}
	
		protected function getSessionKey() {
			$fields = array_keys($this->fields);			
			$fields_hash = md5(implode('|', $fields).Application::getApplicationName().get_class($this));
			return "filter_state_$fields_hash";
		}
		
		protected function getFieldsGroupName() {
			return $this->getResourceName() . '_filter'; 
		}
		
	
		protected function getSortLinkOptions() {
			return array();
		}
	
		public function sortLink($caption, $order_option, $base, $url_addition = null, $default_direction = 'asc') {
	
			$current_order_field = $this->getValue('search_order_field');
			$current_order_direction = $this->getValue('search_order_direction');
			if (!in_array($current_order_direction, array('asc', 'desc'))) {
				$current_order_direction = $default_direction;
			}
	
			if ($current_order_field == $order_option) {
				$new_order_direction = $current_order_direction == 'asc' ? 'desc' : 'asc';
			}
			else {
				$new_order_direction = $default_direction;
			}
	
			$classes = array('sort_link');
			if ($current_order_field == $order_option) {
				$classes[] = 'selected';
				$classes[] = $current_order_direction;
			}
	
			if (!in_array('selected', $classes)) {
				$icon_class = 'ui-icon-carat-2-n-s';
			}
			else {
				$icon_class = in_array('desc', $classes) ? 'ui-icon-triangle-1-s' : 'ui-icon-triangle-1-n';
			}
	
			$classes = 'class="'.implode(' ', $classes).'"';
	
			$link = $base;
			if (strpos($base, '?') === false) $link .= '?';
			else $link .= '&';
	
			$link .= 'search_order_field='.rawurlencode($order_option);
			$link .= '&search_order_direction='.rawurlencode($new_order_direction);
	
			$link = Application::getSeoUrl($link);
	
			if ($url_addition) {
				$link .= "&$url_addition";
			}
	
			//return "<a href=\"$link\" $classes>$caption</a>";
	
			return "
	        		<div class=\"DataTables_sort_wrapper\">
	        			<a href=\"$link\" $classes>$caption</a>
	        			<span class=\"DataTables_sort_icon css_right ui-icon $icon_class\"></span>
	        		</div>
	        	";
		}
	
	}

