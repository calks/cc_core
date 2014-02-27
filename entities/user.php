<?php

	define('USER_ROLE_ADMIN', 1);

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
			$form->addField(new THiddenField('id'));
			$form->addField(new TEditField('name', '', 30, 255));
			$form->addField(new TEditField('family_name', '', 30, 255));
			$form->addField(coreFormElementsLibrary::get('checkbox_collection', 'roles', array(
				'options' => $this->getRoleSelect()
			)));			
			$form->addField(new TEditField('email', '', 30, 100));
			$form->addField(new TEditField('login', '', 30, 100));
			//$form->addField(new TEditField('pass', '', 30, 100));

			$form->addField(new TCheckboxField('active', ''));
			$form->addField(coreFormElementsLibrary::get('checkbox_collection', 'roles', array(
				'options' => $this->getRoleSelect()
			)));
			
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
				$item->roles_str = array();
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
				$mapping[$d->user_id]->roles[] = $d->role_id; 
				$mapping[$d->user_id]->roles_str[] = $d->role_name;
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
				foreach ($this->roles as $role_id) {
					$role_id = (int)$role_id;
					if (!$role_id) return;
					$values[] = "($user_id, $role_id)";					
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
		
		
	}

	
	
	