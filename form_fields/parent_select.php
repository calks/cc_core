<?php

	class coreParentSelectFormField extends coreSelectFormField {
		
		public function GetAsHTML() {
			$Res = '<select name="'.htmlspecialchars($this->Name).'"'.HtmlUtils::attributes($this->attributes).'>';
			foreach ($this->Options as $opt) {
				$value = $opt->Value;
				$text = $opt->Text;
				$disabled = strpos($text, '[disabled]') !== false ? 'disabled="disabled"' : '';
				$selected = $this->Value == $value ? 'selected="selected"' : '';
				$replace = array(
					'[space]' => '&nbsp;',
					'[disabled]' => ''
				);
				$text = str_replace(array_keys($replace), $replace, $text);
			
				$Res .= "<option value=\"$value\" $disabled $selected>$text</option>";
			}
			$Res .= "</select>";
			return $Res;
		}			
	}