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

if(intval($_POST['CM_option_1'])==0)
{
	$sType='text';
	$sValue=$value;
}
elseif(intval($_POST['CM_option_1'])<256)
{
	$sType='char('.intval($_POST['CM_option_1']).')';
	$sValue=mb_substr($value,0,intval($_POST['CM_option_1']),'UTF-8');
}
else
{
	$sType='text';
	$sValue=mb_substr($value,0,intval($_POST['CM_option_1']),'UTF-8');
}
?>
