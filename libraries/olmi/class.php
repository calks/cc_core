<?php
    Application::loadLibrary('olmi/object');
    class CMSObject extends BaseObject {

        function mandatory_fields() {
            return array();
        }

        function unique_fields() {
            return array();
        }

        function trim_fields() {
            return array();
        }

        function get_table_name() {
            return "tbl".get_class($this);
        }

        function order_by() {
            return " order by id desc";
        }

        function getPrimaryKeyField() {
            return "id";
        }

        function load($_id) {
            $dbEngine = Application::getDbEngine();
            $table = $this->get_table_name();
            $object = $dbEngine->LoadObject("select * from ".$table." where id=".$_id, get_class($this));
            if (!$object)
                return 0;
            return $object;
        }

        function load_list() {
            $dbEngine = Application::getDbEngine();
            $table = $this->get_table_name();
            return $dbEngine->LoadQueryResults("select * from ".$table." ".$this->order_by(), get_class($this));
        }

        function make_form(&$form) {
            $form->addField(new THiddenField("id"));
            return $form;
        }

        function delete($_id = '') {
            $dbEngine = Application::getDbEngine();
            $table = $this->get_table_name();
            if (!$this->id) {
                $object = $this->load($_id);
                return $dbEngine->deleteObject($object, $table);
            } else {
                return $dbEngine->deleteObject($this, $table);
            }
        }

        function validate() {
            $db = Application::getDb();
            $dbEngine = Application::getDbEngine();
            $table = $this->get_table_name();
            $errors = array();

            $trim = $this->trim_fields();
            foreach ($trim as $key) {
                $this->$key = trim($this->$key);
            }

            $mandatory = $this->mandatory_fields();
            $mandatory_keys = array_keys($mandatory);
            foreach ($this as $key => $value) {
                if (in_array($key, $mandatory_keys) && $value == '') {
                    if (isset($mandatory[$key]) && $mandatory[$key] != "")
                        $err_out = $mandatory[$key];
                    else
                        $err_out = $key;
                    $errors[] = "Не заполнено поле &laquo;$err_out&raquo;";
                }
            }
            if (sizeof($errors) == 0) {
                $unique = $this->unique_fields();
                $unique_keys = array_keys($unique);

                $extrasql = "";
                if ($this->id)
                    $extrasql = " and id <> ".$this->id;
                foreach ($this as $key => $value) {
                    if (in_array($key, $unique_keys)) {
                        $query = "select count(*) from ".$table." where ".$key."=".$dbEngine->prepareValue($this->$key)." ".$extrasql;
                        if ($db->executeScalar($query) > 0) {
                            if (isset($unique[$key]) && $unique[$key] != "")
                                $err_out = $unique[$key];
                            else
                                $err_out = $key;

                            $errors[] = "Выбранное вами для поля ".$err_out." значение уже используется.";
                        }

                    }
                }
            }
            return $errors;
        }

        function getSubstr($str, $len) {
            $more = true;
            if ($str == "")
                return $str;
            if (is_array($str))
                return $str;
            $str = trim(strip_tags($str));
            // if it's les than the size given, then return it
            if (strlen($str) <= $len)
                return $str;
            // else get that size of text
            $str = substr($str, 0, $len);
            // backtrack to the end of a word
            if ($str != "") {
                // check to see if there are any spaces left
                if (!substr_count($str, " ")) {
                    if ($more == 'true')
                        $str .= "...";
                    return $str;
                }
                // backtrack
                while (strlen($str) && ($str[strlen($str) - 1] != " ")) {
                    $str = substr($str, 0, -1);
                }
                $str = substr($str, 0, -1);
                if ($more == 'true')
                    $str .= "...";
                if ($more != 'true' and $more != 'false')
                    $str .= $more;
            }
            return $str;
        }

        /**
         * Clones properties using other BaseObject as values source
         * @param BaseObject $source_object
         * @return bool;
         *
         */
        function assign($source_object, $only_class_vars = false) {
            if (!$source_object) {
                die(__FILE__.'@'.__LINE__);
                return FALSE;
            }
            if (!in_array(get_class($source_object), array(get_class($this), 'stdClass'))) {
                die(__FILE__.'@'.__LINE__);
            }

            $fields = get_object_vars($source_object);
            if ($only_class_vars) {
                $classFields = get_class_vars(get_class($this));
                $fields = array_intersect_key($fields, $classFields);
            }

            if (0 < count($fields)) {
                foreach ($fields as $key => $value) {
                    $this->setField($key, $value);
                }
            }
            return TRUE;
        }

        function getFields() {
            $result = get_class_vars(get_class($this));
            return array_keys($result);
        }

    }
