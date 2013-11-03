<?php
function smarty_modifier_commify($string, $decimals=0, $dec_point='.', $thousands_sep=',')
{
    return number_format($string, $decimals, $dec_point, $thousands_sep);
}
?>