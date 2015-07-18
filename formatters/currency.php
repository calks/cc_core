<?php

	class coreFormattingAddonCurrencyFormatter {
	
		public function format($amount, $preset_name='default') {
			$out = number_format($amount, 2, ',', ' ');
			if (defined('CURRENCY_LABEL')) $out .= CURRENCY_LABEL;
			return $out;
		}
	
	}