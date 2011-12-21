<?php
/**
 * @file install.php
 * Файл выполняющий операции по установке модуля main (фактически по установке системы)
 * Вызывается только из класса CModuleManagment
 * Файл проекта kolos-cms.
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 * @since 17.02.2011
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

global $ks_db;

$module_name='main';

include MODULES_DIR.'/main/install/db_structure.php';
include_once MODULES_DIR.'/main/libs/class.CUserGroup.php';
include_once MODULES_DIR.'/main/libs/class.CUser.php';
include_once MODULES_DIR.'/main/libs/class.CModulesAccess.php';

//Список таблиц модуля
$arDBList=array(
	'main_modules',
	'main_path_to_template',
	'main_fields',
	'main_events',
	'main_eventtemplates',
	'geography_countries',
	'geography_cities',
	'usergroups',
	'usergroups_levels',
	'users_grouplinks',
	'users'
);
//Получаем список таблиц системы
$arTables=$ks_db->ListTables();


$arFields=array();
foreach($arDBList as $sTable)
	//Чистим базу (если были таблицы - потрем)
	if(in_array($sTable,$arTables))
		$ks_db->CheckTable($sTable,$arStructure[$sTable]);
	else
		$ks_db->AddTable($sTable,$arStructure[$sTable]);

//Прописываем уровни доступа для всех групп
$USERGROUP=new CUserGroup;
if(!$USERGROUP->GetRecord(array('id'=>1)))
{
	$arFields=array(
		'id'=>1,
		'title'=>'Administration',
		'level'=>0,
		'description'=>'',
		'undeletable'=>1,
		'number_of_log_tries'=>3
	);
	$USERGROUP->Save('',$arFields);
	$ks_db->query('SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO"');
	$arFields=array(
		'id'=>0,
		'title'=>'Guests',
		'level'=>0,
		'description'=>'',
		'undeletable'=>1,
		'number_of_log_tries'=>3
	);
	$USERGROUP->Save('',$arFields);
}
$arAccess['groups']=$USERGROUP->GetList(array('title'=>'asc'));
$obAccess=new CModulesAccess();
//Выполняем сохранение прав доступа
if(is_array($arAccess['groups']))
	foreach($arAccess['groups'] as $key=>$value)
		if($value['id']=='1')
			$obAccess->Set($value['id'],'main',0);
		else
			$obAccess->Set($value['id'],'main',10);

$obUser=new CObject('users');
if(!$obUser->GetRecord(array('id'=>1)))
{
	//Добавляем администратора
	$arFields=array(
		'id'=>1,
		'title'=>'admin',
		'password'=>md5('123456'),
		'active'=>1,
		'date_register'=>time(),
	);
	$obUser->Save('',$arFields);
}
//Устанавливаем права доступа админа
$obAccess=new CObject('users_grouplinks');
$arFields=array('user_id'=>1,'group_id'=>1,'date_start'=>0,'date_end'=>0);
$obAccess->Save('',$arFields);

//Создаем системные директории
if(!file_exists(SYS_TEMPLATES_DIR))
{
	$KS_FS->makedir(SYS_TEMPLATES_DIR);
	$KS_FS->makedir(SYS_TEMPLATES_DIR.'/admin/');
	$KS_FS->makedir(SYS_TEMPLATES_DIR.'/configs/');
	$KS_FS->makedir(SYS_TEMPLATES_DIR.'/cache/');
	$KS_FS->makedir(SYS_TEMPLATES_DIR.'/templates_c/');
}
if(!file_exists(UPLOADS_DIR))
{
	$KS_FS->makedir(UPLOADS_DIR);
	$KS_FS->makedir(UPLOADS_DIR.'/templates/');
}
//Устанавливаем файлы административного интерфейса
if($arFiles=$KS_FS->GetDirItems(MODULES_DIR.'/main/install/templates/.default/'))
	foreach($arFiles as $sFile)
		$KS_FS->CopyFile(MODULES_DIR.'/main/install/templates/.default/'.$sFile,TEMPLATES_DIR.'/.default/main/'.$sFile,'');
if($arFiles=$KS_FS->GetDirItems(MODULES_DIR.'/main/install/templates/admin/'))
	foreach($arFiles as $sFile)
		$KS_FS->CopyFile(MODULES_DIR.'/main/install/templates/admin/'.$sFile,SYS_TEMPLATES_DIR.'/admin/'.$sFile,'');
//Устанавливаем скрипты модуля
if($arFiles=$KS_FS->GetDirItems(MODULES_DIR.'/main/install/js/'))
{
	if(!file_exists(ROOT_DIR.JS_DIR))
		$KS_FS->makedir(ROOT_DIR.JS_DIR);
	foreach($arFiles as $sFile)
		$KS_FS->CopyFile(MODULES_DIR.'/main/install/js/'.$sFile,ROOT_DIR.JS_DIR.'/'.$sFile,'');
}
$this->AddNotify('SYSTEM_MODULE_INSTALL_OK','main',NOTIFY_MESSAGE);