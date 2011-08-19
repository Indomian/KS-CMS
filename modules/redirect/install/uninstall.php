<?php
/**
 * @file redirect/install/uninstall.php
 * Файл настройки удаления модуля "Перенаправление"
 * Файл проекта kolos-cms.
 *
 * Создан 06.05.2011
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 1.0
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

global $ks_db;
$module_name='redirect';
$sContent='';
include 'description.php';
require_once MODULES_DIR.'/main/libs/class.CUserGroup.php';
require_once MODULES_DIR.'/main/libs/class.CModulesAccess.php';

if(!$this->IsModule($module_name)) throw new CError('MAIN_MODULE_NOT_REGISTERED');

//Определяем режим работы
if(array_key_exists('go',$_POST))
{
	$showButtons=0;
	//Удаляем запись о модуле
	$this->DeleteItems(array('directory'=>$module_name));
	//Удаляем файлы административных шаблонов
	if($arFiles=$KS_FS->GetDirItems(MODULES_DIR.'/redirect/install/templates/admin/'))
	{
		foreach($arFiles as $sFile)
		{
			$KS_FS->Remove(SYS_TEMPLATES_DIR.'/admin/'.$sFile);
		}
	}
	//Удаляем файлы яваскрипта
	if($arFiles=$KS_FS->GetDirItems(MODULES_DIR.'/redirect/install/js/'))
	{
		if(file_exists(ROOT_DIR.JS_DIR.'/redirect/'))
			$KS_FS->Remove(ROOT_DIR.JS_DIR.'/redirect/');
	}
	if(intval($_POST['deleteTemplates'])>0)
	{
		if(file_exists(TEMPLATES_DIR.'/.default/redirect/'))
		{
			$KS_FS->Remove(TEMPLATES_DIR.'/.default/redirect/');
		}
	}
	//Сообщаем что все ок
	$this->AddNotify(SYSTEM_MODULE_UNINSTALL_OK,$arDescription['title'],NOTIFY_MESSAGE);
}
else
{
	//Если мы не выполняем работу то надо сообщить о настройках модуля перед установкой
	$arFields=array(
		'deleteTemplates'=>array(
			'type'=>'checkbox',
			'title'=>'Удалить шаблоны виджетов в шаблоне по умолчанию',
			'value'=>'1',
		),
	);
	$showButtons=1;
}