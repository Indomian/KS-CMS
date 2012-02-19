<?php
/**
 * @file db_structure.php
 * Файл со структурой базы данных сервера обновлений
 * Файл проекта Update Server Dev.
 *
 * Создан 17.02.2010
 *
 * @author blade39
 * @version 1.0
 * @todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

$arStructure=array(
	'catsubcat_links'=>array(
		'id' => array(
			'Field'	=>	'id',
			'Type'	=> 	'int(11) unsigned',
			'Key'	=>	'PRI',
			'Default'=>	'0',
			'Extra'	=>	'auto_increment',
		),
		'element_id'=>array(
			'Field'=>'element_id',
			'Type'=>'int(11) unsigned',
			'Default'=>'0',
		),
		'category_id'=>array(
			'Field'=>'category_id',
			'Type'=>'int(11) unsigned',
			'Default'=>'0',
		),
	),
	'catsubcat_catsubcat'=>array(
		'id' => array(
			'Field'	=>	'id',
			'Type'	=> 	'int(11) unsigned',
			'Key'	=>	'PRI',
			'Default'=>	'0',
			'Extra'	=>	'auto_increment',
		),
		'active'=>array(
			'Field'=>'active',
			'Type'=>'tinyint(1) unsigned',
			'Default'=>'0',
		),
		'orderation'=>array(
			'Field'=>'orderation',
			'Type'=>'int(11)',
			'Default'=>'1',
		),
		'text_ident'=>array(
			'Field'=>'text_ident',
		),
		'parent_id'=>array(
			'Field'=>'parent_id',
			'Type'=>'int(11) unsigned',
			'Default'=>'0',
		),
		'title'=>array(
			'Field'=>'title',
		),
		'description'=>array(
			'Field'=>'description',
			'Type'=>'text',
			'Default'=>'',
		),
		'content'=>array(
			'Field'=>'content',
			'Type'=>'text',
			'Default'=>'',
		),
		'img'=>array(
			'Field'=>'img',
		),
		'date_add'=>array(
			'Field'=>'date_add',
			'Type'=>'int(11) unsigned',
			'Default'=>'0',
		),
		'date_edit'=>array(
			'Field'=>'date_edit',
			'Type'=>'int(11) unsigned',
			'Default'=>'0',
		),
		'views_count'=>array(
			'Field'=>'views_count',
			'Type'=>'int(11) unsigned',
			'Default'=>'0',
		),
		'seo_title'=>array(
			'Field'=>'seo_title',
		),
		'seo_description'=>array(
			'Field'=>'seo_description',
		),
		'seo_keywords'=>array(
			'Field'=>'seo_keywords',
		),
	),
	'catsubcat_element'=>array(
		'id' => array(
			'Field'	=>	'id',
			'Type'	=> 	'int(11) unsigned',
			'Key'	=>	'PRI',
			'Default'=>	'0',
			'Extra'	=>	'auto_increment',
		),
		'active'=>array(
			'Field'=>'active',
			'Type'=>'tinyint(1) unsigned',
			'Default'=>'0',
		),
		'orderation'=>array(
			'Field'=>'orderation',
			'Type'=>'int(11)',
			'Default'=>'1',
		),
		'text_ident'=>array(
			'Field'=>'text_ident',
		),
		'parent_id'=>array(
			'Field'=>'parent_id',
			'Type'=>'int(11) unsigned',
			'Default'=>'0',
		),
		'title'=>array(
			'Field'=>'title',
		),
		'description'=>array(
			'Field'=>'description',
			'Type'=>'text',
			'Default'=>'',
		),
		'content'=>array(
			'Field'=>'content',
			'Type'=>'text',
		),
		'img'=>array(
			'Field'=>'img',
		),
		'date_add'=>array(
			'Field'=>'date_add',
			'Type'=>'int(11) unsigned',
			'Default'=>'0',
		),
		'date_edit'=>array(
			'Field'=>'date_edit',
			'Type'=>'int(11) unsigned',
			'Default'=>'0',
		),
		'views_count'=>array(
			'Field'=>'views_count',
			'Type'=>'int(11) unsigned',
			'Default'=>'0',
		),
		'seo_title'=>array(
			'Field'=>'seo_title',
		),
		'seo_description'=>array(
			'Field'=>'seo_description',
		),
		'seo_keywords'=>array(
			'Field'=>'seo_keywords',
		),
	),
	'catsubcat_storage'=>array(
		'id' => array(
			'Field'	=>	'id',
			'Type'	=> 	'int(11) unsigned',
			'Key'	=>	'PRI',
			'Default'=>	'0',
			'Extra'	=>	'auto_increment',
		),
		'table' => array(
			'Field'=>'table'
		),
		'element_id'=>array(
			'Field'	=>	'element_id',
			'Type'	=> 	'int(11) unsigned',
			'Default'=>	'0',
		),
		'data'=>array(
			'Field'=>'data',
			'Type'=>'text',
			'Default'=>''
		)
	)
);
