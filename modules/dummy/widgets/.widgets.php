<?php
/**
 * Файл с описанием виджетов и параметром, указывающем на корректность виджетов
 *
 * @file dummy/widgets/.widgets.php
 * @author BlaDe39 <blade39@kolosstudio.ru>
 *
 * @version 2.6
 * @since 31.08.2011
 *
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

$arWidgets = array
(
	"dummy" => array
	(
		"name" => "Пустой",
		"descr" => "Виджет ничего не делает",
		"has_widget" => 1,
		"help" => ""
	),
);
