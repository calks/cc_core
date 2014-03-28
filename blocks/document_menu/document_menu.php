<?php

	define('INDEX_PAGE_ID', 1);

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
            $data = $this->getMenu($menu_type);
                        
            $smarty = Application::getSmarty();
            $smarty->assign('menu', $data);
            
            return $smarty->fetch($template_path);
        }

        protected function getLink($item) {
        	$url = $item->open_link ? $item->open_link : $item->url;
        	if(preg_match( "/^http:\/\//i", $url)) {
        		return $url;
        	}  
        	$url = trim($url, ' /');
        	return $item->open_link ? Application::getSeoUrl("/$url") : Application::getSeoUrl("/document/$url");
        }

        
        protected function getMenu($type, $parent_id=0, $language_id=CURRENT_LANGUAGE) {        	
            $db = Application::getDb();

            $doc = Application::getEntityInstance('document');

            $table = $doc->getTableName();
			
            if (!is_array($parent_id)) $parent_id = array($parent_id);
            elseif (!$parent_id) return array();
                        
            foreach ($parent_id as &$id) $id = (int)$id;
            $parent_id = implode(',', $parent_id);

            switch ($this->menu_type) {
                case 'top':
                    $menu = 'menu & ' . SITE_MENUS_TOP_MENU;
                    break;
                case 'footer':
                    $menu = 'menu & ' . SITE_MENUS_FOOTER_MENU;
                    break;
                default:
                    return array();
            }

            $subquery = $doc->get_content_subquery($language_id);

            $query = "
                SELECT 
                	id, parent_id, open_link, url, open_new_window, title,
                	IF(category=0, 1, 0) AS important
                FROM $table JOIN $subquery AS content ON content.document_id = $table.id
                WHERE parent_id IN($parent_id) AND active = 1 AND $menu
                GROUP BY document_id ORDER BY seq ASC
            ";
            
            $objects_raw = $db->executeSelectAllObjects($query);
            
            $objects = array();
            $id_s = array();
            foreach($objects_raw as $obj) {
                $id_s[] = $obj->id;
                $obj->children = array();
                $objects[$obj->id] = $obj;                
            }

            if (!$parent_id) {
                $children = $this->getMenu($type, $id_s);

                foreach ($children as $child) {
                    $objects[$child->parent_id]->children[] = $child;
                }
                
                $current_page_url = '/' . trim(Router::getSourceUrl(), ' /');
                
                foreach ($objects as &$object) {
                    if($object->open_link != '') $url = $object->open_link;
                    else $url = $object->url;

                    $children = $object->children;

                    foreach ($children as &$child) {
                        $child->link = $this->getLink($child);
                        $child->active = $child->link == $current_page_url;
                    }

                    $object->children = $children;
                    $object->link = $this->getLink($object);
                    $object->active = $object->link == $current_page_url;
                }
            }
     
            return $objects;
        }

    }
