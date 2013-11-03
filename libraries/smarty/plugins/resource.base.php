<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Файл:    resource.base.php
 * Тип:     resource
 * Имя:     base
 * Назначение:  Получает шаблон
 * -------------------------------------------------------------
 */
function base_get_template ($tpl_name, &$tpl_source, &$smarty_obj) {
    global $template;
    if ($template)
        $tpl_source=$template->CONTENT;
    return true;
}

function base_get_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj) {
    $tpl_timestamp=time();
    return true;
}

function base_get_secure($tpl_name, &$smarty_obj) {
    return true;
}

function base_get_trusted($tpl_name, &$smarty_obj) {
}
?>
