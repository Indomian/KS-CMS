<?php
/**
 * @file db_structure.php
 * Файл со структурой базы данных главного модуля
 * Файл проекта KS-CMS
 *
 * Создан 22.10.2010
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.5.4-14
 * @todo Добавить все таблицы
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

$arStructure=array(
	'users'=>array(
		'id' => array(
			'Field'	=>	'id',
			'Type'	=> 	'int(11) unsigned',
			'Null'	=>	'NO',
			'Key'	=>	'PRI',
			'Default'=>	'0',
			'Extra'	=>	'auto_increment',
		),
		'img'=>array('Field'=>'img'),
		'password'=>array('Field'=>'password'),
		'active'=>array(
			'Field'=>'active',
			'Type'=>'tinyint(1) unsigned',
			'Default'=>'0'
		),
		'date_register'=>array(
			'Field'=>'date_register',
			'Type'=>'int(11)',
			'Default'=>'1'
		),
		'title'=>array('Field'=>'title'),
		'email'=>array('Field'=>'email'),
		'last_visit'=>array(
			'Field'=>'last_visit',
			'Type'=>'int(11) unsigned',
			'Default'=>'0'
		),
		'number_of_log_tries'=>array(
			'Field'=>'number_of_log_tries',
			'Type'=>'int(11) unsigned',
			'Default'=>'0'
		),
		'pwd_updated'=>array(
			'Field'=>'pwd_updated',
			'Type'=>'tinyint(1) unsigned',
			'Default'=>'0'
		),
		'blocked_from'=>array(
			'Field'=>'blocked_from',
			'Type'=>'int(11) unsigned',
			'Default'=>'0'
		),
		'blocked_till'=>array(
			'Field'=>'blocked_till',
			'Type'=>'int(11) unsigned',
			'Default'=>'0'
		),
		'code'=>array('Field'=>'code'),
		'new_email'=>array('Field'=>'new_email'),
		'user_vars'=>array(
			'Field'=>'user_vars',
			'Type'=>'text'
		),
		'token'=>array(
			'Field'=>'token',
			'Type'=>'char(32)'
		),
		'last_ip'=>array(
			'Field'=>'last_ip',
			'Type'=>'char(15)'
		)
	),
	'users_log'=>array(
		'id'=>array(
			'Field'	=>	'id',
			'Type'	=> 	'int(11) unsigned',
			'Null'	=>	'NO',
			'Key'	=>	'PRI',
			'Default'=>	'0',
			'Extra'	=>	'auto_increment',
		),
		'user_id'=>array(
			'Field'	=>	'user_id',
			'Type'	=> 	'int(11) unsigned',
			'Default'=>	'0',
		),
		'content'=>array(
			'Field'=>'content',
			'Type'=>'text'
		),
		'ip'=>array('Field'=>'ip'),
		'date'=>array(
			'Field'	=>	'date',
			'Type'	=> 	'int(11) unsigned',
			'Default'=>	'0'
		),
		'request'=>array(
			'Field'=>'request',
			'Type'=>'text',
		)
	),
	'main_modules'=>array(
		'id' => array(
			'Field'	=>	'id',
			'Type'	=> 	'int(11) unsigned',
			'Null'	=>	'NO',
			'Key'	=>	'PRI',
			'Default'=>	'0',
			'Extra'	=>	'auto_increment',
		),
		'name'=>array('Field'=>'name'),
		'URL_ident'=>array('Field'=>'URL_ident'),
		'directory'=>array('Field'=>'directory'),
		'include_global_template'=>array(
			'Field'=>'include_global_template',
			'Type'=>'tinyint(1) unsigned',
			'Default'=>'0'
		),
		'active'=>array(
			'Field'=>'active',
			'Type'=>'tinyint(1) unsigned',
			'Default'=>'0'
		),
		'orderation'=>array(
			'Field'=>'orderation',
			'Type'=>'int(11) unsigned',
			'Default'=>'0'
		),
		'hook_up'=>array(
			'Field'=>'hook_up',
			'Type'=>'tinyint(1) unsigned',
			'Default'=>'0'
		),
		'allow_url_edit'=>array(
			'Field'=>'allow_url_edit',
			'Type'=>'tinyint(1) unsigned',
			'Default'=>'0',
		)
	),
	'main_path_to_template'=>array(
		'id' => array(
			'Field'	=>	'id',
			'Type'	=> 	'int(11) unsigned',
			'Null'	=>	'NO',
			'Key'	=>	'PRI',
			'Default'=>	'0',
			'Extra'	=>	'auto_increment',
		),
		'url_path'=>array('Field'=>'url_path'),
		'template_path'=>array('Field'=>'template_path'),
		'orderation'=>array(
			'Field'=>'orderation',
			'Type'=>'int(11) unsigned',
			'Default'=>'0'
		),
		'type'=>array(
			'Field'=>'type',
			'Type'=>'char(10)'
		),
		'function1'=>array('Field'=>'function1'),
		'function2'=>array('Field'=>'function2')
	),
	'main_fields'=>array(
		'id' => array(
			'Field'	=>	'id',
			'Type'	=> 	'int(11) unsigned',
			'Null'	=>	'NO',
			'Key'	=>	'PRI',
			'Default'=>	'0',
			'Extra'	=>	'auto_increment',
		),
		'title'=>array('Field'=>'title'),
		'description'=>array('Field'=>'description'),
		'script'=>array('Field'=>'script'),
		'module'=>array('Field'=>'module'),
		'type'=>array('Field'=>'type'),
		'default'=>array('Field'=>'default'),
		'option_1'=>array('Field'=>'option_1'),
		'option_2'=>array(
			'Field'=>'option_2',
			'Type'=>'text'
		)
	),
	'main_events'=>array(
		'id' => array(
			'Field'	=>	'id',
			'Type'	=> 	'int(11) unsigned',
			'Null'	=>	'NO',
			'Key'	=>	'PRI',
			'Default'=>	'0',
			'Extra'	=>	'auto_increment',
		),
		'title'=>array('Field'=>'title'),
		'content'=>array(
			'Field'=>'content',
			'Type'=>'text'
		),
		'date_add'=>array(
			'Field'=>'date_add',
			'Type'=>'int(11) unsigned',
			'Default'=>'0'
		),
		'date_end'=>array(
			'Field'=>'date_end',
			'Type'=>'int(11) unsigned',
			'Default'=>'0'
		),
		'author'=>array('Field'=>'author'),
		'address'=>array('Field'=>'address'),
		'status'=>array(
			'Field'=>'status',
			'Type'=>"enum('new', 'inwork', 'done', 'error')",
			'Default'=>'new'
		),
		'type'=>array('Field'=>'type'),
		'format'=>array(
			'Field'=>'format',
			'Type'=>'char(30)'
		),
		'encoding'=>array(
			'Field'=>'encoding',
			'Type'=>'char(30)',
		),
		'name_to'=>array('Field'=>'name_to'),
		'email_from'=>array('Field'=>'email_from'),
	),
	'main_eventtemplates'=>array(
		'id' => array(
			'Field'	=>	'id',
			'Type'	=> 	'int(11) unsigned',
			'Null'	=>	'NO',
			'Key'	=>	'PRI',
			'Default'=>	'0',
			'Extra'	=>	'auto_increment',
		),
		'file_id'=>array('Field'=>'file_id'),
		'title'=>array('Field'=>'title'),
		'address'=>array('Field'=>'address'),
		'copy'=>array('Field'=>'copy'),
	),
	'geography_countries'=>array(
		'id' => array(
			'Field'	=>	'id',
			'Type'	=> 	'int(11) unsigned',
			'Null'	=>	'NO',
			'Key'	=>	'PRI',
			'Default'=>	'0',
			'Extra'	=>	'auto_increment',
		),
		'title'=>array('Field'=>'title'),
		'title_en'=>array('Field'=>'title_en'),
	),
	'geography_cities'=>array(
		'id' => array(
			'Field'	=>	'id',
			'Type'	=> 	'int(11) unsigned',
			'Null'	=>	'NO',
			'Key'	=>	'PRI',
			'Default'=>	'0',
			'Extra'	=>	'auto_increment',
		),
		'country_id'=>array(
			'Field'	=>	'country_id',
			'Type'	=> 	'int(11) unsigned',
			'Default'=>	'0',
		),
		'title'=>array('Field'=>'title'),
		'title_en'=>array('Field'=>'title_en'),
		'text_ident'=>array('Field'=>'text_ident')
	),
	'usergroups_levels'=>array(
		'id' => array(
			'Field'	=>	'id',
			'Type'	=> 	'int(11) unsigned',
			'Null'	=>	'NO',
			'Key'	=>	'PRI',
			'Default'=>	'0',
			'Extra'	=>	'auto_increment'
		),
		'group_id'=>array(
			'Field'=>'group_id',
			'Type'=>'int(11) unsigned',
			'Default'=>'0'
		),
		'module'=>array(
			'Field'=>'module',
			'Type'=>'char(64)'
		),
		'level'=>array(
			'Field'=>'level',
			'Type'=>'tinyint(2) unsigned',
			'Default'=>'0'
		)
	),
	'usergroups'=>array(
		'id' => array(
			'Field'	=>	'id',
			'Type'	=> 	'int(11) unsigned',
			'Null'	=>	'NO',
			'Key'	=>	'PRI',
			'Default'=>	'0',
			'Extra'	=>	'auto_increment'
		),
		'level'=>array(
			'Field'=>'level',
			'Type'=>'smallint(3) unsigned',
			'Default'=>'0'
		),
		'title'=>array('Field'=>'title'),
		'description'=>array('Field'=>'description'),
		'number_of_log_tries'=>array(
			'Field'=>'number_of_log_tries',
			'Type'=>'int(11) unsigned',
			'Default'=>'0'
		),
		'undeletable'=>array(
			'Field'=>'undeletable',
			'Type'=>'tinyint(1) unsigned',
			'Default'=>'0'
		)
	),
	'users_grouplinks'=>array(
		'id' => array(
			'Field'	=>	'id',
			'Type'	=> 	'int(11) unsigned',
			'Null'	=>	'NO',
			'Key'	=>	'PRI',
			'Default'=>	'0',
			'Extra'	=>	'auto_increment',
		),
		'group_id'=>array(
			'Field'=>'group_id',
			'Type'=>'int(11) unsigned',
			'Default'=>'0'
		),
		'user_id'=>array(
			'Field'=>'user_id',
			'Type'=>'int(11) unsigned',
			'Default'=>'0'
		),
		'date_start'=>array(
			'Field'=>'date_start',
			'Type'=>'int(11) unsigned',
			'Default'=>'0'
		),
		'date_end'=>array(
			'Field'=>'date_end',
			'Type'=>'int(11) unsigned',
			'Default'=>'0'
		)
	)
);
