<?php

	class corePagenavBlock extends coreBaseBlock {
		
		protected $page_link_template;
		protected $items_total = null;
		protected $items_per_page;
		protected $current_page;
		
		
		protected function getDelta() {
			return 3;
		}
				
		
		protected function getPageNumbers() {
			$delta = $this->getDelta();
			$range_size = $delta*2+1;
									
			$out = array();  
			
			$start = $this->current_page - $delta < 1 ? 1 : $this->current_page - $delta;			 
			$end = $start+$range_size-1 > $this->total_pages ? $this->total_pages : $start+$range_size-1;
			
			if ($end-$start+1 < $range_size) {
				$new_start = $start - ($range_size - $end + $start -1);
				$start = $new_start>1 ? $new_start : 1;
			}
			
			for($i=$start; $i<=$end; $i++) {				
				$out[] = $i;
			}	
			
			return $out;
			
		}
		
		public function render($params = array()) {
									
			if (!$this->page_link_template) return 'No page link template';
			
			// null будет тогда и только тогда, когда мы не инициализировали
			// items_total. items_total равное 0 означает просто отсутствие элементов в выдаче 
			if (is_null($this->items_total)) return 'No items total';
			if ($this->items_total <= $this->items_per_page) return '';			
			
			if (!$this->items_per_page) return 'No items per page';
						
			$this->total_pages = ceil($this->items_total/$this->items_per_page);
			
			if ($this->current_page > $this->total_pages || $this->current_page < 1) return 'current page is out of page range';
			
			$page_numbers = $this->getPageNumbers();
			if (!$page_numbers) return '';
			
			
			$page_links = array();
			
			$prev = $this->current_page == 1 ? 1 : $this->current_page-1;
			$next = $this->current_page == $this->total_pages ? $this->total_pages : $this->current_page+1;
			
			$show_prev_next = $this->getPrevNextVisibility(); 
			
			if ($show_prev_next) {
				$page_links[] = $this->getLinkObj(1, '&lt;&lt;', 'to_first');
				$page_links[] = $this->getLinkObj($prev, '&lt;', 'prev');
			}
			
			
			foreach($page_numbers as $i) {								
				$page_links[] = $this->getLinkObj($i, $i);
			}
			
			if ($show_prev_next) {
				$page_links[] = $this->getLinkObj($next, '&gt;', 'next');
				$page_links[] = $this->getLinkObj($this->total_pages, '&gt;&gt;', 'to_last');
			}

			$smarty = Application::getSmarty();
			$smarty->assign('page_links', $page_links);
			$smarty->assign('current_page', $this->current_page);
			$smarty->assign('total_pages', $this->total_pages);
			$template_path = $this->getTemplatePath();
			
			return $smarty->fetch($template_path);
			
		}
		
		protected function getPrevNextVisibility() {
			$page_numbers = $this->getPageNumbers();
			return count($page_numbers) < $this->total_pages;
			
		}
		
		protected function getSpacer($caption='...') {
			$obj = new stdClass();
			$obj->caption = $caption;
			$obj->link = '#';
			$obj->disabled = true;
			$obj->type = 'spacer';
			
			return $obj;
		}
		
		protected function getLinkObj($page_num, $caption, $type='normal') {
			$page_num = (int)$page_num;
			
			$link = str_replace('%page%', $page_num, $this->page_link_template);
			
			$obj = new stdClass();
			$obj->caption = $caption;
			$obj->link = Application::getSeoUrl($link);
			$obj->disabled = $page_num == $this->current_page;
			$obj->type = $type;
			
			return $obj;
		}
		
		public function setPageLinkTemplate($page_link_template) {
			$this->page_link_template = $page_link_template;
		}				
		
		public function setItemsTotal($items_total) {
			$this->items_total = (int)$items_total;
		}
		
		public function setItemsPerPage($items_per_page) {			
			$this->items_per_page = (int)$items_per_page;
		}
		
		public function setCurrentPage($current_page) {
			$this->current_page = (int)$current_page;
		}
		
	}