<?php
/**
 * @file modules/hints/install/db_structure.php
 * Файл со структурой базы данных модуля ringoman_auth
 * Файл проекта Update Server Dev.
 *
 * Создан 25.05.2011
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

$arStructure=array(
	'hints_data'=>array(
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
		'content'=>array(
			'Field'	=>	'content',
			'Type'	=> 	'text',
			'Null'	=>	'NO',
			'Key'	=>	'',
			'Default'=>	'',
			'Extra'	=>	'',
		),
	)
);