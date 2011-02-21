<?php

/**
 * Плагин Смарти, форматирует дату
 * 
 * @author BlaDe39 <blade39@kolosstudio.ru>
 * @version 1.0
 * @since 01.03.2010
 */

function smarty_function_TimeFormat($params, &$smarty)
{
	$monthes=array(
		'января',
		'февраля',
		'марта',
		'апреля',
		'мая',
		'июня',
		'июля',
		'августа',
		'сентября',
		'октября',
		'ноября',
		'декабря'
	);
	return date('d',$params['time']).' '.$monthes[intval(date('m',$params['time']))-1].' '.date('Y',$params['time']);
}

?>