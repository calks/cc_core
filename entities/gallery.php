<?php

	class coreGalleryEntity extends coreBaseEntity {
		public $name;
		public $seq;
		public $active;
		
		
		public function getTableName() {
			return "gallery";
		}
		
        function mandatory_fields() {
            return array(
            	"name" => "Название"
            );
        }
				
        function order_by() {
            return " seq ";
        }

        function make_form(&$form) {
            $form->addField(new THiddenField("id"));
            $form->addField(new TEditField("name", "", 85, 255));
            $form->addField(new THiddenField("seq"));
            $form->addField(new TCheckboxField("active", "0"));
			$form->addField(imagePkgHelperLibrary::getField('image', $this->getName(), $this->id, array(			
				'width' => 800,
				'height' => 100,
				'max_files' => 1
			)));
            
            return $form;
        }
		
        function getSelect($add_null_item) {
        	$db = Application::getDb();
        	$table = $this->get_table_name();

        	$sql = "
        		SELECT id, name 
        		FROM $table
        	";        	
        	$data = $db->executeSelectAllObjects($sql);
        	
        	$out = get_empty_select($add_null_item);
        	foreach($data as $item) $out[$item->id] = $item->name;
        	return $out;
        }
        
        function delete() {
        	imagePkgHelperLibrary::deleteImages($this);
        	return parent::delete();
        }
        
        function save() {
        	$id = parent::save();        	
			if ($id) {				
				imagePkgHelperLibrary::commitUploadedFiles(Request::get('image'), $id);				
			}
        	return $id;
        }
        
        
        
		
	}