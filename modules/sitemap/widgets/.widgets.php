<?php
/**
 * \file .widgets.php
 * Файл с описаниями виджетов модуля "Гостевая книга"
 * Файл проекта kolos-cms.
 * 
 * Создан 03.11.2009
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 2.5.2
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

$arWidgets = array
(
	"sitemap"=>array(
		'name'=>'Карта сайта',
		'descr'=>'Виджет вывода карты сайта',
		'has_widget'=>0,
	),
);
?>
