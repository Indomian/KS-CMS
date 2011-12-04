<?php
/**
 * @file install.php
 * Файл выполняющий операции по установке модуля navigation
 * Файл проекта kolos-cms.
 *
 * Создан 21.02.2011
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

$module_name='navigation';

require_once MODULES_DIR.'/main/libs/class.CUserGroup.php';
require_once MODULES_DIR.'/main/libs/class.CAccess.php';
include MODULES_DIR.'/'.$module_name.'/install/db_structure.php';

if($this->IsModule($module_name)) throw new CError('MAIN_MODULE_ALREADY_INSTALLED');

//Список таблиц модуля
$arDBList=array(
	'navigation_menu_types',
	'navigation_menu_elements',
);
//Получаем список таблиц системы
$arTables=$this->obDB->ListTables();
//Чистим базу (если были таблицы - потрем)
$showButtons=0;
$arFields=array();
foreach($arDBList as $sTable)
	if(in_array($sTable,$arTables))
		$this->obDB->CheckTable($sTable,$arStructure[$sTable]);
	else
		$this->obDB->AddTable($sTable,$arStructure[$sTable]);
//Прописываем уровни доступа для всех групп
$USERGROUP=new CUserGroup;
$arAccess['groups']=$USERGROUP->GetList(array('title'=>'asc'));
$obAccess=new CModulesAccess();
//Выполняем сохранение прав доступа
if(is_array($arAccess['groups']))
	foreach($arAccess['groups'] as $key=>$value)
		if($value['id']=='1')
			$obAccess->Set($value['id'],$module_name,0);
		else
			$obAccess->Set($value['id'],$module_name,10);

$arModule=array(
	'name'=>$module_name,
	'URL_ident'=>'',
	'directory'=>$module_name,
	'include_global_template'=>0,
	'active'=>1,
	'orderation'=>3,
	'hook_up'=>1,
	'allow_url_edit'=>0,
	'id'=>-1,
);
$this->Save('',$arModule);
//Устанавливаем файлы административного интерфейса
if($arFiles=$KS_FS->GetDirItems(MODULES_DIR.'/navigation/install/templates/.default/'))
{
	if(!file_exists(TEMPLATES_DIR.'/.default/navigation/'))
		$KS_FS->makedir(TEMPLATES_DIR.'/.default/navigation/');
	foreach($arFiles as $sFile)
		$KS_FS->CopyFile(MODULES_DIR.'/navigation/install/templates/.default/'.$sFile,TEMPLATES_DIR.'/.default/navigation/'.$sFile,'');
}
if($arFiles=$KS_FS->GetDirItems(MODULES_DIR.'/navigation/install/templates/admin/'))
	foreach($arFiles as $sFile)
		$KS_FS->CopyFile(MODULES_DIR.'/navigation/install/templates/admin/'.$sFile,SYS_TEMPLATES_DIR.'/admin/'.$sFile,'');

//Устанавливаем скрипты модуля
if($arFiles=$KS_FS->GetDirItems(MODULES_DIR.'/navigation/install/js/'))
{
	if(!file_exists(ROOT_DIR.JS_DIR))
		$KS_FS->makedir(ROOT_DIR.JS_DIR);
	if(!file_exists(ROOT_DIR.JS_DIR.'/navigation/'))
		$KS_FS->makedir(ROOT_DIR.JS_DIR.'/navigation/');
	foreach($arFiles as $sFile)
		$KS_FS->CopyFile(MODULES_DIR.'/navigation/install/js/'.$sFile,ROOT_DIR.JS_DIR.'/navigation/'.$sFile,'');
}
$this->AddNotify('SYSTEM_MODULE_INSTALL_OK','navigation',NOTIFY_MESSAGE);