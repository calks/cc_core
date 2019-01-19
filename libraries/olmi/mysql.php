<?php

class MysqlDatabase {
  var $db;
  var $dbhost;
  var $dbname;
  var $dbuser;
  var $dbpassword;
  var $showErrors;
  var $lastError;
  var $lastErrorQuery;
  var $new_link;

  /**
   * @constructor
   * @param string $host
   * @param string $database
   * @param string $user
   * @param string $password
   * @param bool $showErrord
   * @param bool $dieOnError
   */
  function MysqlDatabase($host, $database, $user, $password, $showErrors = TRUE, $dieOnError = TRUE, $new_link = NULL) {
    $this->dbhost = $host;
    $this->dbname = $database;
    $this->dbuser = $user;
    $this->dbpassword = $password;
    $this->showErrors = $showErrors;
    $this->new_link = $new_link;

    if ($host && $database) {
      $this->connect($dieOnError);
    }
  }

  /**
   * Connects to the database
   * @param bool $dieOnError
   * @return bool
   */
  function connect($dieOnError) {
    $this->db = @mysql_connect($this->dbhost, $this->dbuser, $this->dbpassword, $this->new_link);
    if (!$this->db) {
      if (!(function_exists('MYSQL_CONNECT_ERROR_HANDLER')
            && MYSQL_CONNECT_ERROR_HANDLER(mysql_error())
            && ($this->db = @mysql_connect($this->dbhost, $this->dbuser, $this->dbpassword, $this->new_link))))
      {
        $this->handleError(mysql_error(), "mysql_connect");
        if ($dieOnError) {
          die();
        }
        return FALSE;
      }
    }
    if (mysql_select_db($this->dbname, $this->db)) {
      if (defined('MYSQL_POSTCONNECT_QUERY')) {
        mysql_query(constant('MYSQL_POSTCONNECT_QUERY'), $this->db);
      }
      return TRUE;
    }
    else {
      $this->handleError(mysql_error(), "mysql_select_db");
      if ($dieOnError) {
        die();
      }
      return FALSE;
    }
  }

  /**
   * Creates new MysqlDatabase object and attaches it to the specified connection.
   * @param resource $connection
   * @return MysqlDatabase
   * @static
   */
  function getInstance($connection) {
    $db = new MysqlDatabase(NULL, NULL, NULL, NULL);
    $db->setConnection($connection);
    return $db;
  }

  /**
   * Sets connection
   * @param resource $connection
   * @return void
   */
  function setConnection($connection) {
    $this->db = $connection;
  }

  /**
   * Executes SQL select query
   * @param string $query
   * @return resource
   */
  function executeQuery($query) {
    if (!($res = mysql_query($query, $this->db)))
      $this->handleError(mysql_error(), $query);
    return $res;
  }

  /**
   * Free all memory associated with the result identifier.
   * @param resource $res_id
   * @return void
   */
  function closeResultSet($res_id) {
    mysql_free_result($res_id);
  }

  /**
   * Returns a result row as an object
   * @param resource $res_id
   * @return object
   */
  function fetchObject($res_id) {
    return mysql_fetch_object($res_id);
  }

  /**
   * Returns a result row as an enumerated array
   * @param resource $res_id
   * @return array
   */
  function fetchRow($res_id) {
    return mysql_fetch_row($res_id);
  }

  /**
   * Returns a result row as an associative array
   * @param resource $res_id
   * @return array
   */
  function fetchAssocArray($res_id) {
    return mysql_fetch_array($res_id, MYSQL_ASSOC);
  }


  /**
   * Returns scalar value
   * @param string $query
   * @return mixed
   */
  function executeScalar($query) {
    $resultSet = $this->executeQuery($query);
    if ($resultSet) {
      $row = $this->fetchRow($resultSet);
      if ($row) {
        return $row[0];
      }
    }
    //TODO � ������ ������ ��������� ������� � ���� �������.
    return NULL;
  }

  /**
   * Returns database row as object
   * @param string $query
   * @return object
   */
  function executeSelectObject($query) {
    $resultSet = $this->executeQuery($query);
    if ($resultSet) {
      $row = $this->fetchObject($resultSet);
      mysql_free_result($resultSet);
      if ($row) {
        return $row;
      }
    }
    //TODO � ������ ������ ��������� ������� � ���� �������.
    return NULL;
  }

  /**
   * Returns database row as an enumerated array
   * @param string $query
   * @return array
   */
  function executeSelectRow($query) {
    $resultSet = $this->executeQuery($query);
    if ($resultSet) {
      $row = $this->fetchRow($resultSet);
      mysql_free_result($resultSet);
      if ($row) {
        return $row;
      }
    }
    //TODO � ������ ������ ��������� ������� � ���� �������.
    return NULL;
  }

