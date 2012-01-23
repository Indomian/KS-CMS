<?php
/**
 * @file install.php
 * Файл выполняющий операции по установке модуля fm
 * Файл проекта kolos-cms.
 *
 * Создан 08.10.10
 *
 * @author blade39 <blade39@kolosstudio.ru>, Dmitry Konev <d.konev@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

global $ks_db,$KS_EVENTS_HANDLER;

$module_name='fm';
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
				$obAccess->Set($value['id'],$module_name,7);
			}
		}
	}

	$arModule=array(
		'name'=>$arDescription['title'],
		'URL_ident'=>$module_name,
		'directory'=>$module_name,
		'include_global_template'=>1,
		'active'=>1,
		'orderation'=>1,
		'hook_up'=>0,
		'allow_url_edit'=>1,
		'id'=>-1,
	);
	$this->AddAutoField('id');
	$this->Save('',$arModule);
	//Устанавливаем файлы административного интерфейса
	if(array_key_exists('installTemplates', $_POST) && ($_POST['installTemplates']==1))
	{
		if($arFiles=$KS_FS->GetDirItems(MODULES_DIR.'/'.$module_name.'/install/templates/.default/'))
		{
			if(!file_exists(TEMPLATES_DIR.'/.default/'.$module_name.'/'))
				$KS_FS->makedir(TEMPLATES_DIR.'/.default/'.$module_name.'/');
			foreach($arFiles as $sFile)
			{
				$KS_FS->CopyFile(MODULES_DIR.'/'.$module_name.'/install/templates/.default/'.$sFile,TEMPLATES_DIR.'/.default/'.$module_name.'/'.$sFile,'');
			}
		}
	}
	if($arFiles=$KS_FS->GetDirItems(MODULES_DIR.'/'.$module_name.'/install/templates/admin/'))
	{
		foreach($arFiles as $sFile)
		{
			$KS_FS->CopyFile(MODULES_DIR.'/'.$module_name.'/install/templates/admin/'.$sFile,SYS_TEMPLATES_DIR.'/admin/'.$sFile,'');
		}
	}
	//Устанавливаем скрипты модуля
	if($arFiles=$KS_FS->GetDirItems(MODULES_DIR.'/'.$module_name.'/install/js/'))
	{
		if(!file_exists(ROOT_DIR.JS_DIR))
			$KS_FS->makedir(ROOT_DIR.JS_DIR);
		if(!file_exists(ROOT_DIR.JS_DIR.'/'.$module_name.'/'))
			$KS_FS->makedir(ROOT_DIR.JS_DIR.'/'.$module_name.'/');
		foreach($arFiles as $sFile)
		{
			$KS_FS->CopyFile(MODULES_DIR.'/'.$module_name.'/install/js/'.$sFile,ROOT_DIR.JS_DIR.'/'.$module_name.'/'.$sFile,'');
		}
	}

	$this->InstallResources($module_name);
	
	$this->AddNotify('SYSTEM_MODULE_INSTALL_OK',$arDescription['title'],NOTIFY_MESSAGE);
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
