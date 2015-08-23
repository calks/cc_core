<?php

	
	class coreUserEntity extends coreBaseEntity {		
		
		public $name;		
		public $family_name;
		public $email;
		public $login;
		public $pass;
		public $active;
		
		public function __construct() {			
			parent::__construct();
			$this->roles = array();
		}

		public function getTableName() {
			return 'user';
		}
		
		public function getRolesTableName() {
			return 'user_role';
		}
		
		public function getRolesCouplingTableName() {
			return 'user_role_coupling';
		}
		
		public function mandatory_fields() {
			return array(
				'name' => 'Имя',
				'email' => 'Email',
				'login' => 'Логин'
			);
		}

		public function unique_fields() {
			return array(
				'email' => 'Email',
				'login' => 'Логин'
			);
		}

		public function order_by() {
			return '`active` ASC, `id` DESC';
		}

		public function make_form(&$form) {
			$form->addField(coreFormElementsLibrary::get('hidden', 'id'));
			$form->addField(coreFormElementsLibrary::get('text', 'name'));
			$form->addField(coreFormElementsLibrary::get('text', 'family_name'));			
			$form->addField(coreFormElementsLibrary::get('checkbox_collection', 'roles')->setOptions($this->getRoleSelect()));			
			$form->addField(coreFormElementsLibrary::get('text', 'email'));
			$form->addField(coreFormElementsLibrary::get('text', 'login'));

			$form->addField(coreFormElementsLibrary::get('checkbox', 'active'));
			
			return $form;
		}
		
		public function validate() {
			$errors = parent::validate();
			if (!email_valid($this->email)) {
				$errors[] = 'Неправильный Email';
			}
			
			if (!$this->pass) {
				$errors[] = 'Не задан пароль';
			}
			
			return $errors;
		}
		
		
		
		public function load_list($params=array()) {
			$table = $this->getTableName();
			$params['fields'][] = "CONCAT($table.name, ' ', $table.family_name) AS user_name";
			
			$list = parent::load_list($params);
			
			$mapping = array();
			foreach($list as $item) {
				$mapping[$item->id] = $item;
				$item->roles = array();
			}
			
			if (!$mapping) return $list;
			
			$ids = array_keys($mapping);
			$ids = implode(',', $ids);
			
			$roles_table = $this->getRolesTableName();
			$roles_coupling_table = $this->getRolesCouplingTableName();
			
			$db = Application::getDb();
			
			$data = $db->executeSelectAllObjects("
				SELECT 
					$roles_coupling_table.user_id,
					$roles_coupling_table.role_id,
					$roles_table.name AS role_name
				FROM 
					$roles_coupling_table 
					LEFT JOIN $roles_table ON $roles_table.id=$roles_coupling_table.role_id
				WHERE 
					$roles_coupling_table.user_id IN($ids) 
			");
					
			foreach ($data as $d) {
				$mapping[$d->user_id]->roles[$d->role_id] = $d->role_name;
			}
			
			return $list;			
		}
				
		public function save() {
			$user_id = parent::save();
			if ($user_id) {
				$roles_coupling_table = $this->getRolesCouplingTableName();
				$db = Application::getDb();
				$db->execute("
					DELETE FROM $roles_coupling_table
					WHERE user_id = $user_id
				");
				
				$values = array();
				foreach ($this->roles as $role_id => $role_name) {
					$role_id = addslashes($role_id);
					$values[] = "($user_id, '$role_id')";					
				}
				
				$values = array_unique($values);
				if ($values) {
					$values = implode(',', $values);
					
					$db->execute("
						INSERT INTO $roles_coupling_table (user_id, role_id) VALUES $values
					");
				}
			}
			return $user_id;
		}
		
		public function getRoleSelect($null_item=false) {
			$out = get_empty_select($null_item);
			
			$roles_table = $this->getRolesTableName();
			$db = Application::getDb();
			$data = $db->executeSelectAllObjects("
				SELECT * FROM $roles_table
			");
			
			foreach($data as $d) $out[$d->id] = $d->name;
			
			return $out;
		}
		
		public function getIdByEmail($email) {
			$table = $this->getTableName();
			$db = Application::getDb();
			$email = addslashes($email);
			$sql = "
				SELECT id FROM $table
				WHERE email = '$email'
			";
			return $db->executeScalar($sql);
		}
		
		
		public function encriptPassword($password) {
			return md5($password); 
		}
		
		
		public function setPassword($password) {			
			$this->pass = $this->encriptPassword($password);
		}
		
		
		protected function generateRoleName($role) {
			$role = str_replace('_', ' ', $role);
			$out = array();
			foreach (explode(' ', $role) as $w) {
				$w = trim($w);
				if ($w) $out[] = ucfirst(strtolower($w));
			}
			return implode(' ', $out);
		}
		
		public function addRole($role) {
			if (!$this->hasRole($role)) {
				$role_options = $this->getRoleSelect();
				if (!isset($role_options[$role])) {
					$roles_table = $this->getRolesTableName();
					$role_safe = addslashes($role);
					$role_name = $this->generateRoleName($role);
					$role_name_safe = addslashes($role_name);
					
					$db = Application::getDb();
					$data = $db->execute("
						INSERT INTO $roles_table
						(`id`, `name`) VALUES ('$role_safe', '$role_name_safe')
					");					
				}
				else {
					$role_name = $role_options[$role];
				}
				$this->roles[$role] = $role_name;
			}
		}
		
		public function removeRole($role) {
			if ($this->hasRole($role)) {				
				unset($this->roles[$role]);				
			}
			
		}
		
		public function hasRole($role) {
			return array_key_exists($role, $this->roles);
		}
		
	}

	
	
	