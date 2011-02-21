<?php
/**
 * \file savedef.php
 * Файл для сохранения настроек поля текст по умолчанию
 * Файл проекта kolos-cms.
 * 
 * Создан 16.09.2009
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 1.0
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

$sType='int(11)';
if(strlen($value)>0)
{
	$arTime=strptime($value,$arField['option_1']);
	$sValue=mktime($arTime['tm_hour'],$arTime['tm_min'],$arTime['tm_sec'],$arTime['tm_mon'],$arTime['tm_mday'],$arTime['tm_year']);
}
else
{
	$sValue=0;
}
?>
