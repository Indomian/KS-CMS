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

function smarty_modifier_spell_age($val)
{
	$arDate=explode('.',$val);
	$arMonthes=array(
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
		'декабря',
	);
	return $arDate[0].' '.$arMonthes[$arDate[1]-1].' '.$arDate[2];
}
