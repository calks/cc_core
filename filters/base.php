<?php

    Application::loadLibrary('olmi/form');
    Application::loadLibrary('fields');

    class coreBaseFilter extends BaseForm {

        protected $mode;
        protected $session_key;

        function __construct( array $params=array() )
        {
            if( is_null( $this->fields ) )
                $this->fields = array();

            $this->mode = isset( $params[ 'mode' ] )
                ? $params[ 'mode' ]
                : 'front';

            $this->add_fields();
            
            $this->addField(new THiddenField('search_order_field'));
        	$this->addField(new THiddenField('search_order_direction'));        
            
        	
            if ($this->isSearchQueryPosted()) {
                $this->LoadFromRequest($_REQUEST);
                $this->saveToSession(Application::getApplicationName());
            } else {
                $this->loadFromSession(Application::getApplicationName());
            }
            
        	if (isset($_GET['search_order_field'])) {        		
        		$this->setValue('search_order_field', $_GET['search_order_field']);
        		$this->saveToSession(Application::getApplicationName());
        	}
        	
        	if (isset($_GET['search_order_direction'])) {
        		$this->setValue('search_order_direction', $_GET['search_order_direction']);
        		$this->saveToSession(Application::getApplicationName());
        	}
            
        }
        
        function isSearchQueryPosted() {
        	if (!Request::isPostMethod()) return false;
        	foreach ($this->fields as $field_name => $field) {
        		if (isset($_POST[$field_name])) return true;
        	}
        	return false;
        }
        
        function set_params(&$params) {        	
        	$order_field = $this->getValue('search_order_field');
        	$order_direction = $this->getValue('search_order_direction');        	
        	if (!in_array($order_direction, array('asc', 'desc'))) {
        		$order_direction = 'asc';
        	}
        	
        	if ($order_field) {
        		$order_field = addslashes(trim($order_field));
        		$params['order_by'][] = "$order_field $order_direction";	
        	}
        }

        function reset_values() {
            unset ($this->fields);
            $this->add_fields($this->mode);
            $this->saveToSession();
        }

        function trimField() {
            foreach ($this->fields as $key => $value) {
                $field_value = $this->getValue($key);
                if (is_array($field_value)) continue;
                $this->setValue($key, trim($field_value));
            }
        }

        function add_fields() {}

        function printGetSearch() {
            $this->trimField();
            $w = array();
            foreach ($this->fields as $field_name => $field_object) {
                $value = $field_object->getValue();
                if (is_array($value)) {
                    foreach($value as $v) $w[] = "{$field_name}[]=$v";
                }
                elseif ($value) {
                    $w[] = "{$field_name}=$value";
                }
            }

            asort($w);

            if (count($w) == 0) {
                return "";
            } else {
                return join("&", $w);
            }
        }

        function initWithGetSearch($get_str) {
            $this->reset_values();
            $input = array();
            parse_str($get_str, $input);
            $this->LoadFromRequest($input);
            $this->saveToSession();
        }

        function saveToSession($name = '') {
            $session_key = $this->getSessionKey($name);            
            foreach (array_keys($this->fields) as $field) {
                $_SESSION[$session_key][$field] = $this->getValue($field);
            }
        }

        function loadFromSession($name = '') {
            $session_key = $this->getSessionKey($name);            
            foreach (array_keys($this->fields) as $field) {
                if (!isset($_SESSION[$session_key][$field])) continue;
                $this->setValue($field, $_SESSION[$session_key][$field]);
            }
        }

        function getSessionKey($name = '') {
            $fields = array_keys($this->fields);
            $fields_hash = md5(implode('|', $fields).$name.$this->mode);
            return "filter_state_$fields_hash";
        }
        
        
        function sortLink($caption, $order_field, $base, $url_addition=null) {
        	
        	
        	if (strpos($order_field, '.') !== false) {
        		$order_field = explode('.', $order_field);
        		$order_field[0] = coreBaseEntity::getTableAlias($order_field[0]);
        		$order_field = implode('.', $order_field);	
        	}
        	
        	$current_order_field = $this->getValue('search_order_field');
        	$current_order_direction = $this->getValue('search_order_direction');
        	if (!in_array($current_order_direction, array('asc', 'desc'))) {
        		$current_order_direction = 'asc';
        	}
        	
        	if ($current_order_field==$order_field) {
        		$new_order_direction = $current_order_direction == 'asc' ? 'desc' : 'asc';
        	}
        	else {
        		$new_order_direction = 'asc';
        	}
        	
        	$classes = array('sort_link');
        	if ($current_order_field == $order_field) {
        		$classes[] = 'selected';
        		$classes[] = $current_order_direction;
        	}
        	
        	
        	if (!in_array('selected', $classes)) {
        		$icon_class = 'ui-icon-carat-2-n-s';
        	}
        	else {
        		$icon_class = in_array('desc', $classes) ? 'ui-icon-triangle-1-s' : 'ui-icon-triangle-1-n';
        	}
        	
        	$classes = 'class="' . implode(' ', $classes) . '"';
        	
        	$link = $base;
        	if (strpos($base, '?') === false) $link .= '?';
        	else $link .= '&';
        	
        	$link .= 'search_order_field=' . rawurlencode($order_field);
        	$link .= '&search_order_direction=' . rawurlencode($new_order_direction);
        	
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

