<?php
/**
 * @file uninstall.php
 * Файл настройки удаления модуля fm
 * Файл проекта kolos-cms.
 *
 * Создан 11.01.12
 *
 * @author blade39 <blade39@kolosstudio.ru>, Dmitry Konev <d.konev@kolosstudio.ru>
 * @version 2.6
 * @todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

global $ks_db,$KS_EVENTS_HANDLER;

$module_name='fm';
$sContent='';

include 'description.php';
require_once MODULES_DIR.'/main/libs/class.CUserGroup.php';
require_once MODULES_DIR.'/main/libs/class.CAccess.php';

if(!$this->IsModule(''.$module_name.'')) throw new CError('MAIN_MODULE_NOT_REGISTERED');

//Определяем режим работы
if(array_key_exists('go',$_POST))
{
	$showButtons=0;
	$arTables=$ks_db->ListTables();
	//Удаляем запись о модуле
	$this->DeleteItems(array('directory'=>$module_name));
	//Удаляем файлы административных шаблонов
	if($arFiles=$KS_FS->GetDirItems(MODULES_DIR.'/'.$module_name.'/install/templates/admin/'))
	{
		foreach($arFiles as $sFile)
		{
			$KS_FS->Remove(SYS_TEMPLATES_DIR.'/admin/'.$sFile);
		}
	}
	//Удаляем файлы яваскрипта
	if($arFiles=$KS_FS->GetDirItems(MODULES_DIR.'/'.$module_name.'/install/js/'))
	{
		if(file_exists(ROOT_DIR.JS_DIR.'/'.$module_name.'/'))
			$KS_FS->Remove(ROOT_DIR.JS_DIR.'/'.$module_name.'/');
	}
	if(intval($_POST['deleteTemplates'])>0)
	{
		if(file_exists(TEMPLATES_DIR.'/.default/'.$module_name.'/'))
		{
			$KS_FS->Remove(TEMPLATES_DIR.'/.default/'.$module_name.'/');
		}
	}
	//Сообщаем что все ок
	$this->AddNotify('SYSTEM_MODULE_UNINSTALL_OK',$arDescription['title'],NOTIFY_MESSAGE);
}
else
{
	//Если мы не выполняем работу то надо сообщить о настройках модуля перед установкой
	$arFields=array();
	$showButtons=1;
}
?>
