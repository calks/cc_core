<?php // $Id: object.inc.php,v 1.1 2010/11/11 09:51:40 nastya Exp $

class BaseObject {

  /**
   * Returns TRUE if object has specified field.
   * @param string $fieldName
   * @return bool
   */
  function hasField($fieldName) {
    return array_key_exists($fieldName, get_object_vars($this));
  }

  /**
   * Returns value of the specified field of the object.
   * @param string $fieldName
   * @return mixed
   */
  function getField($fieldName) {
    return $this->$fieldName;
  }

  /**
   * Sets value of the specified field of the object.
   * @param string $fieldName
   * @param mixed $value
   */
  function setField($fieldName, $value) {
    $this->$fieldName = $value;
  }

  /**
   * Returns array of field names of the object.
   * @return array
   */
  function getFields() {
    $result = get_object_vars($this);
    return array_keys($result);
  }

  /**
   * Returns TRUE if specified field name is primary key.
   * @param string $fieldName
   * @return bool
   */
  function isPrimaryKey($fieldName) {
    return strcasecmp($fieldName, $this->getPrimaryKeyField()) == 0;
  }

  /**
   * Sets value of the auto increment field after object has been inserted to the
   * database table.
   * @param integer $value
   */
  function setAutoincrementField($value) {
    $this->setField($this->getPrimaryKeyField(), $value);
  }

  /**
   * Returns name of the primary key field of the table this object maps to.
   * @return string
   */
  function getPrimaryKeyField() {
    return "Id";
  }

  /**
   * Returns name of the table this object maps to.
   * @return string
   */
  function getTableName() {
    trigger_error("BaseObject::getTableName() not implemented", E_USER_ERROR);
  }

}

?>
