<?php
/**
 * @file db_structure.php
 * Файл со структурой базы данных модуля navigation
 * Файл проекта KS-CMS
 *
 * Создан 21.02.2011
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 * @todo Добавить все таблицы
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

$arStructure=array(
	'navigation_menu_types'=>array(
		'id' => array(
			'Field'	=>	'id',
			'Type'	=> 	'int(11) unsigned',
			'Null'	=>	'NO',
			'Key'	=>	'PRI',
			'Default'=>	'0',
			'Extra'	=>	'auto_increment',
		),
		'text_ident'=>array(
			'Field'=>'text_ident',
			'Type'=>'char(255)',
			'Null'=>'NO',
			'Key'=>'',
			'Default'=>'',
			'Extra'=>'',
		),
		'name'=>array(
			'Field'=>'name',
			'Type'=>'char(255)',
			'Null'=>'NO',
			'Key'=>'',
			'Default'=>'',
			'Extra'=>'',
		),
		'description'=>array(
			'Field'=>'description',
			'Type'=>'char(255)',
			'Null'=>'NO',
			'Key'=>'',
			'Default'=>'',
			'Extra'=>'',
		),
		'script_name'=>array(
			'Field'=>'script_name',
			'Type'=>'char(255)',
			'Null'=>'NO',
			'Key'=>'',
			'Default'=>'',
			'Extra'=>'',
		),
		'active'=>array(
			'Field'=>'active',
			'Type'=>'tinyint(1)',
			'Null'=>'NO',
			'Key'=>'',
			'Default'=>'0',
			'Extra'=>'',
		),
	),
	'navigation_menu_elements'=>array(
		'id' => array(
			'Field'	=>	'id',
			'Type'	=> 	'int(11) unsigned',
			'Null'	=>	'NO',
			'Key'	=>	'PRI',
			'Default'=>	'0',
			'Extra'	=>	'auto_increment',
		),
		'orderation'=>array(
			'Field'=>'orderation',
			'Type'=>'int(11)',
			'Null'=>'NO',
			'Key'=>'',
			'Default'=>'0',
			'Extra'=>'',
		),
		'type_id'=>array(
			'Field'=>'type_id',
			'Type'=>'int(11) unsigned',
			'Null'=>'NO',
			'Key'=>'',
			'Default'=>'0',
			'Extra'=>'',
		),
		'parent_id'=>array(
			'Field'=>'parent_id',
			'Type'=>'int(11) unsigned',
			'Null'=>'NO',
			'Key'=>'',
			'Default'=>'0',
			'Extra'=>'',
		),
		'link'=>array(
			'Field'=>'link',
			'Type'=>'char(255)',
			'Null'=>'NO',
			'Key'=>'',
			'Default'=>'',
			'Extra'=>'',
		),
		'anchor'=>array(
			'Field'=>'anchor',
			'Type'=>'char(255)',
			'Null'=>'NO',
			'Key'=>'',
			'Default'=>'',
			'Extra'=>'',
		),
		'target'=>array(
			'Field'=>'target',
			'Type'=>'char(255)',
			'Null'=>'NO',
			'Key'=>'',
			'Default'=>'',
			'Extra'=>'',
		),
		'img'=>array(
			'Field'=>'img',
			'Type'=>'char(255)',
			'Null'=>'NO',
			'Key'=>'',
			'Default'=>'',
			'Extra'=>'',
		),
	)
);
