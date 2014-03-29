<?php

	
	class coreAdminDocumentModule extends coreAdminBaseModule {
		
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
				$item->link = str_replace('/admin/', '/', $item->link);
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
			$redirect_url = "/admin/{$this->getName()}?action=list&message=" . urldecode($message);
			Redirector::redirect($redirect_url);
						
		}
		
	}
	
	
	
	
	
	
	
	
	