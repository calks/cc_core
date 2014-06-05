<?php


    if (Application::getApplicationName() == 'kauai') {
        Application::loadLibrary('olmi/fck');

        class TEditorField extends TFCKField {

        }
    }
    else {

        Application::loadLibrary('olmi/field');

        include_once Application::getSitePath() . '/core/libraries/ck/ckeditor/ckeditor.php';
        include_once Application::getSitePath() . '/core/libraries/ck/ckfinder/ckfinder.php';

        class TEditorField extends TField {
            protected $_editor;
            protected $_config;

            public function __construct($aName, $aValue = '', $width = null, $height = null, $config = null) {
                TField::TField($aName);
                               
                $this->_editor = new CKEditor('/core/libraries/ck/ckeditor/');
                $this->_config = array(
                	'language' => 'ru'
                );

                //$this->SetValue($aValue);

                CKFinder::SetupCKEditor($this->_editor, '/core/libraries/ck/ckfinder/');

                if (empty($width) == false) $this->SetWidth($width);
                if (empty($height) == false) $this->SetHeight($height);
                $this->SetConfig($config);
            }

            function SetValue($aValue) {
                $this->Value = $aValue;
                $this->_editor->Value = $aValue;
            }

            function SetFromPost($POST) {
                if (isset($POST[$this->Name])) {
                    $this->Value = stripslashes($POST[$this->Name]);
                    $this->_editor->Value = $this->Value;
                    return true;
                }
                return false;
            }

            function GetAsHTML() {
                return $this->_editor->editor($this->Name, $this->Value, $this->_config);
            }

            /*function SetBasePath($path) {
                $this->_editor->BasePath = $path;
            }*/

            function SetWidth($width) {
                $this->_config['width'] = $width;
            }

            function SetHeight($height) {
                $this->_config['height'] = $height;
            }

            /*function SetToolbarSet($toolbar) {
                $this->_editor->ToolbarSet = $toolbar;
            }*/

            function SetConfig($config) {
                if (is_array($config)) {
                    foreach ($config as $k => $v) {
                        $this->_config[$k] = $v;
                    }
                }
            }
        }
    }


