<?php
        
    class coreUserSessionLibrary {
    	static $user_id;
    	static $user_account;    	
    	static $live;    	
    	var $auto_save;
    	
    	function getSessionKey() {
    		return Application::getApplicationName() . "UserSession"; 
    	}
    	
    	function __construct() {    		    		
    		if (self::$live) {    			
    			$this->auto_save = 0;
    			return;
    		}
    		$this->auto_save = 1;
    		
    		self::$live = 1;
    		self::$user_id = null;
    		self::$user_account = null;    		
    		    		
    		self::$user_id = (int)@$_SESSION[$this->getSessionKey()];
    		if (!self::$user_id) return;
    		
    		
    		$user = Application::getEntityInstance('user');
    		self::$user_account = $user->load(self::$user_id);
    		
    		if (!self::$user_account) {
    			self::$user_id = null;
    			return;
    		}
    		
    		if (!self::$user_account->active) {
    			self::$user_id = null;
    			return;
    		}
    		
    		
    	}
    	
    	function __destruct() {    		
    		if (!$this->auto_save) return;    		
    		$_SESSION[$this->getSessionKey()] = self::$user_id;    		
    	}
    	
    	function getSerializableFields() {
    		return array("user_id");
    	}
    	
    	function __sleep() {
    		return $this->getSerializableFields();
    	}
    	
    	function auth($login, $pass) {
    		self::$user_id = null;
    		self::$user_account = null;
    		    		
    		$user = Application::getEntityInstance('user');
    		$table = $user->get_table_name();
    		$db = Application::getDb();

    		$login = addslashes($login);
    		$pass = md5($pass);
    		
    		$user_id = $db->executeScalar("
    			SELECT id
    			FROM $table
    			WHERE login='$login' AND pass='$pass' AND active=1
    		");
    		
    		if (!$user_id) return false;
    		    		    		
    		self::$user_account = $user->load($user_id);
    		self::$user_id = $user_id;
    		$_SESSION[$this->getSessionKey()] = self::$user_id;
    		return true; 
    	}
    	
    	function forceLogin($user_id) {
    		$user = Application::getEntityInstance('user');
    		$user = $user->load($user_id);
    		
    		if (!$user || !$user->active) return false;

    		self::$user_id = $user_id;
    		self::$user_account  = $user;
    		
    		$_SESSION[$this->getSessionKey()] = self::$user_id;
    		return true; 
	   	}
    	
    	function logout() {
    		unset($_SESSION[$this->getSessionKey()]);
    		self::$user_id = null;
    		self::$user_account = null;
    	}
    	
    	function userLogged() {
    		return is_object(self::$user_account);
    	}
    	
        function getUserAccount() {
        	return self::$user_account;
        }
        

        function getUserID() {
        	return self::$user_id;
        }
        
    	/*function register_ownership($object_type, $object_id, $user_id=null) {    		
    		if (!$user_id) $user_id = $this->getUserID();
    		if (!$user_id) return false;
    		
    		$object_type = addslashes($object_type);
    		$object_id = (int)$object_id;
    		
    		$db = JFactory::getDBO();
    		$db->execute("
    			REPLACE INTO #__mypms_owned_objects VALUES(
    				$user_id, $object_id, '$object_type'
    			)
    		");
    			
    		return true;
    		
    	}*/
    	    	
    	 
    	
    	
    	
    	
    }