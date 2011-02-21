<?php
/**
 * @file modifier.spell_age.php
 * Преобразует дату в текстовое представление
 * Файл проекта kolos-cms.
 *
 * Создан 13.12.2010
 *
 * @author blade39 <blade39@kolosstudio.ru>
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/main/libs/class.CMoneyConverter.php';

function smarty_modifier_spell_phone($val)
{
    if( strpos($val, '(') !== false && strpos($val, ')') !== false )
    {
        $val = str_replace('(', '<i>', $val);
        $val = str_replace(')', '</i>', $val);
    }
    else
    {
        $code = substr($val, 0, 3);
        $val = str_replace($code,'<i>'.$code.'</i>',$val);
    }
	return $val;
}

