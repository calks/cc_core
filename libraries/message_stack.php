<?php

	class coreMessageStackLibrary {
		
		protected $messages;
		
		protected function getSessionKey() {
			return 'message_stack' . md5(__FILE__);
		}
		
		protected function loadList() {
			if (!is_null($this->messages)) return;
			$session_key = $this->getSessionKey();
			$this->messages = isset($_SESSION[$session_key]) ? unserialize($_SESSION[$session_key]) : array();  
		}
		
		protected function saveList() {
			$session_key = $this->getSessionKey();
			$_SESSION[$session_key] = serialize($this->messages);
		}
		
		public function add($message, $type) {
			$this->loadList();
			$this->messages[] = array(
				'type' => $type,
				'message' => $message
			);
			$this->saveList();
		}
		
		public function clear() {
			$this->loadList();
			$this->messages = array();
			$this->saveList();
			
		}
		
		public function getList() {
			$this->loadList();
			return $this->messages;
		}
		
		public function getTexts($message_type) {
			$this->loadList();
			$out = array();
			foreach ($this->messages as $m) {
				if ($m['type'] != $type) continue;
				$out[] = $m['messages'];
			}
			return $out;
		}
		
	}