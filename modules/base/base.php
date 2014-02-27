<?php

	class coreBaseModule {

		public function run($params=array()) {
			return '';
		}

        protected function getModuleType() {
            return 'module';
        }

        protected function getName() {
        	
        	$class = get_class($this);
            //$class = strtolower($class);
            $module_type = $this->getModuleType();
            
            if (preg_match('/(?P<container_complex_name>(?P<container_name>[a-zA-Z0-9]+)(?P<container_type>App|Pkg)|core)(?P<module_name>[a-zA-Z0-9]+)'.ucfirst($module_type).'/', $class, $matches)) {            	
            	return coreNameUtilsLibrary::camelToUnderscored($matches['module_name']);
            }
            
            $class = str_replace(ucfirst($module_type), '', $class);

            $application_name = Application::getApplicationName();
            $application_name_camel_case = str_replace(' ', '', ucwords(str_replace('_', ' ', $application_name))); 
            
            $class = str_replace($application_name, '', $class);
            $class = str_replace($application_name_camel_case, '', $class);
            
            $out = '';
            while($letter = substr($class, 0, 1)) {
            	if ($out && $letter != strtolower($letter)) $out .= '_';
            	$out .= strtolower($letter);
            	$class = substr($class, 1);
            }
            
            return $out;
        }

        protected function terminate() {        	        	
			return Application::runModule('page404');                
        }

        protected function runTaskByParams($params) {
            $task = array_shift($params);
            if (!trim($task)) return $this->terminate();
            $method_name = 'task'.ucfirst(strtolower($task));
            if (!method_exists($this, $method_name)) return $this->terminate();
            return call_user_func(array($this, $method_name), $params);
        }

        public function baseDir() {
            $class = $this->getName();
            $module_type = $this->getModuleType();
            $method_name = 'get'.ucfirst(strtolower($module_type)).'Path';
            $module_path = call_user_func(array('Application', $method_name), $class);
            return dirname($module_path);
        }

        
        
        public function getStaticFileUrl($path_relative_to_module) {        	
        	$path_relative_to_module = trim($path_relative_to_module, ' /');        	
        	return coreResourceLibrary::getFirstFilePath($this->getModuleType(), $this->getName(), '/static/' . $path_relative_to_module);
        }
        
        public function getTemplatePath($template_name = '') {
            if (!$template_name) $template_name = $this->getName();
            return coreResourceLibrary::getFirstFilePath($this->getModuleType(), $this->getName(), "/templates/$template_name.tpl");       
        }

        protected function getModuleDirectoryName() {
            switch ($this->getModuleType()) {
            case 'module':
                return 'modules';
            case 'block':
                return 'blocks';
            default:
                die('Can\'t determine type of myself');
            }
        }
		
	}