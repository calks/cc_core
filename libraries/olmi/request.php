<?php 

define('CANCEL_BUTTON_NAME', 'com_olmisoft_html_cancel');


class Request {
  /**
   * Checks if the request method via which variables passed is POST
   * @return int
   */
  function isPostMethod() {
    return strcasecmp($_SERVER["REQUEST_METHOD"], "POST") == 0;
  }


  /**
   * Checks if the information protocol via which the page was requested is HTTP/1.1
   * @return int
   */
  function isHTTP11() {
    return strcasecmp($_SERVER["SERVER_PROTOCOL"], "HTTP/1.1") == 0;
  }

  /**
   * Returns value of variable from request
   * @param string $name
   * @param mixed $default
   * @return mixed
   */
  function get($name, $default = NULL) {
    if (array_key_exists($name, $_REQUEST)) {
    	$value = $_REQUEST[$name];
    	if (!is_array($value)) {
    		$is_url_encoded = preg_match('/%[a-h0-9]{2}/is', $value);
    		if ($is_url_encoded) $value = rawurldecode($value);
    	}    	
    	return $value;
    }
    else {
      return $default;
    }
  }
  
  
  
	public static function isFieldValueSet($field_name, $request) {
		$uid = md5(uniqid());
		return self::getFieldValue($field_name, $request, $uid) != $uid;
	}  
  
	public static function getFieldValue($field_name, $request, $default=null) {		
		$field_name_parts = explode('[', $field_name);
		$ptr = &$request;
		
		while ($field_name_parts) {			
			$key = array_shift($field_name_parts);
			$key = trim($key, ']');
			
			if (!isset($ptr[$key])) {				
				return $default;
			}
			else {
				$ptr = &$ptr[$key];				
			} 
		}
		
		return $ptr;		
	}
  

  /**
   * Returns the request method via which variables passed
   * @return string
   */
  function getMethod() {
    return $_SERVER["REQUEST_METHOD"];
  }



  /**
   * Returns script path without script name
   * @return string
   */
  function getScriptPathOnly() {
    $path = $_SERVER["PHP_SELF"];
    $slashPos = strrpos($path, '/');
    if ($slashPos === FALSE) {
      return $path;
    }
    else {
      return substr($path, 0, $slashPos + 1);
    }
  }


  /**
   * Returns contents of the Host: header from the current request, if there is one
   * @return string
   */
  function getHostName() {
    return $_SERVER['HTTP_HOST'];
  }



}
