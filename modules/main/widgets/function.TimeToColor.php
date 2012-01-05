<?php
/**
 * @filesource function.TimeToColor.php
 * Функция для отладки
 * Файл проекта kolos-cms.
 *
 * @since 14.07.2009
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.7
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

function smarty_function_TimeToColor($params,&$smarty)
{
	return 'white';
	$time=$params['time'];
	$time*=1000;
	$red=round($time/65536);
	$time=$time-$red << 16;
	$green=round($time/255);
	$blue=$time-$green << 8;
	return 'RGB('.$red.','.$green.','.$blue.')';
}
