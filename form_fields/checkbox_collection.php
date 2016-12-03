<?php

	
	class coreCheckboxCollectionFormField extends coreBaseFormField {
		
		protected $options = array();
		protected $max_columns = 1;
		
		public function __construct($field_name) {
			parent::__construct($field_name);
			$this->value = array();
		}
		
		public function render() {
			
			if (!$this->options) return '';
			
			$this->addClass('checkbox-collection');
			
			$options_chunked = array_chunk($this->options, $this->max_columns, true);
			
			$columns_count = count($this->options) < $this->max_columns ? count($this->options) : $this->max_columns;  
			
			$attr_string = $this->getAttributesString();
			
			$out = "<table $attr_string>";
			foreach ($options_chunked as $row=>$cells) {
				
				$out .= "<tr>";
				foreach ($cells as $value=>$caption) {
					$checked = in_array($value, $this->value) ? 'checked="checked"' : '';
					$value = $this->getSafeAttrValue($value);
					$out .= "<td class=\"checkbox\"><input type=\"checkbox\" name=\"{$this->field_name}[]\" value=\"$value\" $checked></td>";
					$out .= "<td class=\"caption\">$caption</td>";
				}
				
				$missing_columns_count = $columns_count - count($cells);
				
				for ($i=1; $i<=$missing_columns_count; $i++) {
					$out .= "<td class=\"checkbox\"></td>";
					$out .= "<td class=\"caption\"></td>";
				}
				
				$out .= "</tr>";
			}
						
			$out .= "</table>";
			
			return $out;		
			
		}
		
		
		public function SetFromPost($POST) {
			$this->value = array();
			
			$data = Request::getFieldValue($this->field_name, $POST, array());			
			foreach ($data as $v) {				
				if (array_key_exists($v, $this->options)) {
					$this->value[] = $v;
				}
			}
		}
		
	}