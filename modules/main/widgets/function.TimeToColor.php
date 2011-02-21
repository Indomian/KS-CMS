<?php
/**
 * \file function.TimeToColor.php
 * Функция для отладки
 * Файл проекта kolos-cms.
 * 
 * Создан 14.07.2009
 *
 * \author blade39
 * \version 
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

function smarty_function_TimeToColor($params,&$smarty)
{
	//if(!KS_DEBUG) return '';
	$time=$params['time'];
	$time*=10000000;
	$red=round($time/65536);
	$time=$time-$red*65536;
	$green=round($time/255);
	$blue=round($time-$green*255);
	return 'white';
	return 'RGB('.$red.','.$green.','.$blue.')';
}
?>
