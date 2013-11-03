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
            $this->_literal[] = $string;            
            $this->rememberOrder('_literal', count($this->_literal)-1);
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
