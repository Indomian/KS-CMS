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

if($sParam2 == 'rate')
{
	$sType='varchar(255)';
	$sValue=floatval($value).'|1';
}
else
{
	$sType='float';
	$sValue=0;
}
?>
