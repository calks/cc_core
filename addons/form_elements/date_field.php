<?php

		
	class coreFormElementsAddonDateField extends coreFormElementsAddonEditField {
		
		protected $uniqid;
		
		public function __construct($name, $params) {
			$page = Application::getPage();
						
			parent::__construct($name, $params);
			
			$this->uniqid = 'datepicker_' . md5(uniqid());
			$this->addClass($this->uniqid);			
		}
		
		public function GetAsHTML() {			
			$out = parent::GetAsHTML();
			$fieldname = $this->Name;			
			
			$out .= "
				<script type=\"text/javascript\">
					jQuery('input.".$this->uniqid."').datepicker({
						dayNamesMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
						monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],			
						monthNamesShort: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
						firstDay : 1,
						dateFormat: 'dd.mm.yy',						
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
		
	}
	