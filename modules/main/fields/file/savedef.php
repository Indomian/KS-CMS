<?php
/**
 * \file savedef.php
 * Файл для сохранения настроек поля файл по умолчанию
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

$sType='char(255)';

if($data['numberSize']=='k')
{
	$data['CM_option_1']*=1024;
}
if($data['numberSize']=='m')
{
	$data['CM_option_1']*=1024*1024;
}
$sValue='';
?>
