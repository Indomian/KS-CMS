<?php
/**
 * @file subscribe/pages/options.php
 * Файл настроек модуля Рассылки сообщений
 * Файл проекта kolos-cms.
 *
 * Создан 01.02.2010
 *
 * @author fox <fox@kolosstudio.ru>, BlaDe39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CModuleOptions.php';
require_once MODULES_DIR.'/main/libs/class.CUserGroup.php';
require_once MODULES_DIR.'/main/libs/class.CAccess.php';
require_once MODULES_DIR.'/main/libs/class.CConfigParser.php';

class CsubscribeAIoptions extends CModuleOptions
{
	private $access_level;

	function __construct($module='subscribe',&$smarty,&$parent)
	{
		parent::__construct($module,$smarty,$parent);
		$this->access_level=$this->obUser->GetLevel($this->module);
	}

	function Run()
	{
		if($this->access_level>0) throw new CAccessError('SYSTEM_NOT_ACCESS_SETTINGS');
		if(isset($_POST['action']) && $_POST['action']=='save')
			try
			{
				$this->obConfig->Set('format',intval($_POST['SB_format']));
				$this->obConfig->Set('encryption',$_POST['SB_encryption']);
				$this->obConfig->Set('from',$_POST['SB_from']);
				$this->obConfig->WriteConfig();
				
				//Выполняем сохранение прав доступа
				$this->SaveAccessLevels();
				$this->obModules->AddNotify('SUBSCRIBE_OPTIONS_SAVED','',NOTIFY_MESSAGE);
				$this->obUrl->Redirect("admin.php?module=".$this->module."&page=options");
			}
			catch (CError $e)
			{
				$this->smarty->assign('last_error',$e);
			}
		$this->smarty->assign('data',$this->obConfig->GetConfig());
		$this->smarty->assign('access',$this->GetAccessLevels());
		return '_options';
	}
}
