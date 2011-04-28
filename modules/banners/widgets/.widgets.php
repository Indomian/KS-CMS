<?php
/**
 * @file banners/.widgets.php
 * Файл с описаниями виджетов модуля "Баннеры"
 * Файл проекта kolos-cms.
 *
 * Создан 03.11.2009
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.5.5
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}
$arWidgets = array
(
	"banner"=>array(
		'name'=>'Баннер',
		'descr'=>'Виджет вывода баннера на странице',
		'has_widget'=>1,
	),
	"rotBanner"=>array(
		'name'=>'Ротация баннеров',
		'descr'=>'Виджет для выполнения ротации баннеров на странице',
		'has_widget'=>1,
	),
);
?>
