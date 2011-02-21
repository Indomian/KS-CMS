<?php
/**
 * \file config.php
 * Файл для конфигурации поля типа Текст, выводит параметры для настройки длины поля
 * Файл проекта kolos-cms.
 * 
 * Создан 16.09.2009
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 1.0
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

$sResult='<table class="layout"><tr><td width="30%">Максимальное значение рейтинга (0 - не ограничивать): </td>' .
		'<td width="70%"><input type="text" class="form_input" ' .
		'name="'.$params['prefix'].'option_1" ' .
		'value="'.intval($params['field']['option_1']).'" ' .
		'id="'.$params['prefix'].'option_1" /></td></tr>' .
		'<tr><td>Значение по умолчанию: </td><td><input type="text" class="form_input" ' .
		'name="'.$params['prefix'].'ext_'.$params['field']['title'].'" '.
		'value="'.$params['field']['default'].'" ' .
		'id="'.$params['prefix'].'ext_'.$params['field']['title'].'" /></td></tr>' .
		'<tr><td>Режим работы</td><td><select ' .
		'name="'.$params['prefix'].'option_2" ' .
		'id="'.$params['prefix'].'option_2">' .
		'<option value="rate"'.($params['field']['option_2']=='rate'?' selected="selected"':'').'>Рейтинг</option>' .
		'<option value="summ"'.($params['field']['option_2']=='summ'?' selected="selected"':'').'>Сумма голосов</option>' .
		'</select></td></tr></table>';
?>
