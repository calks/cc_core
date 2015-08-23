<?php // $Id: form.inc.php,v 1.1 2010/11/11 09:51:41 nastya Exp $



    class BaseForm {
        /**
         * @access protected
         */
        var $fields;

        /**
         * @constructor
         */
        function BaseForm() {
            $this->fields = array();
        }

        /**
         * Adds field to array of the form fields
         * @param TField $aField
         */
        function addField($aField) {
        	$name = isset($aField->Name) ? $aField->Name : $aField->getFieldName();
        	
            $this->fields[$name] = $aField;
        }

        /**
         * Returns html code that renders field
         * @param string $fieldName
         * @return string
         */
        function render($fieldName) {
            if (array_key_exists($fieldName, $this->fields )) {
                return $this->fields[$fieldName]->GetAsHTML();
            } else {
                return "<div class=\"debug\">Unknown field $fieldName</div>";
            }
        }

        /**
         * Returns field value
         * @param string $fieldName
         * @return mixed
         */
        function getValue($fieldName) {
            if (array_key_exists($fieldName, $this->fields )) {
                return $this->fields[$fieldName]->GetValue();
            } else {
                return NULL;
            }
        }

        /**
         * Sets value for the field of the form
         * @param string $fieldName
         * @param mixed $value
         */
        function setValue($fieldName, $value) {
            if (array_key_exists($fieldName, $this->fields )) {
                $this->fields[$fieldName]->SetValue($value);
            }
        }

        /**
         * Returns associative array that contains all fields as fieldName => fieldValue
         * @return array
         */
        function getParams() {
            $result = array();
            foreach ($this->fields as $field) {
                $value = $field->GetValue();
                if (!is_null($value) && $value !== '') {
                    $result[$field->Name ] = $value;
                }
            }
            return $result;
        }

        /**
         * Displays all fields of the form as fieldName=[fieldValue]
         * @return void
         */
        function dump() {
            print "<pre>";
            foreach ($this->fields as $field) {
                print $field->Name."=[".$field->GetValue()."]\n";
            }
            print "</pre>";
        }

        /**
         * Load the fields of the form from request
         * @param string $POST
         * @param object $context
         * @return void
         */
        function LoadFromRequest($POST, $context = NULL) {        	
            if (is_array($POST)) {            	
                reset($this->fields );
                while (list($key, $value) = each($this->fields )) {
                    $field =& $this->fields[$key]; # get the _reference_ to the object in form

                    if (method_exists($field, 'SetFromPost')) $field->SetFromPost($POST);
                }
            }
        }

        /**
         * Load the fields of the form from object
         * @param string $object
         * @return void
         */
        function LoadFromObject(&$object) {
            if ($object) {
                reset($this->fields );
                while (list($key, $value) = each($this->fields )) {
                    if ($object->hasField($key)) {
                        $field =& $this->fields[$key]; # get the _reference_ to the object in form
                        $field->SetValue($object->getField($key));
                    }
                }
            }
        }

        /**
         * Load the fields of the form from array
         * @param string $arr
         * @return void
         */
        function LoadFromArray(&$arr) {
            reset($this->fields );
            while (list($key, $value) = each($this->fields )) {
                if (array_key_exists($key, $arr)) {
                    $field =& $this->fields[$key]; # get the _reference_ to the object in form
                    $field->SetValue($arr[$key]);
                }
            }
        }

        /**
         * Load values of variables of the object from the form
         * @param string $object
         * @param object $context
         * @return void
         */
        function UpdateObject(&$object, $context = NULL) {
            reset($this->fields );
            while (list($key, $field) = each($this->fields )) {
                if (!$context || $context->canAccess($key)) {
                    if ($object->hasField($key) && $field->isEditable() && $field->hasValue()) {
                        $object->setField($key, $field->GetValue());
                    }
                }
            }
        }

        function MergeObject(&$object, $context = NULL) {
            if (!$object) {
                return;
            }

            reset($this->fields );
            while (list($key, $field) = each($this->fields )) {
                if (!$context || $context->canAccess($key)) {
                    if ($object->hasField($key) && $field->isEditable() && $field->hasValue()) {
                        if ($field->GetValue() || $field->GetValue() === '0') {
                            $object->setField($key, $field->GetValue());
                        }
                    }
                }
            }
        }

        function removeField($field) {
            if (isset($this->fields[$field])) {
                unset($this->fields[$field]);
            }

            return $this;
        }

    }

?>
