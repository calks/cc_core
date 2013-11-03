<?php


    class coreCaptchaLibrary {

        protected $name;

        public function __construct($name) {
            $this->name = 'captcha_code_' . $name;
            if(!isset($_SESSION[$this->name])) {
                $this->regenerate();
            }
        }

        public function display() {
            print '<img class="captcha" src="/captcha/' . $this->name . '/' . time() . '" width="150" height="50" alt="Captcha">';
        }

        public function regenerate($password_length=4) {
            $_SESSION[$this->name] = rand((int) pow(10, $password_length - 1), (int) pow(10, $password_length) - 1);;
        }

        public function get_code() {
            return $_SESSION[$this->name];
        }

        public function code_valid($code) {
            return strtolower($this->get_code()) == strtolower($code);
        }

    }
