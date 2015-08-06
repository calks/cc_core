<?php // $Id: field.inc.php,v 1.1 2010/11/11 09:51:40 nastya Exp $

	Application::loadLibrary('olmi/htmlutils');

	$INC_field = true;

	// ===========================================================================================
	//                                                                                          //
	//  TField library                                                                          //
	//  Version 0.3                                                                             //
	//  Last modified 06.05.2003                                                                //
	//  Writen by Maxim Makarenko                                                               //
	//  (C) Olmisoft Inc                                                                        //
	//                                                                                          //
	// ===========================================================================================

	////////////////////////////////////////////////////////////////////////////////////////////////
	//                                  TField class                        //
	////////////////////////////////////////////////////////////////////////////////////////////////
	/*
	 class TField{
	 var $Name;
	 var $Value;
	
	 function TField($aName, $aValue = "");
	 function GetValueForSQL();
	 function GetHTMLSafetyValue();
	 function GetValue();
	 function SetFromPost($POST);
	 function SetValue($aValue);
	 function GetStringForGet();
	 function GetAsHTML();
	 function DisplayAsHTML();
	 }
	 */

	class TField {
		var $Name;
		var $Value;

		//====================================================
		// Counstructor
		function TField($aName, $aValue = "") {
			$this->Name = $aName;
			$this->Value = $aValue;
		}

		//====================================================
		// string GetValueForSQL(void);
		function GetValueForSQL() {
			$Ret = "'".addslashes($this->Value)."'";
			return $Ret;
		}

		//====================================================
		// string GetHTMLSafetyValue(void);
		function GetHTMLSafetyValue() {
			return htmlspecialchars($this->Value);
		}

		//====================================================
		// string GetValue(bool $aForHTML);
		function GetValue() {
			return $this->Value;
		}

		//====================================================
		// void SetFromPost(string[] POST);
		function SetFromPost($POST) {
			if (isset($POST[$this->Name])) {
				$this->Value = @stripslashes($POST[$this->Name]);
				//$x = "SetFromPost ".$this->Name."=".$this->Value;
				//Debug::dump($x);
				}
		}

		//====================================================
		// void SetValue(void);
		function SetValue($aValue) {
			$this->Value = $aValue;
		}

		//====================================================
		// string GetStringForGet(void);
		function GetStringForGet() {
			return $this->Name."=".$this->Value;
		}

		//====================================================
		// string GetAsHTML(void);
		function GetAsHTML() {
			return $this->GetHTMLSafetyValue();
		}

		//====================================================
		// void DisplayAsHTML(void);
		function DisplayAsHTML() {
			print($this->GetAsHTML());
		}

		//====================================================
		// bool IsEditable(void);
		function IsEditable() {
			return true;
		}

		function isStandardField() {
			return TRUE;
		}

		function SetFromRequestSpecial() {
			die("TField::SetFromRequestSpecial() not implemented");
		}

		function hasValue() {
			return TRUE;
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////
	//                                TLabelField class                       //
	////////////////////////////////////////////////////////////////////////////////////////////////
	/*
	 class TLabelField extends TField{
	 function IsEditable();
	 }
	 */

	class TLabelField extends TField {

		//====================================================
		// void GetAsHTML(void);
		function GetAsHTML() {
			return $this->GetHTMLSafetyValue();
		}

		//====================================================
		// bool IsEditable(void);
		function IsEditable() {
			return false;
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////
	//                                THiddenField class                      //
	////////////////////////////////////////////////////////////////////////////////////////////////
	/*
	 class THiddenField extends TField{
	 function GetAsHTML(){
	 }
	 */

	class THiddenField extends TField {

		//====================================================
		// void GetAsHTML(void);
		function GetAsHTML() {
			return sprintf("<input type=\"hidden\" name=\"%s\" value=\"%s\">", $this->Name, $this->GetHTMLSafetyValue());
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////
	//                                TFileField class                        //
	////////////////////////////////////////////////////////////////////////////////////////////////
	/*
	 class TFileField extends TField{
	
	 function TFileField($aName, $aValue);
	 function GetAsHTML($aSize = 0, $aMaxLength = 0);
	 }
	 */

	class TFileField extends TField {
		var $Size;
		var $attributes;

		/**
		 * @constructor
		 */
		function TFileField($aName, $aSize = 40, $attributes = NULL) {
			$this->TField($aName, NULL);
			$this->Size = $aSize;
			$this->attributes = $attributes;
		}

		/**
		 * @return string
		 */
		function GetAsHTML() {
			$Res = sprintf("<input type=\"file\" name=\"%s\" size=\"%u\"".HtmlUtils::attributes($this->attributes).">", $this->Name, $this->Size);
			return $Res;
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////
	//                                TEditField class                        //
	////////////////////////////////////////////////////////////////////////////////////////////////
	/*
	 class TEditField extends TField{
	 var $Size;
	 var $MaxLength;
	
	 function TEditField($aName, $aValue, $aSize = 40; $aMaxLength = 255);
	 function GetAsHTML($aSize = 0, $aMaxLength = 0);
	 }
	 */

	class TEditField extends TField {
		var $Size;
		var $MaxLength;
		var $attributes;

		//====================================================
		// Counstructor
		function TEditField($aName, $aValue = "", $aSize = 40, $aMaxLength = 255, $attributes = "") {
			$this->TField($aName, $aValue);
			$this->Size = $aSize;
			$this->MaxLength = $aMaxLength;
			$this->attributes = $attributes;
		}

		function getInputType() {
			return 'text';
		}

		//====================================================
		// void GetAsHTML($aSize = 0, $aMaxLength = 0);
		function GetAsHTML($aSize = 0, $aMaxLength = 0) {
			$Res = sprintf("<input type=\"%s\" name=\"%s\" size=\"%u\" maxlength=\"%u\" value=\"%s\" ".$this->attributes.">", $this->getInputType(), $this->Name, $aSize ? $aSize : $this->Size, $aMaxLength ? $aMaxLength : $this->MaxLength, $this->GetHTMLSafetyValue());
			return $Res;
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////
	//                                TPasswordField class                      //
	////////////////////////////////////////////////////////////////////////////////////////////////
	/*
	 class TPasswordField extends TEditField{
	 function GetAsHTML($aSize = 0, $aMaxLength = 0);
	 }
	 */

	class TPasswordField extends TEditField {

		function getInputType() {
			return 'password';
		}

	}

	////////////////////////////////////////////////////////////////////////////////////////////////
	//                                TTextField class                        //
	////////////////////////////////////////////////////////////////////////////////////////////////
	/*
	 class TTextField extends TField{
	 var $Cols;
	 var $Rows;
	
	 function TTextField($aName, $aValue, $aCols = 40, $aRows = 8);
	 function GetAsHTML($aCols, $aRows);
	 }
	 */

	class TTextField extends TField {
		var $Cols;
		var $Rows;
		var $attributes;

		//====================================================
		// Constructor
		function TTextField($aName, $aValue = "", $aCols = 40, $aRows = 8, $aAttributes = NULL) {
			$this->TField($aName, $aValue);
			$this->Cols = $aCols;
			$this->Rows = $aRows;
			$this->attributes = $aAttributes;
		}

		//====================================================
		// void GetAsHTML($aCols = 0, $aRows = 0);
		function GetAsHTML($aCols = 0, $aRows = 0) {
			$res = '<textarea name="'.$this->Name.'"';
			if ($aCols == 0) {
				$aCols = $this->Cols;
			}
			if ($aCols != 0) {
				$res .= ' cols="'.$aCols.'"';
			}
			if ($aRows == 0) {
				$aRows = $this->Rows;
			}
			if ($aRows != 0) {
				$res .= ' rows="'.$aRows.'"';
			}
			return $res.HtmlUtils::attributes($this->attributes).'>'.$this->GetHTMLsafetyValue().'</textarea>';
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////
	//                                TCheckboxField class                      //
	////////////////////////////////////////////////////////////////////////////////////////////////
	/*
	 class TCheckboxField extends TField{
	 var $Checked;
	
	 function TCheckboxField($aName, $aValue, $aChecked);
	 function GetValueForSQL(void
	 function SetFromPost($POST);
	 function Check(void
	 function Uncheck(void
	 function GetAsHTML($aCaption = "", $aAlign = "right");
	 }
	 */
	class TCheckboxField extends TField {
		var $Checked;
		var $attributes;

		//====================================================
		// Constructor
		function TCheckboxField($aName, $aValue, $aChecked = false, $attributes = NULL) {
			$this->TField($aName, $aValue);
			$this->Checked = $aChecked;
			$this->attributes = $attributes;
		}

		//====================================================
		// string GetValueForSQL(void);
		function GetValueForSQL() {
			return $this->Checked ? TField::GetValueForSQL() : "'0'";
		}

		//====================================================
		// void SetFromPost(string[] $POST);
		function SetFromPost($POST) {
			if (isset($POST[$this->Name])) {
				$this->Value = stripslashes($POST[$this->Name]); //TODO ������� ��� ������� �� ������.
				$this->Checked = true;
			}
			else {
				$this->Checked = false;
			}
		}

		//====================================================
		// void Check(void);
		function Check() {
			$this->Checked = true;
		}

		//====================================================
		// void Uncheck(void);
		function Uncheck() {
			$this->Checked = false;
		}

		function getDefaultAlign() {
			return "right";
		}

		//====================================================
		// void GetAsHTML($aCaption = NULL, $aAlign = NULL);
		//function GetAsHTML($aCaption = NULL, $aAlign = NULL, $attributes = NULL) {
		function GetAsHTML($aCaption = NULL, $aAlign = NULL) {
			$Res = "";
			if (is_null($aAlign)) {
				$aAlign = $this->getDefaultAlign();
			}
			if ($aCaption && $aAlign && $aAlign != "right") $Res .= $aCaption."&nbsp;";
			$Res .= '<input type="checkbox" name="'.$this->Name.'" value="'.$this->Value.'"';
			if ($this->Checked) {
				$Res .= ' checked';
			}
			if (!is_null($this->attributes)) {
				$Res .= HtmlUtils::attributes($this->attributes);
			}
			$Res .= '>';
			if ($aCaption && $aAlign && $aAlign == "right") $Res .= "&nbsp;".$aCaption;
			if ($aCaption && $aAlign) $Res .= "<BR>";
			return $Res;
		}

		//====================================================
		// DisplayAsHTML
		function DisplayAsHTML($aCaption = NULL, $aAlign = NULL) {
			print $this->GetAsHTML($aCaption, $aAlign);
		}

		//====================================================
		// boolean IsChecked(void);
		function IsChecked() {
			return $this->Checked;
		}

		function SetValue($value) {
			$this->Checked = (boolean) $value;
		}

		function GetValue() {
			return $this->Checked ? TRUE : FALSE;
		}
	}

	class CollectionCheckBoxField extends TField {

		var $fields;
		var $limiter;
		var $mode_view;

		function CollectionCheckBoxField($name, $collection, $checkedValues = array(), $limiter = NULL, $mode_view = 'horizontally') {
			$this->TField($name, $checkedValues);
			$this->fields = $collection;
			$this->limiter = intval($limiter) < 1 ? 1 : intval(abs($limiter));
			if ($this->limiter > 0) {
				$this->mode_view = $mode_view;
			}
			else {
				$this->mode_view = 'horizontally';
			}
		}

		function SetFromPost($POST) {
			if (isset($POST[$this->Name])) {
				$this->SetValue($POST[$this->Name]);
			}
			else {
				$this->SetValue(array());
			}
		}

		function addItem($value, $caption) {
			if (!array_key_exists($value, $this->fields)) {
				$this->fields[$value] = $caption;
			}
		}

		function checkItem($value) {
			if (!array_key_exists($value, $this->Value)) {
				$this->Value[] = $value;
			}
		}

		function GetAsHTML($tableAttr = array(), $checkBoxAttr = array()) {
			
			$res = "<table ".HtmlUtils::attributes($tableAttr)." summary=\"\">\n<tr>";
			$i = 0;

			if ($this->mode_view == 'vertically') {
				$count_td = ceil(sizeof($this->fields) / $this->limiter);
				$j = 0;
			}

			foreach ($this->fields as $value => $caption) {
				if ($this->mode_view == 'vertically') {
					if ($j == 0) {
						$res .= '<td valign="top"><table><tr>';
					}
					elseif ($j < $count_td) {
						$res .= "</tr><tr>\n";
					}
				}
				else {
					/*if($i == 0) {
					 $res .= '<tr>';
					 }*/
					if ($this->limiter > 0 && $i % $this->limiter == 0 && $i != 0) {
						$res .= "</tr><tr>\n";
					}
				}
				$res .= "<td class=\"checkbox\">".$this->getItemAsHTML($value)."</td>";
				$res .= "<td class=\"caption\">".($caption ? $caption : "&nbsp;")."</td>\n";
				if ($this->mode_view == 'vertically') {
					$j++;
					if ($j == $count_td) {
						$res .= '</tr></table></td>';
						$j = 0;
					}
				}
				$i++;
			}
			if ($this->mode_view == 'vertically' && $j != 0) {
				$res .= '</tr></table></td>';
			}
			$res .= "</tr></table>\n";
			return $res;
		}

		/*function GetAsHTML($tableAttr = array(), $checkBoxAttr = array()){
		
		 if (!$tableAttr && $this->attributes)
		 $tableAttr=$this->attributes;
		
		 $res  = "\n<table ".HtmlUtils::attributes($tableAttr)." summary=\"\"><tr valign='top'><td>\n";
		 $res .= "<table ".HtmlUtils::attributes($tableAttr)." summary=\"\">\n";
		 $i=0;
		 foreach ($this->fields as $value => $caption) {
		 $caption   = $caption ? $caption : "&nbsp;";
		 $res .= "<tr><td>".$this->getItemAsHTML($value, array(), $this->FormName)."</td><td>".$caption."</td></tr>\n";
		 $i++;
		 if ($this->limiter>0 && $i%$this->limiter==0)
		 $res .= "</table></td><td><table ".HtmlUtils::attributes($tableAttr)." summary=\"\">\n";
		 }
		 $res .= "</table>\n";
		 $res .= "</td></tr></table>\n";
		 return $res;
		 } */

		function getCaption($value) {
			return array_key_exists($value, $this->fields) ? $this->fields[$value] : "";
		}

		function getItemAsHTML($value, $checkboxAttr = array()) {
			if (!array_key_exists($value, $this->fields)) {
				return "wrong value for CollectionCheckBoxField::getItemAsHTML($value)";
			}

			$isChecked = @in_array($value, $this->Value) ? " checked" : "";
			return "<input type=\"checkbox\" name=\"".$this->Name."[]\" value=\"".$value."\" ".HtmlUtils::attributes($checkboxAttr).$isChecked.">";
		}
	}

	//  ================================   S  E  L  E  C  T   =====================================

	////////////////////////////////////////////////////////////////////////////////////////////////
	//                                TSelectOption class                     //
	////////////////////////////////////////////////////////////////////////////////////////////////
	/*
	 class TSelectOption{
	 var $Value;
	 var $Text;
	
	 function TSelectOption($aValue, $aText);
	 function GetAsHTML($aSelectedValue);
	 function DisplayAsHTML($aSelectedValue);
	 }
	 */

	class TSelectOption {
		var $Value;
		var $Text;

		//====================================================
		// Constructor
		function TSelectOption($aValue, $aText) {
			$this->Value = $aValue;
			$this->Text = $aText;
		}

		//====================================================
		// GetOption
		function GetAsHTML($aSelectedValue) {
			return sprintf("<option value=\"%s\"%s>%s</option>", htmlspecialchars($this->Value), (string) $this->Value == (string) $aSelectedValue ? " selected" : "", htmlspecialchars($this->Text));
		}

		//====================================================
		// DisplayOption
		function DisplayAsHTML($aSelectedValue) {
			print $this->GetAsHTML($aSelectedValue);
		}
	}

	class MultipleSelectOption extends TSelectOption {

		function MultipleSelectOption($value, $text) {
			$this->TSelectOption($value, $text);
		}

		function GetAsHTML($selectedValues) {
			if (!is_array($selectedValues)) {
				return TSelectOption::GetAsHTML($selectedValues);
			}
			$selected = in_array($this->Value, $selectedValues) ? " selected" : "";
			return "<option value=\"".htmlspecialchars($this->Value)."\"".$selected.">".htmlspecialchars($this->Text)."</option>";
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////
	//                                TSelectField class                      //
	////////////////////////////////////////////////////////////////////////////////////////////////

	class TSelectField extends TField {
		var $Options;
		var $attributes;

		//====================================================
		// Constructor
		function TSelectField($aName, $aValue, $values, $attributes = NULL) {
			$this->Name = $aName;
			$this->Value = $aValue;
			$this->Options = array();
			foreach ($values as $key => $value) {
				$this->Options[] = new TSelectOption($key, $value);
			}
			$this->attributes = $attributes;
		}

		/**
		 * @return string
		 */
		function GetAsHTML() {
			$count = count($this->Options);
			$Res = '<select name="'.htmlspecialchars($this->Name).'"'.HtmlUtils::attributes($this->attributes).'>';
			if ($count) {
				for ($i = 0; $i < $count; $i++) {
					$Res .= $this->Options[$i]->GetAsHTML($this->Value);
				}
			}
			$Res .= "</select>";
			return $Res;
		}

		/* option text */
		function GetTextValue() {
			$count = count($this->Options);
			$Res = "";
			if ($count) {
				for ($i = 0; $i < $count; $i++) {
					if ($this->Value == $this->Options[$i]->Value) $Res = $this->Options[$i]->Text;
				}
			}
			return $Res;
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////
	//                                TSelectDayField class                     //
	////////////////////////////////////////////////////////////////////////////////////////////////

	class TSelectDayField extends TSelectField {

		var $options;

		function TSelectDayField($aName, $aValue, $aOptions = NULL) {
			$this->Name = $aName;
			$this->Value = $aValue;
			$this->options = $aOptions;
			if (!is_null($this->options) && array_key_exists('allowNull', $this->options) && $this->options['allowNull']) {
				$nullText = array_key_exists('nullText', $this->options) ? $this->options['nullText'] : '--';
				$this->Options[] = new TSelectOption('', $nullText);
			}
			for ($i = 1; $i < 32; $i++) {
				$this->Options[] = new TSelectOption($i, sprintf("%02u", $i));
			}
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////
	//                                TSelectMonthField class                   //
	////////////////////////////////////////////////////////////////////////////////////////////////

	class TSelectMonthField extends TSelectField {

		var $options;

		function TSelectMonthField($aName, $aValue = "", $aOptions = NULL) {
			$this->Name = $aName;
			$this->options = $aOptions;
			$Months = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
			if (!is_null($this->options) && array_key_exists('allowNull', $this->options) && $this->options['allowNull']) {
				$nullText = array_key_exists('nullText', $this->options) ? $this->options['nullText'] : '--';
				$this->Options[] = new TSelectOption('', $nullText);
			}
			else {
				if (!$aValue) $aValue = date("m");
			}
			$this->Value = $aValue;
			for ($i = 1; $i <= 12; ++$i) {
				$this->Options[] = new TSelectOption($i, $Months[$i - 1]);
			}
		}

	}

	//////////////////////////////////////////////////////////////////////////////
	//                                TSelectYearField class                    //
	//////////////////////////////////////////////////////////////////////////////

	class TSelectYearField extends TField {

		var $minYear;
		var $maxYear;
		var $options;
		var $attributes;

		function TSelectYearField($aName, $aValue = "", $aStart = 0, $aEnd = 0, $options = NULL) {
			if (!$aValue) $aValue = date("Y");
			if (!$aStart) $aStart = date("Y");
			if (!$aEnd) $aEnd = date("Y") + 10;

			$this->Name = $aName;
			$this->Value = $aValue;
			$this->minYear = $aStart;
			$this->maxYear = $aEnd;
			$this->options = $options;
		}

		function GetAsHTML() {
			$result = '<select name="'.htmlspecialchars($this->Name).'"'.HtmlUtils::attributes($this->attributes).'>';
			if (!is_null($this->options) && array_key_exists('allowNull', $this->options) && $this->options['allowNull']) {
				$nullText = array_key_exists('nullText', $this->options) ? $this->options['nullText'] : '--';
				$result .= '<option value="">'.$nullText.'</option>';
			}
			if (!(!is_null($this->options) && array_key_exists('reverse', $this->options) && $this->options['reverse'])) {
				for ($i = $this->minYear; $i <= $this->maxYear; ++$i) {
					$option = "<option value=$i";
					if ($i == $this->Value) {
						$option .= " selected";
					}
					$option .= ">".$i."</option>";
					$result .= $option;
				}
			}
			else {
				for ($i = $this->maxYear; $i >= $this->minYear; --$i) {
					$option = "<option value=$i";
					if ($i == $this->Value) {
						$option .= " selected";
					}
					$option .= ">".$i."</option>";
					$result .= $option;
				}
			}
			$result .= "</select>";
			return $result;
		}

	}

	////////////////////////////////////////////////////////////////////////////////////////////////
	//                                TDBSelectField class                        //
	////////////////////////////////////////////////////////////////////////////////////////////////

	class TDBSelectField extends TSelectField {

		function TDBSelectField($aName, $aValue, $aQuery, $aDB, $attributes = NULL, $staticOptions = NULL) {
			$this->Name = $aName;
			$this->Value = $aValue;
			if ($attributes) {
				$this->attributes = $attributes;
			}
			if (!is_null($staticOptions)) {
				foreach ($staticOptions as $key => $value) {
					$this->Options[] = new TSelectOption($key, $value);
				}
			}
			if ($aQuery) {
				if ($mysql_result = mysql_query($aQuery, $aDB)) {
					while ($r = mysql_fetch_array($mysql_result)) {
						$this->Options[] = new TSelectOption($r[0], $r[1]);
					}
				}
				else {
					print mysql_error();
				}
			}
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////
	//                                TObjectArraySelectField class             //
	////////////////////////////////////////////////////////////////////////////////////////////////

	class TObjectArraySelectField extends TSelectField {
		//====================================================
		// Constructor
		function TObjectArraySelectField($aName, $aValue, $aValues, $keyField, $valueField, $attributes = NULL) {
			$this->Name = $aName;
			$this->Value = $aValue;
			$this->Options = array();
			foreach ($aValues as $obj) {
				$this->Options[] = new TSelectOption($obj->$keyField, $obj->$valueField);
			}
			$this->attributes = $attributes;
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////
	//                           TCollectionSelectField class                                     //
	////////////////////////////////////////////////////////////////////////////////////////////////

	class TCollectionSelectField extends TSelectField {

		function TCollectionSelectField($aName, $aValue) {
			$this->Name = $aName;
			$this->Value = $aValue;
		}

		function GetAsHTML($aCollection) {
			$count = count($aCollection);
			$Res = '<select name="'.htmlspecialchars($this->Name).'">';
			if ($count) {
				for ($i = 0; $i < $count; $i++) {
					$Res .= $aCollection[$i]->GetAsHTML($this->Value);
				}
			}
			else {
				$Res .= '<option value="">(No options)</option>';
			}
			$Res .= "</select>";
			return $Res;
		}

		function DisplayAsHTML($aCollection) {
			print $this->GetAsHTML($aCollection);
		}
	}

	//  =================================-  R  A  D  I  O  =========================================

	////////////////////////////////////////////////////////////////////////////////////////////////
	//                                TRadioOption class                      //
	////////////////////////////////////////////////////////////////////////////////////////////////
	/*
	 class TRadioOption{
	 var $Name;
	 var $Value;
	 var $Caption;
	
	 function TRadioOption($aName, $aValue, $aCaption = "");
	 function GetAsHTML($aSelectedValue, $aCaption = "", $aAlign = "right");
	 function DisplayAsHTML($aSelectedValue);
	 }
	 */

	class TRadioOption {
		var $Value;
		var $Caption;
		var $attributes;

		//====================================================
		// Constructor
		function TRadioOption($aValue, $aCaption = "", $attributes = NULL) {
			$this->Value = $aValue;
			$this->Caption = $aCaption;
			$this->attributes = $attributes;
		}

		//====================================================
		// GetAsHTML
		function GetAsHTML($aName, $aSelectedValue, $aAlign = "left") {
			$Res = "";
			if ($this->Caption && $aAlign != "left") $Res = $this->Caption."&nbsp;";
			$Res .= sprintf("<input type=\"radio\" name=\"%s\" value=\"%s\"%s ".HtmlUtils::attributes($this->attributes).">", htmlspecialchars($aName), htmlspecialchars($this->Value), $this->Value == $aSelectedValue ? ' checked="checked"' : "");
			if ($this->Caption && $aAlign == "left") $Res .= "&nbsp;".$this->Caption;
			#if ($this->Caption) $Res .= "<br>";
			return $Res;
		}

		//====================================================
		// DisplayAsHTML
		function DisplayAsHTML($aName, $aSelectedValue, $aAlign = "left") {
			print $this->GetAsHTML($aName, $aSelectedValue, $aAlign);
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////
	//                                TRadioField class                         //
	////////////////////////////////////////////////////////////////////////////////////////////////

	class TRadioField extends TField {
		var $Options;
		var $attributes;
		var $separator;

		//====================================================
		// Constructor
		function TRadioField($aName, $aValue, $aValues, $attributes = NULL, $aSeparator = "<br>") {
			$this->Name = $aName;
			$this->Value = $aValue;
			$this->Options = array();
			$this->attributes = $attributes;
			$this->separator = $aSeparator;
			reset($aValues);
			while (list($key, $value) = each($aValues)) $this->Options[count($this->Options)] = new TRadioOption($key, $value, $attributes);
		}

		//====================================================
		// GetAsHTML
		function GetAsHTML($aAlign = "left") {
			$container_class = 'input-group';
			
			
			if (strpos($this->attributes, 'class=') !== false) {
				$attributes = preg_replace('/(class=)(\'|")/', 'class=$2'.$container_class.' ', $this->attributes);				
			} 
			else {
				$attributes .= ' class="'.$container_class.'"';
			}
			
			$this->attributes = '';
			
			
			$Res = '<span ' . $attributes .'>';
			$count = count($this->Options);
			for ($i = 0; $i < $count; $i++) {
				$Res .= '<span class="option">';
				$Res .= $this->Options[$i]->GetAsHTML($this->Name, $this->Value, $aAlign);
				$Res .= $this->separator;
				$Res .= '</span>';
			}
			$Res .= "</span>";
			return $Res;
		}

		//====================================================
		// GetAsHTMLByID
		function GetAsHTMLByID($aID, $aAlign = "left") {
			$count = count($this->Options);
			for ($i = 0; $i < $count; $i++) {
				if ($this->Options[$i]->Value == $aID) {
					$Res = $this->Options[$i]->GetAsHTML($this->Name, $this->Value, $aAlign);
				}
			}
			return $Res;
		}

		//====================================================
		// DisplayAsHTML
		function DisplayAsHTML($aAlign = "left") {
			print $this->GetAsHTML($aAlign);
		}

		//====================================================
		// DisplayAsHTMLByID
		function DisplayAsHTMLByID($aID, $aAlign = "left") {
			print $this->GetAsHTMLByID($aID, $aAlign);
		}

	}

	////////////////////////////////////////////////////////////////////////////////////////////////
	//                                TDBRadioField class                       //
	////////////////////////////////////////////////////////////////////////////////////////////////

	class TDBRadioField extends TRadioField {

		//====================================================
		// Constructor
		function TDBRadioField($aName, $aValue, $aQuery, $aDB) {
			$this->Name = $aName;
			$this->Value = $aValue;
			$this->Options = array();
			if ($aDB && $aQuery) {
				if ($result = mysql_query($aQuery, $aDB)) {
					while ($r = mysql_fetch_array($result)) $this->Options[count($this->Options)] = new TRadioOption($r[0], $r[1]);
				}
				else print mysql_error();
			}
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////
	//                                TCollectionRadioField class                       //
	////////////////////////////////////////////////////////////////////////////////////////////////

	class TCollectionRadioField extends TRadioField {

		//====================================================
		// Constructor
		function TCollectionRadioField($aName, $aValue) {
			$this->Name = $aName;
			$this->Value = $aValue;
		}

		//====================================================
		// GetAsHTML
		function GetAsHTML($aCollection, $aAlign = "right") {
			$count = count($aCollection);
			for ($i = 0; $i < $count; $i++) {
				$Res .= $aCollection->GetAsHTML($this->Name, $this->Value, $aAlign);
			}
			return $Res;
		}
	}

	// =======================  D  A  T  E  ======================================

	//////////////////////////////////////////////////////////////////////////////
	//                                TDateField class                          //
	//////////////////////////////////////////////////////////////////////////////

	class TDateField extends TField {
		var $IsUSFormat;
		var $options;

		//====================================================
		// Constructor
		function TDateField($aName, $aValue = "", $aIsUSFormat = false, $options = NULL) {
			$this->Name = $aName;
			$this->IsUSFormat = $aIsUSFormat;
			$this->options = $options;
			if ($aValue) {
				$this->Value = $aValue;
			}
			else if (!$this->allowNull()) {
				$this->Value = date("Y-m-d");
			}
		}

		//====================================================
		// void SetFromPost($POST);
		function SetFromPost($POST) {
			$year = false;
			$month = false;
			$day = false;
			if (isset($POST[$this->Name."_day"])) $day = $POST[$this->Name."_day"];
			if (isset($POST[$this->Name."_month"])) $month = $POST[$this->Name."_month"];
			if (isset($POST[$this->Name."_year"])) $year = $POST[$this->Name."_year"];
			if ($year && $month && $day) $this->Value = sprintf("%04u-%02u-%02u", $year, $month, $day);
		}

		//====================================================
		// string GetStringForGet(void);
		function GetStringForGet() {
			$date = explode("-", $this->Value);
			$Res = $this->Name."_day=".$date[2];
			$Res .= "&".$this->Name."_month=".$date[1];
			$Res .= "&".$this->Name."_year=".$date[0];
			return $Res;
		}

		//====================================================
		// string GetAsHTML(void);
		function GetAsHTML() {
			// YYYY-MM-DD
			$date = explode("-", $this->Value);
			$yearOptions = array();
			$monthOptions = array();
			$dayOptions = array();
			if (!is_null($this->options)) {
				if ($this->allowNull()) {
					$yearOptions['allowNull'] = TRUE;
					$monthOptions['allowNull'] = TRUE;
					$dayOptions['allowNull'] = TRUE;
					if (array_key_exists('year.nullText', $this->options)) {
						$yearOptions['nullText'] = $this->options['year.nullText'];
					}
					if (array_key_exists('month.nullText', $this->options)) {
						$monthOptions['nullText'] = $this->options['month.nullText'];
					}
					if (array_key_exists('day.nullText', $this->options)) {
						$dayOptions['nullText'] = $this->options['day.nullText'];
					}
				}
				if (array_key_exists('year.reverse', $this->options) && $this->options['year.reverse']) {
					$yearOptions['reverse'] = TRUE;
				}
			}
			$SD = new TSelectDayField($this->Name."_day", (int) $date[2], $dayOptions);
			$SM = new TSelectMonthField($this->Name."_month", (int) $date[1], $monthOptions);
			$minYear = date('Y') + (!is_null($this->options) && array_key_exists('year.min', $this->options) ? intval($this->options['year.min']) : 0);
			$maxYear = date('Y') + (!is_null($this->options) && array_key_exists('year.max', $this->options) ? $this->options['year.max'] : 10);
			$SY = new TSelectYearField($this->Name."_year", $date[0], $minYear, $maxYear, $yearOptions);
			$separator = !is_null($this->options) && array_key_exists('separator', $this->options) ? $this->options['separator'] : ' / ';

			if ($this->IsUSFormat) {
				$Res = $SM->GetAsHTML().$separator.$SD->GetAsHTML().$separator.$SY->GetAsHTML();
			}
			else {
				$Res = $SD->GetAsHTML().$separator.$SM->GetAsHTML().$separator.$SY->GetAsHTML();
			}
			return $Res;
		}

		function allowNull() {
			return !is_null($this->options) && array_key_exists('allowNull', $this->options) && $this->options['allowNull'];
		}

	}

	////////////////////////////////////////////////////////////////////////////////////////////////
	//                                TDateCalendarField class                                //
	//  Control for dropdown calendar with input field
	//  Page must contain js files: calendar.js, calendar_setup.js
	//  and css file for calendar
	//  URL: http://www.dynarch.com/projects/calendar/
	//  local: \\ORION\Distrib\jscalendar-1.0
	////////////////////////////////////////////////////////////////////////////////////////////////

	class TDateCalendarField extends TDateField {
		var $image;
		var $dFormat;

		function isUnsupportedBrowser() {
			$s = $_SERVER['HTTP_USER_AGENT'];
			return (strpos($s, 'Mac_PowerPC') || strpos($s, 'Macintosh')) && strpos($s, 'MSIE 5');
		}

		function TDateCalendarField($aName, $aValue = "", $aSize = "20", $aImage = "", $aFormat = "%Y-%m-%d") {
			if ($this->isUnsupportedBrowser()) {
				$this->TDateField($aName, $aValue, true);
				return;
			}
			$this->TField($aName, $aValue);
			$this->image = $aImage;
			$this->size = $aSize;
			$this->dFormat = $aFormat;
		}

		function SetFromPost($POST) {
			if ($this->isUnsupportedBrowser()) {
				TDateField::SetFromPost($POST);
			}
			else {
				if (isset($POST[$this->Name])) $this->Value = $POST[$this->Name];
			}
		}

		function GetAsHTML() {
			if ($this->isUnsupportedBrowser()) {
				return TDateField::GetAsHTML();
			}

			$input_id = $this->Name."_inp";
			$button_id = $this->Name."_btn";
			$script = "<script type=\"text/javascript\">\n"."<!--\n"." var set = new Object();\n"." set.inputField  = \"$input_id\";\n"." set.ifFormat    = \"".$this->dFormat."\";\n"." set.button      = \"$button_id\";\n"." set.align       = \"Tl\";\n"." set.singleClick = true;\n"." Calendar.setup(set);\n"."-->\n"."</script>\n";

			$res = "<input readonly=\"true\" size=\"".$this->size."\" name=\"".$this->Name."\" id=\"$input_id\" maxlength=\"10\" onfocus=\"javascript:vDateType='1'\" onkeyup=\"DateFormat(this,this.value,event,false,'1')\" onblur=\"DateFormat(this,this.value,event,true,'1')\" size=\"21\" value=\"".$this->GetHTMLSafetyValue()."\" type=\"text\">";
			$res .= "<img align=\"middle\" src=\"".$this->image."\" id=\"$button_id\" style=\"cursor: pointer;\" title=\"Date selector\">";
			//$res = "<input readonly=\"true\" size=\"".$this->size."\" name=\"".$this->Name."\" id=\"$input_id\" maxlength=\"10\"  size=\"21\" value=\"".$this->GetHTMLSafetyValue()."\" type=\"text\"><button type=\"reset\" id=\"".$button_id."\">...</button>";
			return $res.$script;
			//    return sprintf("<input type=\"hidden\" name=\"%s\" value=\"%s\">", $this->Name, $this->GetHTMLSafetyValue());

		}
	}
?>
