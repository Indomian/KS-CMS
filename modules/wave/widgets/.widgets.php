<?php
/**
 * @file .widgets.php
 * Файл с описаниями виджетов модуля "Комментарии"
 * 
 * @since 27.10.2010
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.5.4-14
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

$arWidgets = array
(
	"WavePosts"=>array(
		'name'=>'Комментарии',
		'descr'=>'Виджет вывода списка комментариев и формы для их добавления',
		'has_widget'=>1,
	),
);
?>
