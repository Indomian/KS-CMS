<?php
/**
 * \file savedef.php
 * Сюда сделать описание файла
 * Файл проекта CMS-local.
 * 
 * Создан 14.01.2009
 *
 * \author blade39
 * \version 
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

if($_POST['CM_ext_'.$arField['title'].'_sw']==1)
{
	$sValue=-intval($_POST['CM_ext_'.$arField['title'].'_forum']);
}
elseif($_POST['CM_ext_'.$arField['title'].'_del']==1)
{
	$sValue=0;
}
else
{
	$sValue=intval($value);
}

$sType='int(11)';
?>