  /**
   * Returns all fetched data as an array of assoc arrays
   * @param string $query
   * @return array
   */
  function executeSelectAll($query) {
    $resultSet = $this->executeQuery($query);
    if ($resultSet) {
      $result = array();
      while ($row = $this->fetchAssocArray($resultSet)) {
        $result[] = $row;
      }
      mysql_free_result($resultSet);
      return $result;
    }
    //TODO � ������ ������ ��������� ������� � ���� �������.
    return NULL;
  }

  /**
   * Returns all fetched data as an array of objects
   * @param string $query
   * @return array
   */
  function executeSelectAllObjects($query) {
    $resultSet = $this->executeQuery($query);
    if ($resultSet) {
      $result = array();
      while ($row = $this->fetchObject($resultSet)) {
        $result[] = $row;
      }
      mysql_free_result($resultSet);
      return $result;
    }
    return NULL;
  }


  /**
   * Returns first column of fetched as an array
   * @param string $query
   * @return array
   */
  function executeSelectColumn($query) {
    $resultSet = $this->executeQuery($query);
    if ($resultSet) {
      $result = array();
      while ($row = $this->fetchRow($resultSet)) {
        $result[] = $row[0];
      }
      mysql_free_result($resultSet);

      if ($result) {
        return $result;
      }
    }
    //TODO � ������ ������ ��������� ������� � ���� �������.
    //return NULL;
    return array();
  }


  /**
   * Returns database row as an associative array
   * @param string $query
   * @return array
   */
  function executeSelectAssocArray($query) {
    $resultSet = $this->executeQuery($query);
    if ($resultSet) {
      $row = $this->fetchAssocArray($resultSet);
      mysql_free_result($resultSet);
      if ($row) {
        return $row;
      }
    }
    //TODO � ������ ������ ��������� ������� � ���� �������.
    return NULL;
  }

  /**
   * Executes SQL insert/update/delete query
   * @param string $query
   * @return resource
   */
  function execute($query) {
    if (!($res = mysql_query($query, $this->db)))
      $this->handleError(mysql_error(), $query);

    return $res;
  }

  /**
   * Prints error message
   * @param string $query
   * @param string $errmsg
   * @return void
   */
  function handleError($errmsg, $query) {
    $this->lastError = $errmsg;
    $this->lastErrorQuery = $this->cutDebugInfo($query);
    if ($this->showErrors) {
    	//echo Debug::getTraceSummary();
      print "<div class=\"debug\">";
      print "<h2>Database error</h2>\n";
      if ($this->lastErrorQuery) {
        print "<p>".$this->lastErrorQuery."</p>";
        print "<br>";
      }
      print "<p>".htmlspecialchars($errmsg)."</p>";
      print "</div>";die();
    }
  }

  /**
   * Limit debug info to 4096 symbols.
   * @param string $info
   * @return string
   */
  function cutDebugInfo($info) {
    if (strlen($info) > 14096) return substr($info,0,14096)."...";
    else return $info;
  }

  /**
   * Returns last inserted value of auto_increment field
   * @return int
   */
  function getLastAutoIncrementValue() {
    return mysql_insert_id($this->db);
  }

  /**
   * Returns last inserted value of auto_increment field
   * @return int
   */
  function getAffectedRows() {
    return mysql_affected_rows($this->db);
  }
  
  /**
   * Sets internal flag allowing error messages displaying.
   * @param bool $value
   * @return void
   */
  function setShowErrors($value) {
    $this->showErrors = $value;
  }
  
  
  function getShowErrors() {
    return $this->showErrors;
  }
  

  /**
   * Returns last error message.
   * @return string
   */
  function getLastError() {
    return $this->lastError;
  }

  /**
   * Returns number of fields in the specified resultSet.
   * @param resource $resultSet
   * @return int
   */
  function getFieldCount($resultSet) {
    return mysql_num_fields($resultSet);
  }


  /**
   * Returns object with column information from a resultSet.
   * @param resource $resultSet
   * @param int $index
   * @return object
   */
  function getField($resultSet, $index) {
    return mysql_fetch_field($resultSet, $index);
  }

  /**
   * Escapes special characters in a string for use in a SQL statement,
   * taking into account the current charset of the connection
   * @param string $value
   * @return string
   */
  function escapeString($value) {
    return mysql_real_escape_string($value, $this->db);
  }

  /**
   * Returns number of rows in the specified resultset.
   * @param resource $result
   * @return int
   */
  function numRows($result) {
    return mysql_num_rows($result);
  }

}


