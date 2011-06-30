<?php
/**
 * @file modules/hints/install/uninstall.php
 * Файл настройки удаления модуля hints
 * Файл проекта kolos-cms.
 *
 * Создан 25.05.11
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

global $ks_db,$KS_EVENTS_HANDLER;

$module_name='hints';
$sContent='';

include 'description.php';
require_once MODULES_DIR.'/main/libs/class.CUserGroup.php';
require_once MODULES_DIR.'/main/libs/class.CAccess.php';

if(!$this->IsModule('hints')) throw new CError('MAIN_MODULE_NOT_REGISTERED');

//Определяем режим работы
if(array_key_exists('go',$_POST))
{
	$showButtons=0;
	//Удаляем запись о модуле
	$this->DeleteItems(array('directory'=>$module_name));
	//Удаляем файлы
	$this->DeleteAllModuleFiles($module_name);
	//Сообщаем что все ок
	$this->AddNotify(SYSTEM_MODULE_UNINSTALL_OK,$arDescription['title'],NOTIFY_MESSAGE);
}
else
{
	//Если мы не выполняем работу то надо сообщить о настройках модуля перед установкой
	$arFields=array(
	);
	$showButtons=1;
}

