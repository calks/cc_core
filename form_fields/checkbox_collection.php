<?php

	
	class coreCheckboxCollectionFormField extends coreBaseFormField {
		
		protected $options = array();
		protected $max_columns = 1;
		
		function GetAsHTML() {
			
			if (!$this->options) return '';
			
			$this->addClass('checkbox-collection');
			
			$options_chunked = array_chunk($this->options, $this->max_columns);
			
			$columns_count = count($this->options) < $this->max_columns ? count($this->options) : $this->max_columns;  
			
			$attr_string = $this->getAttributesString();
			
			$out = "<table $attr_string>";
			foreach ($options_chunked as $row=>$cells) {
				$out .= "<tr>";
				foreach ($cells as $value=>$caption) {
					$checked = in_array($value, $this->value) ? 'checked="checked"' : '';
					$value = $this->getSafeAttrValue($value);
					$out .= "<td class=\"checkbox\"><input type=\"checkbox\" value=\"$value\" $checked></td>";
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
		
	}