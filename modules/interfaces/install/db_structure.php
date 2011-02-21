<?php
/**
 * @file db_structure.php
 * Файл со структурой базы данных модуля interfaces
 * Файл проекта KS-CMS
 *
 * Создан 21.02.2011
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.5.6
 * @todo Добавить все таблицы
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

$arStructure=array(
	'interfaces_smilies'=>array(
		'id' => array(
			'Field'	=>	'id',
			'Type'	=> 	'int(11) unsigned',
			'Null'	=>	'NO',
			'Key'	=>	'PRI',
			'Default'=>	'0',
			'Extra'	=>	'auto_increment',
		),
		'smile'=>array(
			'Field'=>'smile',
			'Type'=>'char(50)',
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
		'group'=>array(
			'Field'=>'group',
			'Type'=>'char(255)',
			'Null'=>'NO',
			'Key'=>'',
			'Default'=>'',
			'Extra'=>'',
		),
	)
);