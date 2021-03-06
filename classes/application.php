<?php

	define('LANGUAGES_ENGLISH', 38);
    define('LANGUAGES_RUSSIAN', 136);

	
	class coreApplicationClass {
	
		
		private static $site_root;
		private static $site_url;
		private static $host;
		private static $config;
		private static $db;		
		private static $smarty;
		private static $breadcrumbs;
		private static $message_stack;
		private static $page;
		private static $user_session;
		private static $mobile = null;	


		public function __construct() {
					
			$application_name = $this->getApplicationName();
			self::$site_root = realpath(dirname(__FILE__)."/../..");

			$config_path = self::$site_root."/applications/$application_name/conf.php";
			@include $config_path;

			self::$config = $config;
			
			$host_path = self::getSitePath() . self::getVarDirectory() . "/host_name";

			if (isset($_SERVER['HTTP_HOST'])) {
				self::$host = @strtolower($_SERVER['HTTP_HOST']);
				@file_put_contents($host_path, self::$host);
			}
			else {				
				self::$host = @file_get_contents($host_path);
			}

			$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] ? 'https' : 'http';
			self::$site_url = $protocol . '://'.self::$host;

			self::$db = null;
			self::$breadcrumbs = null;
			self::$message_stack = null;

			
			self::loadLibrary('misc');    
    		
		}
		
		
		
		protected function preparePage($page) {
			
		}
		

		public function isMobile() {
			if (is_null(self::$mobile)) {
				self::$mobile = (int) ((bool) (self::detectMobileBrowser()));
			}
			
			return self::$mobile;
		}

		public function loadLibrary($library_name) {			
			$library_name = explode('/', $library_name);
			$path = self::$site_root.'/core/libraries';
			foreach ($library_name as $fragment) $path .= '/'.$fragment;
			$path .= '.php';
			
			if (is_file($path)) include_once($path);
			else return false;
			return true;
		}

		public function runModule($module_name, $params = array()) {

			$module = self::getResourceInstance(APP_RESOURCE_TYPE_MODULE, $module_name);
			$out = call_user_func(array($module, 'run'), $params);

			return $out;

		}
		
		
		public function gettext($message) {
        	$params = func_get_args();
        	array_unshift($params, null);        	
        	return call_user_func_array(array('coreGettextLibrary', 'gettext'), $params);
        }

		public function ngettext($message, $message_plural, $n) {
        	return coreGettextLibrary::ngettext(null, $message, $message_plural, $n);
        }
        
		
		public function getDb() {
			if (!self::$db) {				
				self::$db = new coreMysqlLibrary(self::$config['database']['host'], self::$config['database']['name'], self::$config['database']['user'], self::$config['database']['pass']);
				self::$db->execute("set names utf8");
			}
			return self::$db;
		}

		

		public function getPage() {
			if (!self::$page) {
				self::$page = corePageLibrary::getInstance();
				$this->preparePage(self::$page);
			}

			return self::$page;
		}

		public function getUserSession() {
			if (!self::$user_session) {				
				$class_name = coreResourceLibrary::getEffectiveClass('service', 'user_session');								
				self::$user_session = new $class_name();
			}

			return self::$user_session;
		}

		
		public function resourceExists($resource_type, $resource_name) {
			$resource_class = coreResourceLibrary::getEffectiveClass($resource_type, $resource_name);			
			return $resource_class != '';
		}

		public function filterExists($filter_name) {
			return self::resourceExists(APP_RESOURCE_TYPE_FILTER, $filter_name);
		}
		
		public function entityExists($entity_name) {
			return self::resourceExists(APP_RESOURCE_TYPE_ENTITY, $entity_name);
		}

		public function getEntityInstance($entity_name) {
			return self::getResourceInstance(APP_RESOURCE_TYPE_ENTITY, $entity_name);
		}

		// TODO: Переименовать в getBlockInstance
		public function getBlock($block_name) {
			return self::getResourceInstance(APP_RESOURCE_TYPE_BLOCK, $block_name);
		}

		// TODO: Переименовать в getFilterInstance
		public function getFilter($filter_name) {
			return self::getResourceInstance(APP_RESOURCE_TYPE_FILTER, $filter_name);
		}

		public function getResourceInstance($resource_type, $resource_name) {			
			$resource_class = coreResourceLibrary::getEffectiveClass($resource_type, $resource_name);
			if ($resource_class) return new $resource_class();
			else die("Can't instantiate $resource_name $resource_type" );
		}


		public function getResourceRouting() {
			$resource_routing = array();
			
			$resource_routing['default'] = array(
				'applications/' . Application::getApplicationName(),
				APP_RESOURCE_CONTAINER_PACKAGES,
				APP_RESOURCE_CONTAINER_CORE
			);
			
			return $resource_routing;
			
		}
		

		public function getSmarty($namespace='default') {
			if (!self::$smarty) self::$smarty = array();
			if (!isset(self::$smarty[$namespace])) {

				//self::loadLibrary('smarty/Smarty.Class');

				$smarty = new coreSmartyLibrary();
				//$smarty->assign('here', 1);

				$cache_dir = Application::getSitePath().Application::getTempDirectory().'/smarty/cache/';
				$compile_dir = Application::getSitePath().Application::getTempDirectory().'/smarty/compile/';

				if (!is_dir($cache_dir)) {
					if (!@mkdir($cache_dir, 0777, true)) {
						die("Can't create smarty cache directory");
					}
				}

				if (!is_dir($compile_dir)) {
					if (!@mkdir($compile_dir, 0777, true)) {
						die("Can't create smarty compile directory");
					}
				}
				
				$smarty->compile_dir = $compile_dir;				
				$smarty->cache_dir = $cache_dir;

				$smarty->caching = false;
				self::$smarty[$namespace] = $smarty;
			}
			return self::$smarty[$namespace];
		}
		
		
		public function getMailer() {
			
			$mailers = coreResourceLibrary::findEffective('message_sender', 'email');						
			if (!$mailers) return null;
			$mailer_class = $mailers['email']->class;
			return new $mailer_class();
		}

		
		public function getBreadcrumbs() {
			if (!self::$breadcrumbs) {
				$class_name = coreResourceLibrary::getEffectiveClass('service', 'breadcrumbs');
				self::$breadcrumbs = new $class_name();
			}
		
			return self::$breadcrumbs;
		}
		
		
		
		public function getMessageStack() {
			if (!self::$message_stack) {				
				self::$message_stack = new coreMessageStackLibrary();
			}
			return self::$message_stack;
		}
		
		
		public function stackMessage($message, $type='message') {
			$message_stack = self::getMessageStack();
			$message_stack->add($message, $type);
		}
		
		public function stackError($message) {
			$message_stack = self::getMessageStack();
			$message_stack->add($message, 'error');
		}
		
		public function stackWarning($message) {
			$message_stack = self::getMessageStack();
			$message_stack->add($message, 'warning');
		}
		

		public function getHost() {
			return self::$host;
		}

		// TODO: Переименовать
		public function getSitePath() {
			return self::$site_root;
		}

		public function getSiteUrl() {
			return self::$site_url;
		}

		public function getApplicationName() {
			$classname = get_class($this);
			preg_match('/^(?<app_name>[a-zA-Z0-9]+)App[A-Z].*/U', $classname, $matches);
			return coreNameUtilsLibrary::camelToUnderscored($matches['app_name']);			
		}

		public function getSeoUrl($internal_url) {
			return coreUrlRewriterLibrary::internalToSeo($internal_url);
		}

		public function getTempDirectory() {
			return isset(self::$config['temp_directory']) ? self::$config['temp_directory'] : '/temp/'.self::getApplicationName();
		}

		public function getVarDirectory() {
			return isset(self::$config['var_directory']) ? self::$config['var_directory'] : '/var/'.self::getApplicationName();
		}
		
		
				
		
		protected function detectMobileBrowser() {
			$user_agent = strtolower(getenv('HTTP_USER_AGENT'));
			$accept = strtolower(getenv('HTTP_ACCEPT'));

			if ((strpos($accept, 'text/vnd.wap.wml') !== false) || (strpos($accept, 'application/vnd.wap.xhtml+xml') !== false)) {
				return 1; // Мобильный браузер обнаружен по HTTP-заголовкам
				}

			if (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) {
				return 2; // Мобильный браузер обнаружен по установкам сервера				
				}

			if (preg_match('/(mini 9.5|vx1000|lge |m800|e860|u940|ux840|compal|'.'wireless| mobi|ahong|lg380|lgku|lgu900|lg210|lg47|lg920|lg840|'.'lg370|sam-r|mg50|s55|g83|t66|vx400|mk99|d615|d763|el370|sl900|'.'mp500|samu3|samu4|vx10|xda_|samu5|samu6|samu7|samu9|a615|b832|'.'m881|s920|n210|s700|c-810|_h797|mob-x|sk16d|848b|mowser|s580|'.'r800|471x|v120|rim8|c500foma:|160x|x160|480x|x640|t503|w839|'.'i250|sprint|w398samr810|m5252|c7100|mt126|x225|s5330|s820|'.'htil-g1|fly v71|s302|-x113|novarra|k610i|-three|8325rc|8352rc|'.'sanyo|vx54|c888|nx250|n120|mtk |c5588|s710|t880|c5005|i;458x|'.'p404i|s210|c5100|teleca|s940|c500|s590|foma|samsu|vx8|vx9|a1000|'.'_mms|myx|a700|gu1100|bc831|e300|ems100|me701|me702m-three|sd588|'.'s800|8325rc|ac831|mw200|brew |d88|htc\/|htc_touch|355x|m50|km100|'.'d736|p-9521|telco|sl74|ktouch|m4u\/|me702|8325rc|kddi|phone|lg |'.'sonyericsson|samsung|240x|x320vx10|nokia|sony cmd|motorola|'.'up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|'.'pocket|kindle|mobile|psp|treo)/', $user_agent)) {
				return 3; // Мобильный браузер обнаружен по сигнатуре User Agent
				}

			if (in_array(substr($user_agent, 0, 4), Array("1207", "3gso", "4thp", "501i", "502i", "503i", "504i", "505i", "506i",
				"6310", "6590", "770s", "802s", "a wa", "abac", "acer", "acoo", "acs-",
				"aiko", "airn", "alav", "alca", "alco", "amoi", "anex", "anny", "anyw",
				"aptu", "arch", "argo", "aste", "asus", "attw", "au-m", "audi", "aur ",
				"aus ", "avan", "beck", "bell", "benq", "bilb", "bird", "blac", "blaz",
				"brew", "brvw", "bumb", "bw-n", "bw-u", "c55/", "capi", "ccwa", "cdm-",
				"cell", "chtm", "cldc", "cmd-", "cond", "craw", "dait", "dall", "dang",
				"dbte", "dc-s", "devi", "dica", "dmob", "doco", "dopo", "ds-d", "ds12",
				"el49", "elai", "eml2", "emul", "eric", "erk0", "esl8", "ez40", "ez60",
				"ez70", "ezos", "ezwa", "ezze", "fake", "fetc", "fly-", "fly_", "g-mo",
				"g1 u", "g560", "gene", "gf-5", "go.w", "good", "grad", "grun", "haie",
				"hcit", "hd-m", "hd-p", "hd-t", "hei-", "hiba", "hipt", "hita", "hp i",
				"hpip", "hs-c", "htc ", "htc-", "htc_", "htca", "htcg", "htcp", "htcs",
				"htct", "http", "huaw", "hutc", "i-20", "i-go", "i-ma", "i230", "iac",
				"iac-", "iac/", "ibro", "idea", "ig01", "ikom", "im1k", "inno", "ipaq",
				"iris", "jata", "java", "jbro", "jemu", "jigs", "kddi", "keji", "kgt",
				"kgt/", "klon", "kpt ", "kwc-", "kyoc", "kyok", "leno", "lexi", "lg g",
				"lg-a", "lg-b", "lg-c", "lg-d", "lg-f", "lg-g", "lg-k", "lg-l", "lg-m",
				"lg-o", "lg-p", "lg-s", "lg-t", "lg-u", "lg-w", "lg/k", "lg/l", "lg/u",
				"lg50", "lg54", "lge-", "lge/", "libw", "lynx", "m-cr", "m1-w", "m3ga",
				"m50/", "mate", "maui", "maxo", "mc01", "mc21", "mcca", "medi", "merc",
				"meri", "midp", "mio8", "mioa", "mits", "mmef", "mo01", "mo02", "mobi",
				"mode", "modo", "mot ", "mot-", "moto", "motv", "mozz", "mt50", "mtp1",
				"mtv ", "mwbp", "mywa", "n100", "n101", "n102", "n202", "n203", "n300",
				"n302", "n500", "n502", "n505", "n700", "n701", "n710", "nec-", "nem-",
				"neon", "netf", "newg", "newt", "nok6", "noki", "nzph", "o2 x", "o2-x",
				"o2im", "opti", "opwv", "oran", "owg1", "p800", "palm", "pana", "pand",
				"pant", "pdxg", "pg-1", "pg-2", "pg-3", "pg-6", "pg-8", "pg-c", "pg13",
				"phil", "pire", "play", "pluc", "pn-2", "pock", "port", "pose", "prox",
				"psio", "pt-g", "qa-a", "qc-2", "qc-3", "qc-5", "qc-7", "qc07", "qc12",
				"qc21", "qc32", "qc60", "qci-", "qtek", "qwap", "r380", "r600", "raks",
				"rim9", "rove", "rozo", "s55/", "sage", "sama", "samm", "sams", "sany",
				"sava", "sc01", "sch-", "scoo", "scp-", "sdk/", "se47", "sec-", "sec0",
				"sec1", "semc", "send", "seri", "sgh-", "shar", "sie-", "siem", "sk-0",
				"sl45", "slid", "smal", "smar", "smb3", "smit", "smt5", "soft", "sony",
				"sp01", "sph-", "spv ", "spv-", "sy01", "symb", "t-mo", "t218", "t250",
				"t600", "t610", "t618", "tagt", "talk", "tcl-", "tdg-", "teli", "telm",
				"tim-", "topl", "tosh", "treo", "ts70", "tsm-", "tsm3", "tsm5", "tx-9",
				"up.b", "upg1", "upsi", "utst", "v400", "v750", "veri", "virg", "vite",
				"vk-v", "vk40", "vk50", "vk52", "vk53", "vm40", "voda", "vulc", "vx52",
				"vx53", "vx60", "vx61", "vx70", "vx80", "vx81", "vx83", "vx85", "vx98",
				"w3c ", "w3c-", "wap-", "wapa", "wapi", "wapj", "wapm", "wapp", "wapr",
				"waps", "wapt", "wapu", "wapv", "wapy", "webc", "whit", "wig ", "winc",
				"winw", "wmlb", "wonu", "x700", "xda-", "xda2", "xdag", "yas-", "your",
				"zeto", "zte-"))) {
				return 4; // Мобильный браузер обнаружен по сигнатуре User Agent
			}

			return 0; // Мобильный браузер не обнаружен
		}
		
	
	}