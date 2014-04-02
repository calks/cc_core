<?php

	define('USE_PROFILER', false);
	define('APP_RESOURCE_TYPE_BLOCK', 'block');
	define('APP_RESOURCE_TYPE_FILTER', 'filter');
	define('APP_RESOURCE_TYPE_LIBRARY', 'library');
	define('APP_RESOURCE_TYPE_MODULE', 'module');
	define('APP_RESOURCE_TYPE_ENTITY', 'entity');
	define('APP_RESOURCE_TYPE_ADDON', 'addon');
	define('APP_RESOURCE_TYPE_STATIC', 'static');
	define('APP_RESOURCE_TYPE_TEMPLATE', 'template');

	define('APP_RESOURCE_CONTAINER_ADMIN_APPLICATION', 1);
	define('APP_RESOURCE_CONTAINER_FRONT_APPLICATION', 2);
	define('APP_RESOURCE_CONTAINER_PACKAGES', 4);
	define('APP_RESOURCE_CONTAINER_CORE', 5);
	

	class Application {
		private static $application_name;
		private static $site_root;
		private static $site_url;
		private static $host;
		private static $config;
		private static $db;
		private static $dbEngine;
		private static $smarty;
		private static $breadcrumbs;
		private static $message_stack;
		private static $page;
		private static $user_session;

		private static $mobile;

		private static $packages_list;
		public static $loader;
		
		protected static $application_folders;

		public static function init($application_name) {

			self::$application_folders = array();
			
			self::$application_name = $application_name;
			self::$site_root = realpath(dirname(__FILE__)."/../../..");
			

			if (isset($_SERVER['HTTP_HOST'])) {
				self::$host = @strtolower($_SERVER['HTTP_HOST']);
			}
			else {
				self::$host = "";
			}

			self::$site_url = 'http://'.self::$host;

			self::$db = null;
			self::$breadcrumbs = null;
			self::$message_stack = null;

			self::$mobile = (int) ((bool) (self::detectMobileBrowser()));

			$config_path = self::$site_root."/applications/$application_name/conf.php";
			include_once $config_path;

			self::$config = $config;

			session_start();
			
			self::loadLibrary('misc');    
    		self::loadLibrary('core/dataobject');
		}
		
		
		public static function render($application_name) {
			
			self::init($application_name);		    
			if (USE_PROFILER) {
		        Application::loadLibrary('core/profiler');
		        $profiler = new profiler("Total time consumption");
		        $profiler->start();
		    }
			
		    require self::getSitePath()."/applications/$application_name/index.php";
		    
		    if (USE_PROFILER) $profiler->stop();			
			
		}
		

		public static function isMobile() {
			return self::$mobile;
		}

		public static function loadLibrary($library_name) {			
			$library_name = explode('/', $library_name);
			$path = self::$site_root.'/core/libraries';
			foreach ($library_name as $fragment) $path .= '/'.$fragment;
			$path .= '.php';
			
			if (is_file($path)) include_once($path);
			else return false;
			return true;
		}

		public static function runModule($module_name, $params = array()) {

			if (USE_PROFILER) {
				$profiler = new profiler("Module $module_name");
				$profiler->start();
			}

			$module = self::getResourceInstance($module_name, 'module');
			$out = call_user_func(array($module, 'run'), $params);

			if (USE_PROFILER) $profiler->stop();
			return $out;

		}


		public static function getDb() {
			if (!self::$db) {
				self::loadLibrary('olmi/mysql');
				self::$db = new MySqlDatabase(self::$config['database']['host'], self::$config['database']['name'], self::$config['database']['user'], self::$config['database']['pass']);
				self::$db->execute("set names utf8");
			}
			return self::$db;
		}

		

		public static function getPage() {
			if (!self::$page) {
				self::$page = corePageLibrary::getInstance();
			}

			return self::$page;
		}

		public static function getUserSession() {
			if (!self::$user_session) {				
				self::$user_session = new coreUserSessionLibrary();
			}

			return self::$user_session;
		}

		
		public static function resourceExists($resource_name, $resource_type) {
			$resource_class = self::getResourceClass($resource_name, $resource_type);			
			return $resource_class != '';
		}

		public static function filterExists($filter_name) {
			return self::resourceExists($filter_name, APP_RESOURCE_TYPE_FILTER);
		}
		
		public static function entityExists($entity_name) {
			return self::resourceExists($entity_name, APP_RESOURCE_TYPE_ENTITY);
		}

		public static function getEntityInstance($entity_name) {
			return self::getResourceInstance($entity_name, APP_RESOURCE_TYPE_ENTITY);
		}

		// TODO: Переименовать в getBlockInstance
		public static function getBlock($block_name) {
			return self::getResourceInstance($block_name, APP_RESOURCE_TYPE_BLOCK);
		}

		// TODO: Переименовать в getFilterInstance
		public static function getFilter($filter_name) {
			return self::getResourceInstance($filter_name, APP_RESOURCE_TYPE_FILTER);
		}
		

		public static function getPackagesList() {
			if (is_null(self::$packages_list)) {
				self::$packages_list = array();
				$packages_directory = self::getSitePath().'/packages';
				if (is_dir($packages_directory)) {
					$dir = opendir($packages_directory);
					while ($file = readdir($dir)) {
						if (in_array($file, array('.', '..'))) continue;
						if (!is_dir($packages_directory.'/'.$file)) continue;
						self::$packages_list[] = $file;
					}

					closedir($dir);
				}

			}

			return self::$packages_list;
		}

		public static function getResourceInstance($resource_name, $resource_type) {
			$resource_class = self::getResourceClass($resource_name, $resource_type);

			if ($resource_class) return new $resource_class();
			else die("Can't instantiate $resource_name $resource_type" );
		}

		// TODO: Придумать более удачное название. Подумать над переносом логики в coreNameUtils
		public static function getClassDirectory($class) {
			return self::$loader->getDirectoryForClass($class);
		}

		// TODO: Разобраться с терминологией (URL/Path/Directory). Либо переписать функции,
		// возвращающие абсолютные пути, либо дать им более явные имена (Absolute/Relative)
		public static function getResourceDirectory($resource_name, $resource_type) {
			// Убеждаемся, что для запрошенного ресурса есть класс
			// Кроме того, поскольку при запросе имени класса запустится автолоадер,
			// мы можем быть уверены что в кэше окажется нужный нам путь
			$resource_class = self::getResourceClass($resource_name, $resource_type);
			
			if ($resource_class) {				
				return self::$loader->getDirectory($resource_name, $resource_type);				
			}
			else {
				return null;
			}
		}
		
		
		public static function isAdmin() {
			return self::getApplicationName() == 'admin';
		}

		
		public static function getResourceRouting() {
			$resource_routing = isset(self::$config['resource_routing']) ? self::$config['resource_routing'] : array();

			if (!isset($resource_routing['default'])) {
				$resource_routing['default'] = array(
					APP_RESOURCE_CONTAINER_FRONT_APPLICATION,
					APP_RESOURCE_CONTAINER_PACKAGES,
					APP_RESOURCE_CONTAINER_CORE
				);

				$is_admin_application = Application::getApplicationName() == 'admin';
				if ($is_admin_application) {
					array_unshift($resource_routing['default'], APP_RESOURCE_CONTAINER_ADMIN_APPLICATION);
				}
			}
			
			return $resource_routing;
			
		}

		protected static function getPossibleClasses($resource_name, $resource_type) {
			$is_admin_application = false;
			$admin_application_name = $front_application_name = $application_name = Application::getApplicationName();
			if (strpos($application_name, '_admin') !== false) {
				$front_application_name = str_replace('_admin', '', $application_name);
				$is_admin_application = true;
			}
			else {
				$admin_application_name = $application_name.'_admin';
			}
			
			$resource_routing = self::getResourceRouting();

			$possible_classes = array();

			$routing_rule = isset($resource_routing[$resource_name]) ? $resource_routing[$resource_name] : $resource_routing['default'];

			$class_ending = ucfirst(coreNameUtilsLibrary::underscoredToCamel($resource_name).ucfirst($resource_type));
			foreach ($routing_rule as $rule) {
				if ($rule == APP_RESOURCE_CONTAINER_CORE) {
					$possible_classes[] = 'core'.$class_ending;
				}
				elseif ($rule == APP_RESOURCE_CONTAINER_FRONT_APPLICATION) {
					$possible_classes[] = coreNameUtilsLibrary::underscoredToCamel($front_application_name).'App'.$class_ending;
				}
				elseif ($rule == APP_RESOURCE_CONTAINER_ADMIN_APPLICATION) {
					$possible_classes[] = coreNameUtilsLibrary::underscoredToCamel($admin_application_name).'App'.$class_ending;
				}
				elseif ($rule == APP_RESOURCE_CONTAINER_PACKAGES) {
					$packages = self::getPackagesList();
					foreach ($packages as $package) {
						$possible_classes[] = coreNameUtilsLibrary::underscoredToCamel($package).'Pkg'.$class_ending;
					}
				}
				elseif (strpos($rule, 'applications/') === 0) {
					$app_name = str_replace('applications/', '', $rule);
					$possible_classes[] = coreNameUtilsLibrary::underscoredToCamel($app_name).'App'.$class_ending;
				}
				elseif (strpos($rule, 'packages/') === 0) {
					$pkg_name = str_replace('packages/', '', $rule);
					$possible_classes[] = coreNameUtilsLibrary::underscoredToCamel($pkg_name).'Pkg'.$class_ending;
				}
				else {
					die("Bad resource routing rule");
				}
			}	
			return $possible_classes;		
		}
		

		// TODO: Подумать над переносом в coreNameUtils
		public static function getResourceClass($resource_name, $resource_type) {
			$possible_classes = self::getPossibleClasses($resource_name, $resource_type);
			
			$class_name = null;
			foreach ($possible_classes as $class) {
				if (class_exists($class)) return $class;
			}

			return null;
		}
		
		

		public static function getSmarty() {
			if (!self::$smarty) {

				//self::loadLibrary('smarty/Smarty.Class');

				self::$smarty = new coreSmartyLibrary();

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
				
				self::$smarty->compile_dir = $compile_dir;				
				self::$smarty->cache_dir = $cache_dir;

				self::$smarty->caching = false;
			}
			return self::$smarty;
		}
		
		
		public static function getMailer() {
			$library_class = Application::getResourceClass('mailer', APP_RESOURCE_TYPE_LIBRARY);
			return new $library_class();
		}

		public static function getBreadcrumbs() {
			if (!self::$breadcrumbs) {
				self::loadLibrary('core/breadcrumbs');
				self::$breadcrumbs = new Breadcrumbs();
			}
			return self::$breadcrumbs;
		}
		
		
		public static function getMessageStack() {
			if (!self::$message_stack) {				
				self::$message_stack = new coreMessageStackLibrary();
			}
			return self::$message_stack;
		}
		
		
		public static function stackMessage($message, $type='message') {
			$message_stack = self::getMessageStack();
			$message_stack->add($message, $type);
		}
		
		public static function stackError($message) {
			$message_stack = self::getMessageStack();
			$message_stack->add($message, 'error');
		}
		
		public static function stackWarning($message) {
			$message_stack = self::getMessageStack();
			$message_stack->add($message, 'warning');
		}
		

		public static function getHost() {
			return self::$host;
		}

		// TODO: Переименовать
		public static function getSitePath() {
			return self::$site_root;
		}

		public static function getSiteUrl() {
			return self::$site_url;
		}

		public static function getApplicationName() {
			return self::$application_name;
		}

		public static function getSeoUrl($internal_url) {
			return UrlRewriter::internalToSeo($internal_url);
		}

		public static function getTempDirectory() {
			return isset(self::$config['temp_directory']) ? self::$config['temp_directory'] : '/temp/'.self::getApplicationName();
		}

		public static function getVarDirectory() {
			return isset(self::$config['var_directory']) ? self::$config['var_directory'] : '/var/'.self::getApplicationName();
		}

		protected static function detectMobileBrowser() {
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
	
	
	
