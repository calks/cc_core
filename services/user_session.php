<?php
        
    class coreUserSessionService {
    	
    	protected static $user_id;
    	protected static $user_account;    	
    	protected static $live;
    	    	
    	protected $auto_save;    	
    	
    	protected function getSessionKey() {
    		return Application::getApplicationName() . "UserSession"; 
    	}
    	
    	protected function log($message) {    	
    		return;
    		$log_path = Application::getSitePath() . Application::getVarDirectory() . '/session.log';
    		$url = 'http://' . $_SERVER['HTTP_HOST'] . @$_SERVER[REQUEST_URI];
    		$session_key = $this->getSessionKey();
    		$session_id = session_id();
    		$cookie = @$_COOKIE['PHPSESSID'];
    		file_put_contents($log_path, date('Y-m-d H:i:s') .  " $message ($session_key, $session_id, $cookie, $url)\n", FILE_APPEND);
    	}
    	
    	public function __construct() {
    		if (!session_id()) session_start();
    		
    		if (self::$live) {
    			$this->auto_save = 0;
    			return;
    		}
    		$this->auto_save = 1;
    		
    		self::$live = 1;
    		self::$user_id = null;
    		self::$user_account = null;    		
    		    		
    		self::$user_id = (int)@$_SESSION[$this->getSessionKey()];
    		$this->log("loaded: " . self::$user_id);
    		
    		if (!self::$user_id) return;
    		
    		$user = Application::getEntityInstance('user');
    		self::$user_account = $user->load(self::$user_id);
    		
    		if (!self::$user_account) {
    			self::$user_id = null;
    			return;
    		}
    		
    		if (!self::$user_account->is_active) {
    			self::$user_id = null;
    			return;
    		}
    		
    	}
    	
    	public function __destruct() {    		
    		if (!$this->auto_save) return;    		
    		$_SESSION[$this->getSessionKey()] = self::$user_id;    
    		$this->log("saved: " . self::$user_id);		
    	}
    	
    	protected function getSerializableFields() {
    		return array("user_id");
    	}
    	
    	public function __sleep() {
    		return $this->getSerializableFields();
    	}
    	
    	
    	public function findUserByLogin($login) {
    		$user = Application::getEntityInstance('user');
    		$table = $user->getTableName();
    		$login = addslashes($login);
    		$params['where'][] = "$table.login='$login'";
    		$users = $user->load_list($params);    		
    		return $users ? array_shift($users) : null;    		
    	}
    	
    	public function findUserByCredentials($login, $pass) {
    		$user = Application::getEntityInstance('user');
    		$pass = $user->encriptPassword($pass);
    		$user = $this->findUserByLogin($login);
    		if (!$user) return null;
    		if ($user->pass != $pass) return null;
    		return $user;
    	}
    	
    	public function auth($login, $pass) {
    		self::$user_id = null;
    		self::$user_account = null;
    		    		
    		$user = $this->findUserByCredentials($login, $pass);

    		if (!$user) return false;
    		$can_login = coreAccessControlLibrary::accessAllowed($user, $user, 'login');    		
    		if (!$can_login) return false;
    		    		    		
    		self::$user_account = $user;
    		self::$user_id = $user->id;
    		$_SESSION[$this->getSessionKey()] = self::$user_id;
    		return true; 
    	}
    	
    	public function forceLogin($user_id) {
    		$user = Application::getEntityInstance('user');
    		$user = $user->load($user_id);
    		
    		if (!$user || !$user->is_active) return false;

    		self::$user_id = $user_id;
    		self::$user_account  = $user;
    		
    		$_SESSION[$this->getSessionKey()] = self::$user_id;
    		return true; 
	   	}
    	
    	public function logout() {
			$this->log('logout');
    		
    		unset($_SESSION[$this->getSessionKey()]);
    		self::$user_id = null;
    		self::$user_account = null;
    	}
    	
    	public function userLogged() {
    		return is_object(self::$user_account);
    	}
    	
        public function getUserAccount() {
        	return self::$user_account;
        }
        

        public function getUserID() {
        	return self::$user_id;
        }
        
    	
    }