<?php

/**
 * Файл с описанием виджетов и параметром, указывающим на корректность виджетов
 * 
 * @filesource .widgets.php
 * @author North-E <pushkov@kolosstudio.ru>
 * 
 * @version 1.0
 * @since 03.06.2009
 */

$arWidgets = array
(
	"Captcha" => array
	(
		"name" => "Каптча",
		"descr" => "Виджет выводит картинку CAPTCHA",
		"has_widget" => 1
	),
	'SysNotice'=>array(
		'name'=>'Системное уведомление',
		'descr'=>'Виджет выполняет вывод системных уведомлений (результаты операций, предупрждения и т.д.)',
		'has_widget'=>1
	),
	'TimeFormat'=>array(
		'name'=>'Форматированное время',
		'descr'=>'Форматирует время из unix_timestamp в формат &quot;15 ноября 2009&quot;',
		'has_widget'=>0
	),
);
?>