<?php

	class coreLockLibrary {
		
		protected static $locks = array();
		
		protected static function getTableName() {
			return 'locks';
		}
		
		public static function wait($name, $timeout) {
			while (!self::set($name, $timeout)) {
				sleep(1);
			}
		}
		
		public static function isLocked($name) {
			self::removeExpired();
			
			$db = Application::getDB();
			$name = addslashes($name);
			$table = self::getTableName();
			
			return (bool)$db->executeScalar("
				SELECT COUNT(*) FROM $table
				WHERE name='$name' 
			");
		}
		
		protected function removeExpired() {
			$db = Application::getDB();
			$table = self::getTableName();
			
			$db->execute("
				DELETE FROM $table
				WHERE created < DATE_SUB(NOW(), INTERVAL timeout SECOND) 
			");
		}
		
		public static function set($name, $timeout=6) {
			self::removeExpired();
			
			$db = Application::getDB();
			$name = addslashes($name);
			$timeout = (int)$timeout;
			$table = self::getTableName();
			
			$old_show_errors = $db->getShowErrors();
			$db->setShowErrors(0);
			$db->execute("
				INSERT INTO $table (
					name, 
					created,
					timeout
				) VALUES (
					'$name',
					NOW(),
					$timeout
				)
			");
			$db->setShowErrors($old_show_errors);
					
			if ($db->getAffectedRows() < 1) return false;
			self::$locks[$name] = 1;
			return true;
		}
		
		public static function remove($name) {
			if (!isset(self::$locks[$name])) return false;			
			$db = Application::getDB();
			$name = addslashes($name);
			$table = self::getTableName();
			
			$db->execute("
				DELETE FROM $table
				WHERE name='$name' 
			");
			
			unset(self::$locks[$name]); 
			return true;
		}
		
		
	}