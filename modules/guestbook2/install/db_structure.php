<?php
/**
 * @file db_structure.php
 * Файл со структурой таблиц модуля Гостевая книга
 * Файл проекта Update Server Dev.
 * 
 * Создан 08.10.2010
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.5.4-14 
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

$arStructure=array(
	'gb2_posts'=>array(
		'id' => array(
			'Field'	=>	'id',
			'Type'	=> 	'int(11) unsigned',
			'Null'	=>	'NO',
			'Key'	=>	'PRI',
			'Default'=>	'0', 
			'Extra'	=>	'auto_increment',
		),
		'category_id' => array(
			'Field'	=>	'category_id',
			'Type'	=> 	'int(11) unsigned',
			'Null'	=>	'NO',
			'Key'	=>	'',
			'Default'=>	'0', 
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
		'title' => array(
			'Field'	=>	'title',
			'Type'	=>	'char(255)',
			'Null'	=>	'NO',
			'Key'	=>	'',  
			'Default'=> '', 
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
		'date_answer' => array(
			'Field'	=>	'date_answer',
			'Type'	=> 	'int(11) unsigned',
			'Null'	=>	'NO',
			'Key'	=>	'',
			'Default'=>	'0', 
			'Extra'	=>	'',
		),
		'date_shown' => array(
			'Field'	=>	'date_shown',
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
	),
	'gb2_answers'=>array(
		'id' => array(
			'Field'	=>	'id',
			'Type'	=> 	'int(11) unsigned',
			'Null'	=>	'NO',
			'Key'	=>	'PRI',
			'Default'=>	'0', 
			'Extra'	=>	'auto_increment',
		),
		'active' => array(
			'Field'	=>	'active',
			'Type'	=> 	'int(1) unsigned',
			'Null'	=>	'NO',
			'Key'	=>	'',
			'Default'=>	'1', 
			'Extra'	=>	'',
		),
		'post_id' => array(
			'Field'	=>	'post_id',
			'Type'	=> 	'int(11) unsigned',
			'Null'	=>	'NO',
			'Key'	=>	'',
			'Default'=>	'0', 
			'Extra'	=>	'',
		),
		'title' => array(
			'Field'	=>	'title',
			'Type'	=>	'char(255)',
			'Null'	=>	'NO',
			'Key'	=>	'',  
			'Default'=> '', 
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
		'date' => array(
			'Field'	=>	'date',
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
	),
	'gb2_categories'=>array(
		'id' => array(
			'Field'	=>	'id',
			'Type'	=> 	'int(11) unsigned',
			'Null'	=>	'NO',
			'Key'	=>	'PRI',
			'Default'=>	'0', 
			'Extra'	=>	'auto_increment',
		),
		'active' => array(
			'Field'	=>	'active',
			'Type'	=> 	'int(1) unsigned',
			'Null'	=>	'NO',
			'Key'	=>	'',
			'Default'=>	'1', 
			'Extra'	=>	'',
		),
		'title' => array(
			'Field'	=>	'title',
			'Type'	=>	'char(255)',
			'Null'	=>	'NO',
			'Key'	=>	'',  
			'Default'=> '', 
			'Extra'	=>	'',
		),
		'text_ident' => array(
			'Field'	=>	'text_ident',
			'Type'	=>	'char(255)',
			'Null'	=>	'NO',
			'Key'	=>	'',  
			'Default'=> '', 
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
		'orderation' => array(
			'Field'	=>	'orderation',
			'Type'	=> 	'int(11) unsigned',
			'Null'	=>	'NO',
			'Key'	=>	'',
			'Default'=>	'0', 
			'Extra'	=>	'',
		),
	),
);
?>