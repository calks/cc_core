<?php

    

    Application::loadLibrary('olmi/field');

    class TFCKField extends TField {
        var $_fck;

	    function FCK_Config(&$fckInstance) {
	        $fckInstance->BasePath = '/core/libraries/fck/';
	        $fckInstance->Config['AutoDetectLanguage'] = false;
	        $fckInstance->Config['DefaultLanguage'] = 'en';
	        $fckInstance->Height = 400;
	        $fckInstance->ToolbarSet = "Full";
	    }
	        
        
        function TFCKField($aName, $aValue = '', $width = null, $height = null, $config = null) {
            TField::TField($aName);

            $this->_fck = new FCKeditor($aName);
            $this->SetValue($aValue);

            $this->FCK_Config($this->_fck);

            if (empty($width) == false) {
                $this->SetWidth($width);
            }

            if (empty($height) == false) {
                $this->SetHeight($height);
            }


            $this->SetConfig($config);
        }

        function SetValue($aValue) {
            $this->Value = $aValue;
            $this->_fck->Value = $aValue;
        }

        function SetFromPost($POST) {
            if (isset($POST[$this->Name])) {
                $this->Value = stripslashes($POST[$this->Name]);
                $this->_fck->Value = $this->Value;
                return true;
            }
            return false;
        }

        function GetAsHTML() {
            return $this->_fck->Create();
        }

        function SetBasePath($path) {
            $this->_fck->BasePath = $path;
        }

        function SetWidth($width) {
            $this->_fck->Width = $width;
        }

        function SetHeight($height) {
            $this->_fck->Height = $height;
        }

        function SetToolbarSet($toolbar) {
            $this->_fck->ToolbarSet = $toolbar;
        }

        function SetConfig($config) {
            if (is_array($config)) {
                foreach ($config as $k => $v) {
                    $this->_fck->Config[$k] = $v;
                }
            }
        }
    }
