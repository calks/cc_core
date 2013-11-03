<?php

    class coreNewsEntity extends coreBaseEntity {
        var $id;        
        var $date;
        var $title;        
        var $story;        
        var $active;

        function get_table_name() {
            return "news";
        }

        function mandatory_fields() {
            return array(
            	"title" => "Заголовок", 
            	"date" => "Дата", 
            	"story" => "Текст новости"
            );
        }

        function order_by() {
            return " date DESC ";
        }

        function make_form(&$form) {
            $form->addField(new THiddenField("id"));
            $form->addField(new TEditField("title", "", 85));
            $form->addField(coreFormElementsLibrary::get('date', 'date'));
            $form->addField(new TEditorField("story", "", 800, 200));
            $form->addField(new TCheckboxField("active", "0"));
			$form->addField(imagePkgHelperLibrary::getField('image', $this->getName(), $this->id, array(			
				'width' => 800,
				'height' => 100,
				'max_files' => 1
			)));
			
            return $form;
        }
        
        function delete() {
        	imagePkgHelperLibrary::deleteImages($this);
        	return parent::delete();
        }
        
        function save() {
        	$date = $this->date;
        	$this->date = coreFormattingLibrary::dateRussianToMysql($this->date);
        	
        	$id = parent::save();
        	
			if ($id) {				
				imagePkgHelperLibrary::commitUploadedFiles(Request::get('image'), $id);				
			}
        	
        	$this->date = $date;
        	return $id;
        }
        
        function load_list($params) {
        	$mode = isset($params['mode']) ? $params['mode'] : '';

        	if ($mode=='front') {
        		$alias = $this->get_table_abr();
        		$params['where'][] = "$alias.active=1";
        	}
        	
        	$list = parent::load_list($params);
        	
        	foreach($list as $item) {
        		$item->date_str = coreFormattingLibrary::formatDate($item->date);
        		$item->date = coreFormattingLibrary::dateMysqlToRussian($item->date);
        	}
        	
        	return $list;
        	
        }
        
        function count_list($params) {
        	$mode = isset($params['mode']) ? $params['mode'] : '';
        	
        	if ($mode=='front') {
        		$alias = $this->get_table_abr();
        		$params['where'][] = "$alias.active=1";
        	}
        	
        	return parent::count_list($params);
        
        }


    }
