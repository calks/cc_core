<?php

	
	class coreAdminDocumentModule extends coreCrudBaseModule {
		
		protected function getObjectName() {
			return 'document';
		}
		
		public function getPageTitle() {
			return 'Страницы';
		}
		
		
		protected function taskList($params) {
			$smarty = Application::getSmarty();
			$smarty->assign('line_template_path', $this->getTemplatePath('list_line'));
			return parent::taskList();				
		}
		
		protected function getPreservedFields() {
			$fields = array('protected');
			if (isset($this->original_objects[0]) && $this->original_objects[0]->protected) $fields[] = 'url';
			return $fields;
		}
		
		protected function afterListLoad($list) {
			foreach($list as $item) {
				$item->moveup_link = "/admin/{$this->getName()}?action=moveup&amp;ids[]=$item->id";	
				$item->movedown_link = "/admin/{$this->getName()}?action=movedown&amp;ids[]=$item->id";
				$item->edit_link = "/admin/{$this->getName()}?action=edit&amp;ids[]=$item->id";
				$item->delete_link = "/admin/{$this->getName()}?action=delete&amp;ids[]=$item->id";
				$this->afterListLoad($item->children);
			}
		}
		
		protected function createForm($object) {
			parent::createForm($object);
			$link_type_options = array(
				'page_itself' => 'Сама страница или раздел',
				'alias' => 'Ссылка'
			);
			
			$type_field = coreFormElementsLibrary::get('radio', 'link_type');
			$type_field->setOptions($link_type_options);
			$this->form->addField($type_field);
			$type_field->setValue($object->open_link ? 'alias' : 'page_itself');
		}

		
		protected function taskEdit() {
			$page = Application::getPage();
			$page->addScript($this->getStaticFilePath('/type_switch.js'));
			return parent::taskEdit();
		}
		
		protected function updateObjectFromRequest($object) {
			parent::updateObjectFromRequest($object);
			$link_type = $this->form->getValue('link_type');
			if ($link_type == 'page_itself') {
				$object->open_link = '';
			}
			elseif($link_type == 'alias') {
				$object->url = '';
				$object->meta_title = '';
				$object->content = '';
				$object->meta_desc = '';
				$object->meta_key = '';
			}
		}
		
		
		protected function validateObject($object) {
			parent::validateObject($object);
			
			$link_type = $this->form->getValue('link_type');
			
			if ($link_type == 'page_itself') {
				if (!trim($object->url)) {
					$this->errors[] = "Необходимо задать URL";
				}
				else {					
					$existing = $object->loadToUrl($object->url);
					if ($existing && $existing->id != $object->id) {
						$this->errors[] = "Указанное значение URL уже используется для другой страницы";
					}
				}				
			}
			elseif($link_type == 'alias') {
				if (!trim($object->open_link)) {
					$this->errors[] = "Необходимо задать ссылку на страницу";
				}
			}
			
		}
		
		
		protected function taskMove($params, $direction) {
			$object = $this->objects[0];
			$table = $object->getTableName();
			$db = Application::getDb();			
			if($direction=='up') {
				$sql = "
					SELECT id, seq FROM $table
					WHERE parent_id=$object->parent_id
					AND seq<$object->seq
					ORDER BY seq DESC
					LIMIT 1
				";
			}
			elseif($direction=='down') {
				$sql = "
					SELECT id, seq FROM $table
					WHERE parent_id=$object->parent_id
					AND seq>$object->seq
					ORDER BY seq ASC
					LIMIT 1
				";				
			}
			else return $this->terminate();
			
			$neighbour = $db->executeSelectObject($sql);
			if ($neighbour) {
				$db->execute("
					UPDATE $table SET seq=$object->seq
					WHERE id=$neighbour->id
				");			
				$db->execute("
					UPDATE $table SET seq=$neighbour->seq
					WHERE id=$object->id
				");
				$this->normalizeSeq($object->parent_id);
			}
			
			
			$message = 'Объект перемещен ';// . $direction=='up' ? 'выше' : 'ниже';
			Application::stackMessage($message);
			$redirect_url = "/admin/{$this->getName()}?action=list";
			Redirector::redirect($redirect_url);
						
		}
		
	}
	
	
	
	
	
	
	
	
	