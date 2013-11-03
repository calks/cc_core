<?php

	class coreGalleryPhotoEntity extends coreBaseEntity {		
		public $seq;
		public $comment;		
		public $gallery_id;
		
		public function getTableName() {
			return "gallery_photo";
		}		
				
        public function order_by() {
            return " seq ";
        }

        public function make_form(&$form) {
        	$form->addField(new THiddenField("id"));            
            $form->addField(new TEditField("comment", "", 85, 255));
            $form->addField(new THiddenField("seq"));            
            
            $gallery = Application::getEntityInstance('gallery');
            $form->addField(new TSelectField('gallery_id', '', $gallery->getSelect('-- Не выбрана --')));
            
			$form->addField(imagePkgHelperLibrary::getField('image', $this->getName(), $this->id, array(			
				'width' => 800,
				'height' => 100,
				'max_files' => 1
			)));
            
            
            return $form;
        }		

        public function validate() {
        	$errors = parent::validate();
        	
        	if (!$this->gallery_id) {
        		$errors[] = "Нужно выбрать галерею";
        	}
        	
        	return $errors;
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