<?php

    class URL {

        protected $raw_url;

        protected $address;
        protected $params;
        protected $get;

        public function __construct($raw_url) {
            $this->raw_url = $raw_url;

            $tmp = explode('?', $raw_url);

            $this->address = $tmp[0];
            $this->params = array();

            $this->get = array();
            if (isset($tmp[1])) {
                $get = explode('&', $tmp[1]);
                foreach ($get as $nvp) {
                    $nvp = explode('=', $nvp);
                    $this->get[$nvp[0]] = isset( $nvp[1] ) ? $nvp[1] : $nvp[0];
                }
            }

        }

        public function getRawUrl() {
            return $this->raw_url;
        }

        public function getAddress() {
            return $this->address;
        }

        public function setAddress($address) {
            if (is_array($address))
                $address = implode('/', $address);
            $this->address = $address;
        }

        public function getParams() {
            return $this->params;
        }

        public function setParams($params) {
            if (is_array($params))
                $params = implode('/', $params);
            $this->params = $params;
        }

        public function addGetParam($name, $value) {
            $this->get[$name] = $value;
        }

        public function removeGetParam($name) {
            if (isset($this->get[$name]))
                unset($this->get[$name]);
        }

        public function getGetParams() {
            return $this->get;
        }

        public function setParts($address, $params) {
            $this->setAddress($address);
            $this->setParams($params);
        }

        public function toString() {
            $url = $this->address;
            if ($this->params) {
                if (substr($url, strlen($url) - 1) != '/')
                    $url .= '/';
                $url .= $this->params;
            }
            if ($this->get) {
                $get = '';
                foreach ($this->get as $name => $value) {
                    $get .= empty($get) ? '?' : '&';
                    $get .= $name.'='.urlencode($value);
                }
                $url .= $get;
            }
            return $url;
        }

    }

