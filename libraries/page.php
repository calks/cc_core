<?php

    class corePageLibrary {
        protected static $_instance = array();
        protected static $_accessibleProperties = array();
        protected static $_keyMetaAttributes = array();

        protected $_heading = '';
        protected $_title = '';
        protected $_title_static_part = '';
        protected $_title_delimeter = '';
        protected $_description = '';
        protected $_keywords = '';
        protected $_meta = '';

        protected $_stylesheets = array();
        protected $_scripts = array();
        protected $_literal = array();
        
        protected $_adding_order = array();

        public static function getInstance() {
            if (!self::$_instance)
                self::$_instance = new corePageLibrary();

            return self::$_instance;
        }

        private function __construct() {
            self::$_accessibleProperties = array('_heading', '_title', '_description');
            self::$_keyMetaAttributes = array('name', 'property', 'http-equiv');

            $this->_title = '';
            $this->_description = '';
            $this->_keywords = '';

            $this->_meta = array();
            $this->_stylesheets = array();
            $this->_scripts = array();
        }

        private function __clone() {
        }

        public function __set($property, $value) {
            $property = '_'.strtolower($property);
            if (in_array($property, self::$_accessibleProperties))
                $this->{$property} = $value;
        }

        public function __get($property) {
            $property = '_'.strtolower($property);
            return in_array($property, self::$_accessibleProperties) ? $this->{$property} : null;
        }

        public function addMeta(array $meta) {
            $key = '';
            foreach (self::$_keyMetaAttributes as $keyAttr) {
                if (isset($meta[$keyAttr]))
                    $key .= "{$keyAttr}=\"{$meta[$keyAttr]}\" ";
            }
            $key = md5($key);

            $this->_meta[$key] = $meta;
            
            $this->rememberOrder('_meta', $key);

            return $this;
        }
        
        
        public function addLiteral($string) {
            $key = md5(trim($string));
            if(isset($this->_literal[$key])) return $this; 
        	$this->_literal[$key] = $string;            
            $this->rememberOrder('_literal', $key);
            return $this;
        }
        

        public function setTitle($title) {
            $this->_title = $title;
        }

        public function setTitleStaticPart($static_part, $delimeter = '|') {
            $this->_title_static_part = $static_part;
            $this->_title_delimeter = $delimeter;
        }

        public function setDescription($description) {
            $this->_description = $description;
        }

        public function setKeywords($keywords) {
            $this->_keywords = $keywords;
        }

        
        protected function removeApplicationDomain($source) {
        	$site_url = Application::getSiteUrl();
        	$source = str_replace($site_url, '', $source);
        	return $source;
        }
        
        public function addScript($source, $type = 'text/javascript') {            
        	$source = $this->removeApplicationDomain($source);

            if (!isset($this->_scripts[$type])) {
            	$this->_scripts[$type] = array();	
            }
            
            if (in_array($source, $this->_scripts[$type])) return $this;

            $this->_scripts[$type][] = $source;
            $this->rememberOrder('_scripts', $type, count($this->_scripts[$type])-1);

            return $this;
        }


        public function addStylesheet($source) {
            $source = $this->removeApplicationDomain($source);
        	
            if(in_array($source, $this->_stylesheets)) return $this;

            $this->_stylesheets[] = $source;
           	$this->rememberOrder('_stylesheets', count($this->_stylesheets)-1);	
            
            return $this;
        }

        protected function prepareString($str) {
        	return strip_tags(htmlspecialchars($str, ENT_QUOTES, 'utf-8'));
        }
        
        
        protected function rememberOrder($array_name, $index_1, $index_2 = null) {
        	$this->_adding_order[] = array(
        		'array_name' => $array_name,
        		'index_1' => $index_1,
        		'index_2' => $index_2
        	);
        }
        
        
        public function compressStatic() {
        	$this->compressCss();
        	$this->compressJs();
        }
        
        protected function compressJs() {
        	$compressed_dir = Application::getTempDirectory() . '/static';
        	$compressed_dir_absolute = Application::getSitePath() . $compressed_dir;
        	
        	if (!is_dir($compressed_dir_absolute)) {
        		if (!@mkdir($compressed_dir_absolute, 0777, true)) {
        			die("Can't create dir $compressed_dir_absolute");
        		}
        	}
       		
        	$literal_scripts = array();
        	foreach($this->_literal as $k=>$content) {
        		$is_js = preg_match('/\s*<script\s+type=("|\')text\/javascript("|\')>(?P<code>.*)<\/script>/isU', $content, $matches);
        		if (!$is_js) continue;
        		$literal_scripts[$k] = trim($matches['code']);
        	}
        	
        	$js_filename = md5(implode('', $this->_scripts['text/javascript']) . implode('', array_keys($literal_scripts))) . '.js';
        	$js_path = "$compressed_dir/$js_filename";
        	$js_path_absolute = "$compressed_dir_absolute/$js_filename";
        	
        	if (!is_file($js_path_absolute)) {
        		$lock_name = 'generating compressed js' . md5(__FILE__);
        		if (!coreLockLibrary::set($lock_name)) return;
        	
	        	$f = fopen($js_path_absolute, 'w');
	        	if (!$f) die("Can't write to $js_path_absolute");
	        	
	        	foreach($this->_adding_order as $key=>$data) {
	        		$array_name = $data['array_name'];	        		
	        		if (!in_array($array_name, array('_literal', '_scripts'))) continue;	        		
	        		$index_1 = $data['index_1'];	        		
	        		if ($array_name=='_scripts' && $index_1 != 'text/javascript') continue;
	        		if ($array_name=='_literal' && !array_key_exists($index_1, $literal_scripts)) continue;	        		
	        		$index_2 = $data['index_2'];
	        		//unset($this->_adding_order[$key]);
	        		
	        		switch($array_name) {
	        			case '_scripts':
	        				$a = $this->$array_name;
	        				$path = $a[$index_1][$index_2];	        				
	        				$path_absolute = strpos($path, 'http') !== 0 ? Application::getSitePath() . $path : $path;        	
        					$content = file_get_contents($path_absolute);
        					break;
	        			case '_literal':
	        				$content = $literal_scripts[$index_1];	
	        				break;
	        		}
	        		
	        		fwrite($f, $content);
	        	}
	        	
	        	fclose($f);
	        	coreLockLibrary::remove($lock_name);
        	}
        	        	
        	foreach($this->_adding_order as $key=>$data) {
        		$array_name = $data['array_name'];	        		
        		if (!in_array($array_name, array('_literal', '_scripts'))) continue;	        		
        		$index_1 = $data['index_1'];	        		
        		if ($array_name=='_scripts' && $index_1 != 'text/javascript') continue;
        		if ($array_name=='_literal' && !array_key_exists($index_1, $literal_scripts)) continue;	        		
        		//$index_2 = $data['index_2'];
        		unset($this->_adding_order[$key]);
        	}        	
        	
        	$this->addScript($js_path);
        }
        
        
        public function compressCss() {
        	$compressed_dir = Application::getTempDirectory() . '/static';
        	$compressed_dir_absolute = Application::getSitePath() . $compressed_dir;
        	
        	if (!is_dir($compressed_dir_absolute)) {
        		if (!@mkdir($compressed_dir_absolute, 0777, true)) {
        			die("Can't create dir $compressed_dir_absolute");
        		}
        	}
        	
        	$css_filename = md5(implode('', $this->_stylesheets)) . '.css';
        	$css_path = "$compressed_dir/$css_filename";
        	$css_path_absolute = "$compressed_dir_absolute/$css_filename";
        	
        	
        	if (!is_file($css_path_absolute)) {
        		$lock_name = 'generating compressed css' . md5(__FILE__);
        		if (!coreLockLibrary::set($lock_name)) return;
        	
	        	$f = fopen($css_path_absolute, 'w');
	        	if (!$f) die("Can't write to $css_path_absolute");
	        	
	        	foreach($this->_stylesheets as $path) {
	        		fwrite($f, $this->prepareCss($path));
	        	}
	        	
	        	fclose($f);
	        	coreLockLibrary::remove($lock_name);
        	}
        	
        	$stylesheet_keys = array_keys($this->_stylesheets);
        	$first_key = array_shift($stylesheet_keys);
        	$this->_stylesheets = array(
        		$first_key => $css_path
        	);
        	
        	foreach ($this->_adding_order as $k=>$v) {
        		if ($v['array_name'] == '_stylesheets' && $v['index_1'] != $first_key) {
        			unset($this->_adding_order[$k]);
        		}
        	}
        	
        }
        
        protected function prepareCss($path) {
        	$base_dir = dirname($path);
        	
        	$path_absolute = strpos($path, 'http') !== 0 ? Application::getSitePath() . $path : $path;
        	
        	$content = file_get_contents($path_absolute);
        	if (preg_match_all('/(?<externals>url\s?\(.*\))/isU', $content, $matches, PREG_PATTERN_ORDER)) {
        		$externals = array_unique($matches['externals']);
        		$replacements = array();
        		foreach($externals as $e) {
        			$e_cleared = trim(substr($e, 3), " ()'\"");
        			if (strpos($e_cleared, 'http') === 0) continue;
        			$real_path = $this->getRealPath($base_dir, $e_cleared);
        			$replacements[$e] = "url($real_path)";
        		}
        		if ($replacements) {
        			$content = str_replace(array_keys($replacements), $replacements, $content);
        		}
        	}
        	
        	return $this->stripStatic($content);
        }
        
        
        protected function getRealPath($base_dir, $relative_path) {
        	$relative_path = explode('/', $relative_path);
        	foreach ($relative_path as $idx=>$part) {
        		if ($part != '..') continue;
        		$base_dir = dirname($base_dir);
        		unset($relative_path[$idx]);
        	}
        	
        	return $base_dir . '/' . implode('/', $relative_path);
        }
        
        protected function stripStatic($content) {
        	$content = preg_replace('/\/\*.*\*\//isU', '', $content);
        	$content = preg_replace('/\s+/', ' ', $content);

        	$replacements = array();
        	$replacements['; '] = ';';
        	$replacements[' ;'] = ';';
        	$replacements[' }'] = '}';
        	$replacements['} '] = '}';
        	$replacements[' {'] = '{';
        	$replacements['{ '] = '{';
        	$replacements[' ,'] = ',';
        	$replacements[', '] = ',';
        	$replacements['> '] = '>';
        	$replacements[' >'] = '>';
        	$replacements[': '] = ':';
        		
        	$content = str_replace(array_keys($replacements), $replacements, $content);
        	
        	return $content;
        }
        
        
        public function getHtmlHead() {
            $out = "<head>\n";
            $title = $this->getTitle();
            $title = $this->prepareString($title);

            $out .= "\t<title>$title</title>\n";
            
            $keywords = $this->prepareString($this->_keywords);
            $out .= "\t<meta name=\"keywords\" content=\"{$keywords}\">\n";

            $description = $this->prepareString($this->_description);
            $out .= "\t<meta name=\"description\" content=\"{$description}\">\n";

            foreach ($this->_adding_order as $item_data) {
            	$array_name = $item_data['array_name'];
            	$index_1 = $item_data['index_1'];
            	$index_2 = $item_data['index_2'];
            	
            	$array = $this->$array_name;
            	$item = is_null($index_2) ? $array[$index_1] : $array[$index_1][$index_2];
            	
            	switch ($array_name) {
            		case '_meta':
                		$out .= "\t<meta ";
                		foreach ($item as $attr => $value) {
                			$out .= "{$attr}=\"{$value}\" ";                			
                		}                    
                		$out .= ">\n";
            			break;
            		case '_stylesheets':
            			$out .= "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"{$item}\">\n";            			
            			break;
            		case '_literal':
            			$out .= "\t$item\n";            			
            			break;
            		case '_scripts':
            			$out .= "\t<script type=\"$index_1\" src=\"$item\"></script>\n";          			
            			break;
            		default:
            			die("Don't know what $array_name is");	
            			
            	}
            }
            
            $out .= "</head>\n";

            return $out;
        }

        public function getTitle( $useStaticPart = true )
        {
            $title = $this->_title;
            if( $useStaticPart && $this->_title_static_part) {
                if( $title )
                    $title .= $this->_title_delimeter ? " $this->_title_delimeter " : ' ';
                $title .= $this->_title_static_part;
            }

            return $title;
        }
        
        public function getMetaDescription() {
            $description = $this->prepareString($this->_description);
            return "\t<meta name=\"description\" content=\"{$description}\">\n";        	
        }
        
        public function getMetaKeywords() {
            $keywords = $this->prepareString($this->_keywords);
            return "\t<meta name=\"keywords\" content=\"{$keywords}\">\n";        	
        }
        
    }
