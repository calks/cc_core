<?php

		
	class coreDateFormField extends coreTextFormField {
		
		protected $day_names;
		protected $month_names;
		protected $month_names_short;
		protected $first_day;
		protected $date_format;
		
		public function __construct($name, $params) {
			parent::__construct($name, $params);
			$this->id = 'datepicker_' . md5(uniqid());
			$this->attr('id', $this->id);
			
			$use_russian_locale = defined('CURRENT_LANGUAGE') && defined('LANGUAGES_RUSSIAN') && CURRENT_LANGUAGE == LANGUAGES_RUSSIAN;  
			
			
			if ($use_russian_locale) {
				$this->day_names = array('Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб');
				$this->month_names = array('Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь');
				$this->month_names_short = array('Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь');
				$this->first_day = 1;
				$this->date_format = 'dd.mm.yy';
			}
			else {
				$this->day_names = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
				$this->month_names = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
				$this->month_names_short = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
				$this->first_day = 0;
				$this->date_format = 'mm/dd/yy';
			}			
		}
		
		public function GetAsHTML() {
			$out = parent::GetAsHTML();
			$fieldname = $this->field_name;
			$day_names = json_encode($this->day_names);
			$month_names = json_encode($this->month_names);
			$month_names_short = json_encode($this->month_names_short);
			
			$out .= "
				<script type=\"text/javascript\">
					jQuery('#".$this->id."').datepicker({
						dayNamesMin: $day_names,
						monthNames: $month_names,			
						monthNamesShort: $month_names_short,
						firstDay : $this->first_day,
						dateFormat: '$this->date_format',						
						beforeShow: function() {
							var widget = $(this).datepicker('widget');
							if (!widget.parents('div:first').hasClass('jquery-ui')) {
								widget.wrap('<div class=\"jquery-ui\"></div>');
							} 
    					},
						changeMonth: true,
						changeYear: true		 
					});
				</script>	
			";
			
			return $out;
		}
		
		public function SetValue($mysql_date) {
			$this->value = coreFormattingLibrary::dateFromMysql($mysql_date, $this->date_format);
		}
		
		public function GetValue() {
			return coreFormattingLibrary::dateToMysql($this->value, $this->date_format);
		}
		
		
		
		public function SetFromPost($POST) {						
			$value = Request::getFieldValue($this->field_name, $POST);
			$this->value = $value;
		}
		
		
		
		
	}
	