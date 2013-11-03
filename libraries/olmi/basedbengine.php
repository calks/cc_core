<?php // $Id: basedbengine.inc.php,v 1.1 2010/11/11 09:51:40 nastya Exp $

class AbstractDataLoader {

  function newObject() {
    die("AbstractDataLoader::newObject not implemented");
  }

  function loadObject(&$object, $record) {
    die("AbstractDataLoader::loadObject not implemented");
  }

}

class DefaultDataLoader extends AbstractDataLoader {
  var $className;
  var $classVars;

  /**
   * @constructor
   */
  function DefaultDataLoader($className) {
    $this->className = $className;
    $this->classVars = get_class_vars($className);
  }

  /**
   * Returns new object
   * @return object
   */
  function newObject() {
    if ($this->className == 'eventscalendar') debug_print_backtrace();
    return new $this->className;
  }

  /**
   * Load values of variables of the object from the record
   * @param string $object
   * @param object $record
   * @return void
   */
  function loadObject(&$object, $record) {
    reset($this->classVars);
    while (list($key, $dummy) = each($this->classVars)) {
      if (isset($record->$key)) {
        $object->setField($key, $record->$key);
      }
    }
  }
}

class BaseDbEngine {
  /**
   * Active database connection.
   * @access protected
   */
  var $database;

  /**
   * @constructor
   * @param resource $database
   */
  function BaseDbEngine(&$database) {
    $this->database = &$database;
  }

  /**
   * Executes query, fetches results and returns array of objects.
   * $loader is data object class name or a subclass of {@link AbstractDataLoader}.
   * @param string $query
   * @param mixed $loader
   * @return array
   */
  function LoadQueryResults($query, $loader) {
    if (!is_object($loader)) {
      $loader = new DefaultDataLoader(strval($loader));
    }
    if ($resultSet = $this->database->executeQuery($query)) {
      $result = array();
      while ($record = $this->database->fetchObject($resultSet)) {
/*
        if(defined("DEBUG")) {
          Debug::dump($record);
        }
*/
        $obj = $loader->newObject();
        $loader->loadObject($obj, $record);
        $result[] = $obj;
      }
      return $result;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Executes query, fetches results and returns one object.
   * If query returns more than one result than function return FALSE
   * $loader is data object class name or a subclass of {@link AbstractDataLoader}.
   * @param string $query
   * @param mixed $loader
   * @return array
   */
  function LoadObject($query, $loader) {
    $arr = $this->LoadQueryResults($query, $loader);
    if (count($arr) > 1 || !$arr) {
      return FALSE;
    }
    else {
      return $arr[0];
    }
  }

  /**
   * @access protected
   * @param object $object
   * @param string $tableName
   * @return string
   */
  function getObjectTableName(&$object, $tableName) {
    if ($tableName) {
      return $tableName;
    }
    else {
      return $object->getTableName();
    }
  }

  /**
   * @access protected
   * @param object $object
   * @param string $tableName
   * @return string
   */
  function getInsertQuery(&$object, $tableName) {
    $classKey = 'BASEOBJECT_FIELDS_'.get_class($object);
    if (array_key_exists($classKey, $GLOBALS)) {
      $fields = $GLOBALS[$classKey];
    }
    else {
      //$fields = $object->getFields();
      $fields = array_keys( get_object_vars( $object ) );
      $GLOBALS[$classKey] = $fields;
    }
    $insertFields = array();
    $insertValues = array();
    
    for ($i = 0; $i < count($fields); ++$i) {
      $fieldName = $fields[$i];
      $value = $object->getField($fieldName);
      if (!$object->isPrimaryKey($fieldName) || $value) {
        $insertFields[] = $this->quoteFieldName($fieldName);
        $insertValues[] = $this->prepareValue($value);
      }
    }
    if (count($insertFields) && count($insertValues)) {
      return "insert into ".$tableName."(".join(", ",$insertFields).") values (".join(", ", $insertValues).")";
    }
    else {
      return NULL;
    }
  }

  /**
   * @param object $object
   * @param string $tableName
   * @return bool
   */
  function insertObject(&$object, $tableName = NULL) {
    $query = $this->getInsertQuery($object, $this->getObjectTableName($object, $tableName));
    if (!is_null($query)) {
      $status = $this->database->execute($query);
      if ($status) {
        $object->setAutoincrementField($this->database->getLastAutoIncrementValue());
      }
      return $status;
    }
    else {
      return NULL;
    }
  }

  /**
   * @access protected
   * @param object $object
   * @param string $tableName
   * @return string
   */
  function getUpdateQuery(&$object, $tableName) {
	$fields = array_keys( get_object_vars( $object ) );
  	//$fields = $object->getFields();
    $conditions = array();
    $updates = array();
    for ($i = 0; $i < count($fields); ++$i) {
      $fieldName = $fields[$i];
      $expression = $this->quoteFieldName($fieldName)."=".$this->prepareValue($object->getField($fieldName));
      if ($object->isPrimaryKey($fieldName)) {
        $conditions[] = $expression;
      }
      else {
        $updates[] = $expression;
      }
    }
    if (count($conditions) && count($updates)) {
      return "update ".$tableName." set ".join(", ", $updates)." where ".join(" and ", $conditions);
    }
    else {
      return NULL;
    }
  }

  /**
   * @param object $object
   * @param string $tableName
   * @return bool
   */
  function updateObject(&$object, $tableName = NULL) {
    $query = $this->getUpdateQuery($object, $this->getObjectTableName($object, $tableName));
    if ($query) {
      return $this->database->execute($query);
    }
    else {
      return NULL;
    }
  }

  /**
   * @access protected
   * @param object $object
   * @param string $tableName
   * @return string
   */
  function getDeleteQuery(&$object, $tableName) {
    $fields = $object->getFields();
    $conditions = array();
    for ($i = 0; $i < count($fields); ++$i) {
      $fieldName = $fields[$i];
      if ($object->isPrimaryKey($fieldName)) {
        $conditions[] = $this->quoteFieldName($fieldName)."=".$this->prepareValue($object->getField($fieldName));
      }
    }
    if (count($conditions)) {
      return "delete from ".$tableName." where ".join(" and ", $conditions);
    }
    else {
      return NULL;
    }
  }

  /**
   * @param object $object
   * @param string $tableName
   * @return bool
   */
  function deleteObject(&$object, $tableName = NULL) {
    $query = $this->getDeleteQuery($object, $this->getObjectTableName($object, $tableName));
    if ($query) {
      return $this->database->execute($query);
    }
    else {
      return NULL;
    }
  }

  /**
   * Converts value to string valid for use in database queries.
   * @param mixed $value
   * @return string
   */
  function prepareValue($value) {
    if (is_string($value)) {
      return "'".addslashes($value)."'";
    }
    else if (is_null($value)) {
      return "NULL";
    }
    else if (is_bool($value)) {
      return (int) $value;
    }
    else {
      return $value;
    }
  }

  /**
   * Returns scalar value
   * @param string $query
   * @return mixed
   */
  function executeScalar($query) {
    return $this->database->executeScalar($query);
  }

  function quoteFieldName($fieldName) {
    return '`'.$fieldName.'`';
  }

}

?>
