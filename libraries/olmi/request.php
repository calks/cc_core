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
   * Checks if the request method via which variables passed is HEAD
   * @return int
   */
  function isHeadMethod() {
    return strcasecmp($_SERVER["REQUEST_METHOD"], "HEAD") == 0;
  }

  /**
   * Checks if the request method via which variables passed is GET
   * @return int
   */
  function isGetMethod() {
    return strcasecmp($_SERVER["REQUEST_METHOD"], "GET") == 0;
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
   * Returns slashed value of variable from request
   * @param string $name
   * @param mixed $default
   * @return mixed
   */
  function getEscaped($name, $default = NULL) {
    if (array_key_exists($name, $_REQUEST)) {
      return get_magic_quotes_gpc() ? $_REQUEST[$name] : addslashes($_REQUEST[$name]);
    }
    else {
      return $default;
    }
  }

  /**
   * Returns the request method via which variables passed
   * @return string
   */
  function getMethod() {
    return $_SERVER["REQUEST_METHOD"];
  }

  /**
   * Returns the query string, if any, via which the page was accessed
   * @return string
   */
  function getQueryString() {
    if (array_key_exists("QUERY_STRING", $_SERVER)) {
      return $_SERVER["QUERY_STRING"];
    }
    else {
      return NULL;
    }
  }

  /**
   * Returns the filename of the currently executing script, relative to the document root
   * @return string
   */
  function getScriptName()
  {
      /// PHP CGI error
    /*if (array_key_exists("SCRIPT_NAME", $_SERVER)) {
      return $_SERVER["SCRIPT_NAME"];
    }
    else {*/
      return $_SERVER["PHP_SELF"];
    /*}*/
  }

  /**
   * Returns script path without script name
   * @return string
   */
  function getScriptPathOnly() {
    $path = Request::getScriptName();
    $slashPos = strrpos($path, '/');
    if ($slashPos === FALSE) {
      return $path;
    }
    else {
      return substr($path, 0, $slashPos + 1);
    }
  }

  /**
   * Returns URL path to the current module.
   * @param string $localPath
   * @return string
   */
  function getModulePath($localPath) {
    $path = Request::getScriptPathOnly();
    if ($localPath != '' && substr($path, strlen($path) - strlen($localPath)) == $localPath) {
      $path = substr($path, 0, strlen($path) - strlen($localPath));
    }
    return $path;
  }

  /**
   * Returns script name without script path
   * @return string
   */
  function getScriptNameOnly(){
    $path = Request::getScriptName();
    $lastSlashPos = strrpos($path, '/');
    if ($lastSlashPos === FALSE) {
      return $path;
    }
    else {
      return substr($path, $lastSlashPos + 1);
    }
  }

  /**
   * Returns general information about request: method name, script name,
   * the query string and variables from $_POST. Intended usage is writing
   * the retruned value to the log.
   * @return string
   */
  function getDebugInfo() {
    $result = Request::getMethod()." ".Request::getScriptName()." ";
    $query = Request::getQueryString();
    if (isset($query)) $result .= $query;
    else $result .= "-";
    foreach ($_POST as $key => $value) {
      $result .= " ".$key."=".$value;
    }
    return $result;
  }

  /**
   * Checks if the request method has parameter
   * @param string $paramName
   * @return bool
   */
  function hasParam($paramName) {
    return array_key_exists($paramName, $_REQUEST);
  }

  /**
   * Checks if the "cancel" button was clicked
   * @return bool
   */
  function isCancelled() {
    if (Request::hasParam(CANCEL_BUTTON_NAME)) {
      return TRUE;
    }
    foreach ($_REQUEST as $key => $value) {
      if (substr($key, 0, strlen(CANCEL_BUTTON_NAME)) == CANCEL_BUTTON_NAME && !empty($value)) {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * Returns contents of the Host: header from the current request, if there is one
   * @return string
   */
  function getHostName() {
    return $_SERVER['HTTP_HOST'];
  }



}
