<?php


    class coreDocumentMenuBlock extends coreBaseBlock {    	
    	
    	protected $menu_type;

    	public function setMenuType($menu_type) {
    		$this->menu_type = $menu_type;
    	}
    	
        public function render() {
        	$allowed_types = array('top', 'footer');
        	if (!in_array($this->menu_type, $allowed_types)) {
        		return $this->terminate();	
        	}          
            $template_path = $this->getTemplatePath($this->menu_type);	
            if (!$template_path) {
            	return $this->terminate();
            } 
            $data = $this->getMenu($this->menu_type);
            $smarty = Application::getSmarty();
            $smarty->assign('menu', $data);            
			$smarty->assign('block', $this);
			
			$this->html = $smarty->fetch($template_path);
			return $this->html;
            
        }

        protected function getLink($item) {
        	$url = $item->open_link ? $item->open_link : $item->url;
        	if(preg_match( "/^http:\/\//i", $url)) {
        		return $url;
        	}  
        	$url = trim($url, ' /');
        	return $item->open_link ? "/$url" : Application::getSeoUrl("/textpage/$url");
        }


        protected function prepareMenu(&$documents) {

        	$current_page_url = '/' . trim(Router::getSourceUrl(), ' /');
        	
			foreach ($documents as $doc) {
				$doc->link = $this->getLink($doc);
				$doc->active = $doc->link == $current_page_url;				
				$this->prepareMenu($doc->children);
			}
        }
        
        protected function getMenu($type, $language_id=CURRENT_LANGUAGE) {        	
            $db = Application::getDb();

            $doc = Application::getEntityInstance('document');

            $table = $doc->getTableName();
            $content_table = $doc->get_content_table_name();
            
            switch ($type) {
                case 'top':
                    $menu = 'menu & ' . SITE_MENUS_TOP_MENU;
                    break;
                case 'footer':
                    $menu = 'menu & ' . SITE_MENUS_FOOTER_MENU;
                    break;
                default:
                    return array();
            }

            $params['where'][] = "$table.is_active=1";
            $params['where'][] = $menu;
            $params['fields'] = array(
            	"$table.id", 
            	"$table.parent_id",
            	"$table.open_link",
            	"$table.url",
            	"$table.open_new_window",
            	"content.title",
                "0 AS `important`"
            );
            $params['fieldlist_mode'] = 'specified_only';
            
            $documents = $doc->load_list($params);
            
            $this->prepareMenu($documents);
            
            return $documents;
            
        }

    }
