<?php
/**
 * @file db_structure.php
 * Файл со структурой таблиц модуля Обсуждения
 * Файл проекта Update Server Dev.
 *
 * Создан 27.10.2010
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.5.4-14
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

$arStructure=array(
	'wave_posts'=>array(
		'id' => array(
			'Field'	=>	'id',
			'Type'	=> 	'int(11) unsigned',
			'Null'	=>	'NO',
			'Key'	=>	'PRI',
			'Default'=>	'0',
			'Extra'	=>	'auto_increment',
		),
		'hash' => array(
			'Field'	=>	'hash',
			'Type'	=> 	'char(255)',
			'Null'	=>	'NO',
			'Key'	=>	'',
			'Default'=>	'',
			'Extra'	=>	'',
		),
		'active' => array(
			'Field'	=>	'active',
			'Type'	=> 	'int(1) unsigned',
			'Null'	=>	'NO',
			'Key'	=>	'',
			'Default'=>	'1',
			'Extra'	=>	'',
		),
		//Три следующих ячейки пока не используются, но в будущем обеспечат
		//древовидность комментариев
		'parent_id'=>array(
			'Field'	=>	'parent_id',
			'Type'	=> 	'int(11) unsigned',
			'Null'	=>	'NO',
			'Key'	=>	'',
			'Default'=>	'0',
			'Extra'	=>	'',
		),
		'left_margin'=>array(
			'Field'	=>	'left_margin',
			'Type'	=> 	'int(11) unsigned',
			'Null'	=>	'NO',
			'Key'	=>	'',
			'Default'=>	'0',
			'Extra'	=>	'',
		),
		'right_margin'=>array(
			'Field'	=>	'right_margin',
			'Type'	=> 	'int(11) unsigned',
			'Null'	=>	'NO',
			'Key'	=>	'',
			'Default'=>	'0',
			'Extra'	=>	'',
		),
		'depth'=>array(
			'Field'	=>	'depth',
			'Type'	=> 	'int(11) unsigned',
			'Null'	=>	'NO',
			'Key'	=>	'',
			'Default'=>	'0',
			'Extra'	=>	'',
		),
		'content'=>array(
			'Field'=>'content',
			'Type'=>'text',
			'Null'=>'NO',
			'Key'=>'',
			'Default'=>'',
			'Extra'=>'',
		),
		'date_add' => array(
			'Field'	=>	'date_add',
			'Type'	=> 	'int(11) unsigned',
			'Null'	=>	'NO',
			'Key'	=>	'',
			'Default'=>	'0',
			'Extra'	=>	'',
		),
		'user_id' => array(
			'Field'	=>	'user_id',
			'Type'	=> 	'int(11) unsigned',
			'Null'	=>	'NO',
			'Key'	=>	'',
			'Default'=>	'0',
			'Extra'	=>	'',
		),
		'user_name' => array(
			'Field'	=>	'user_name',
			'Type'	=>	'char(255)',
			'Null'	=>	'NO',
			'Key'	=>	'',
			'Default'=> '',
			'Extra'	=>	'',
		),
		'user_email' => array(
			'Field'	=>	'user_email',
			'Type'	=>	'char(255)',
			'Null'	=>	'NO',
			'Key'	=>	'',
			'Default'=> '',
			'Extra'	=>	'',
		),
		'rate_usefull' => array(
			'Field'	=>	'rate_usefull',
			'Type'	=> 	'int(11) unsigned',
			'Null'	=>	'NO',
			'Key'	=>	'',
			'Default'=>	'0',
			'Extra'	=>	'',
		),
		'rate_useless' => array(
			'Field'	=>	'rate_useless',
			'Type'	=> 	'int(11) unsigned',
			'Null'	=>	'NO',
			'Key'	=>	'',
			'Default'=>	'0',
			'Extra'	=>	'',
		),
	),
	'wave_rating_locks'=>array(
		'id' => array(
			'Field'	=>	'id',
			'Type'	=> 	'int(11) unsigned',
			'Null'	=>	'NO',
			'Key'	=>	'PRI',
			'Default'=>	'0',
			'Extra'	=>	'auto_increment',
		),
		'comment_id' => array(
			'Field'	=>	'comment_id',
			'Type'	=> 	'int(11) unsigned',
			'Null'	=>	'NO',
			'Key'	=>	'',
			'Default'=>	'0',
			'Extra'	=>	'',
		),
		'user_id' => array(
			'Field'	=>	'user_id',
			'Type'	=> 	'int(11) unsigned',
			'Null'	=>	'NO',
			'Key'	=>	'',
			'Default'=>	'0',
			'Extra'	=>	'',
		),
		'date' => array(
			'Field'	=>	'date',
			'Type'	=> 	'int(11) unsigned',
			'Null'	=>	'NO',
			'Key'	=>	'',
			'Default'=>	'0',
			'Extra'	=>	'',
		),
	),
);
?>