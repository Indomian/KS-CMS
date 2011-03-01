<?php
/**
 * \file install.php
 * Файл выполняющий операции по установке модуля Карта сайта
 * Файл проекта kolos-cms.
 *
 * Создан 12.12.2009
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 2.5.3
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

global $ks_db,$KS_EVENTS_HANDLER;

$module_name='sitemap';
$sContent='';

include 'description.php';
require_once MODULES_DIR.'/main/libs/class.CUserGroup.php';
require_once MODULES_DIR.'/main/libs/class.CAccess.php';

//Определяем режим работы
if(array_key_exists('go',$_POST))
{
	$showButtons=0;
	$arFields=array();
	//Прописываем уровни доступа для всех групп
	$USERGROUP=new CUserGroup;
	$arAccess['groups']=$USERGROUP->GetList(array('title'=>'asc'));
	$obAccess=new CModulesAccess();
	//Выполняем сохранение прав доступа
   	if(is_array($arAccess['groups']))
   	{
	   	foreach($arAccess['groups'] as $key=>$value)
		{
			if($value['id']=='1')
			{
				$obAccess->Set($value['id'],$module_name,0);
			}
			else
			{
				$obAccess->Set($value['id'],$module_name,9);
			}
		}
   	}

	$arModule=array(
		'name'=>$arDescription['title'],
		'URL_ident'=>$module_name,
		'directory'=>$module_name,
		'include_global_template'=>1,
		'active'=>1,
		'orderation'=>5,
		'hook_up'=>0,
		'allow_url_edit'=>1,
		'id'=>-1,
	);
	$this->AddAutoField('id');
	$this->Save('',$arModule);
	//Устанавливаем файлы административного интерфейса
	if($_POST['installTemplates']==1)
	{
		if($arFiles=$KS_FS->GetDirItems(MODULES_DIR.'/sitemap/install/templates/.default/'))
		{
			if(!file_exists(TEMPLATES_DIR.'/.default/sitemap/'))
				$KS_FS->makedir(TEMPLATES_DIR.'/.default/sitemap/');
			foreach($arFiles as $sFile)
			{
				$KS_FS->CopyFile(MODULES_DIR.'/sitemap/install/templates/.default/'.$sFile,TEMPLATES_DIR.'/.default/sitemap/'.$sFile,'');
			}
		}
	}
	if($arFiles=$KS_FS->GetDirItems(MODULES_DIR.'/sitemap/install/templates/admin/'))
	{
		foreach($arFiles as $sFile)
		{
			$KS_FS->CopyFile(MODULES_DIR.'/sitemap/install/templates/admin/'.$sFile,SYS_TEMPLATES_DIR.'/admin/'.$sFile,'');
		}
	}
	//Устанавливаем скрипты модуля
	if($arFiles=$KS_FS->GetDirItems(MODULES_DIR.'/sitemap/install/js/'))
	{
		if(!file_exists(ROOT_DIR.JS_DIR))
			$KS_FS->makedir(ROOT_DIR.JS_DIR);
		if(!file_exists(ROOT_DIR.JS_DIR.'/sitemap/'))
			$KS_FS->makedir(ROOT_DIR.JS_DIR.'/sitemap/');
		foreach($arFiles as $sFile)
		{
			$KS_FS->CopyFile(MODULES_DIR.'/sitemap/install/js/'.$sFile,ROOT_DIR.JS_DIR.'/catsubcat/'.$sFile,'');
		}
	}
	$this->AddNotify(SYSTEM_MODULE_INSTALL_OK,$arDescription['title'],NOTIFY_MESSAGE);
}
else
{
	//Если мы не выполняем работу то надо сообщить о настройках модуля перед установкой
	$arFields=array();
	$arFields['text']=array(
		'type'=>'label',
		'title'=>$arDescription['description']
		);
}
?>
