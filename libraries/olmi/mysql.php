<?php

	class MysqlDatabase {
	
		
		protected $connection;		
		protected $last_error;
		protected $last_failed_query;
		protected $show_errors = true;
		

		public function __construct($host, $database, $user, $password) {			
			$this->connection = @mysqli_connect($host, $user, $password, $database);
			if (!$this->connection) $this->handleError("Failed to connect to database");
		}
	
		
		protected function executeQuery($query) {			
			$res = @mysqli_query($this->connection, $query);
			
			if (!$res) {
				$this->handleError($query);
			}
			
			return $res;
		}
		
	
		public function setShowErrors($show_errors) {
			$this->show_errors = (bool)$show_errors;			
		}
		
		
		public function getShowErrors() {
			return $this->show_errors;			
		}
		
		
		public function executeScalar($query) {
			$resultset = $this->executeQuery($query);
			if ($resultset) {
				$row = mysqli_fetch_row($resultset);
				mysqli_free_result($resultset);
				return $row ? reset($row) : null;
			}
			else {
				return false;
			}
		}
	
		
		public function executeSelectObject($query) {
			$resultset = $this->executeQuery($query);
			if ($resultset) {
				$object = mysqli_fetch_object($resultset);
				mysqli_free_result($resultset);
				return $object ? $object : null;
			}
			else {
				return false;
			}
		}
			

		public function executeSelectAllObjects($query) {
			$resultset = $this->executeQuery($query);
			if ($resultset) {
				$out = array();
				while($object = mysqli_fetch_object($resultset)) {
					$out[] = $object;
				}
				mysqli_free_result($resultset);
				return $out;
			}
			else {
				return false;	
			}
			
		}
	

		public function executeSelectColumn($query) {
			$resultset = $this->executeQuery($query);
			if ($resultset) {
				$out = array();
				while($row = mysqli_fetch_array($resultset)) {
					$out[] = $row[0];
				}
				mysqli_free_result($resultset);
				
				return $out;
			}
			else {
				return false;	
			}
			
		}
	
		
		public function execute($query) {
			$res = $this->executeQuery($query);
			if (!$res) {
				$this->handleError(mysqli_error($this->connection), $query);
			} 
			
			return (bool)$res;
		}
	

		protected function handleError($errmsg, $query=null) {
			$this->last_error = $errmsg;
			$this->last_failed_query = $query;
			
			if ($this->show_errors) {
				print "<div class=\"debug\">";
				print "<h2>Database error</h2>\n";
				if ($query) {
					print "<p>" . $this->$query . "</p>";
					print "<br>";
				}
				print "<p>" . htmlspecialchars($errmsg) . "</p>";
				print "</div>";
				die();
			}
		}
	

		
		public function getLastAutoIncrementValue() {
			return mysqli_insert_id($this->connection);
		}
	
		
		public function getAffectedRows() {
			return mysqli_affected_rows($this->connection);
		}
	
		
		public function getLastError() {
			return $this->last_error;
		}
	

		public function escapeString($value) {
			return mysqli_real_escape_string($this->connection, $value);
		}
	
	}
	
	
