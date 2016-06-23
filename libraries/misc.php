<?php

    Application::loadLibrary('olmi/redirect');

    function website_address_valid($url) {
        $regexp = "/(?P<protocol>(?:(?:f|ht)tp|https):\/\/)?
                  (?P<domain>(?:(?!-)
                  (?P<sld>[a-zA-Z\d\-]+)(?<!-)
                  [\.]){1,2}
                  (?P<tld>(?:[a-zA-Z]{2,}\.?){1,}){1,}
                  |
                  (?P<ip>(?:(?(?<!\/)\.)(?:25[0-5]|2[0-4]\d|[01]?\d?\d)){4})
                  )
                  (?::(?P<port>\d{2,5}))?
                  (?:\/
                  (?P<script>[~a-zA-Z\/.0-9-_]*)?
                  (?:\?(?P<parameters>[=a-zA-Z+%&0-9,.\/_ -]*))?
                  )?
                  (?:\#(?P<anchor>[=a-zA-Z+%&0-9._]*))?/x";

        return preg_match($regexp, $url, $m);
    }

    function email_valid($email) {        
        return preg_match('/^([0-9a-zA-Z])([0-9a-zA-Z_\.-]*)@([0-9a-zA-Z])([0-9a-z\.-]*)\.([a-zA-Z]+)$/is', $email);
    }


    function email_unique($email, $user_id=0) {
    	$user = Application::getEntityInstance('user');
    	$table = $user->getTableName();
    	$email = addslashes($email);
    	$user_id = (int)$user_id;
    	$db = Application::getDb();
    	return !(bool)$db->executeScalar("
    		SELECT COUNT(*)
    		FROM $table
    		WHERE email='$email' AND id!=$user_id
    	");
    }
    
    function get_empty_select($add_null_item = false) {
        $select = array();

        if ($add_null_item) {
            if (is_string($add_null_item)) {
                $select[0] = $add_null_item;
            } elseif (is_array($add_null_item)) {
                $keys = array_keys($add_null_item);
                if (count($add_null_item) == 1) {
                    $select[$keys[0]] = $add_null_item[$keys[0]];
                } else {
                    $select[$add_null_item[$keys[0]]] = $add_null_item[$keys[1]];
                }
            } else {
                $select[0] = "-- Выберите --";
            }
        }

        return $select;
    }

	function encode_header_utf_8($str) {
		if (!$str) return "";
	    return '=?utf-8?B?'.base64_encode($str).'?=';
	}
    
	function array_is_subset($array_to_test, $set) {
		return count(array_intersect($array_to_test, $set)) == count($array_to_test);
	}

