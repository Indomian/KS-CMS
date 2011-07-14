<?php
/**
 * @file redirect/pages/options.php
 * Файл настроек модуля редиректа
 * Файл проекта kolos-cms.
 *
 * Создан 06.05.2011
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CModuleAdmin.php';
require_once MODULES_DIR.'/main/libs/class.CUserGroup.php';
require_once MODULES_DIR.'/main/libs/class.CModulesAccess.php';
require_once MODULES_DIR.'/main/libs/class.CConfigParser.php';

class CredirectAIoptions extends CModuleAdmin
{
	private $obElement;

	function __construct($module='redirect',&$smarty,&$parent)
	{
		parent::__construct($module,$smarty,$parent);
	}

	function Run()
	{
		//Проверка прав доступа
		if($this->obUser->GetLevel($this->module)>0) throw new CAccessError('SYSTEM_NOT_ACCESS_SETTINGS');

		$obConfig=new CConfigParser($this->module);
		$obConfig->LoadConfig();
		$ks_config=$obConfig->GetConfig();
		//Получаем права на доступ к модулю
		$USERGROUP=new CUserGroup;
		$arAccess['groups']=$USERGROUP->GetList(array('title'=>'asc'));
		//Получаем список доступов для модуля
		$arAccess['module']=$this->obModules->GetAccessArray($this->module);
		$obAccess=new CModulesAccess();
		$arAccess['levels']=$obAccess->GetList(array('id'=>'asc'),array('module'=>$this->module));
		unset($arAccess['levels'][$this->module]);
		$arRes=array();
		foreach($arAccess['levels'] as $key=>$item)
		{
			$arRes[$item['group_id']]=$item;
		}
		foreach($arAccess['groups'] as $arGroup)
		{
			if(!array_key_exists($arGroup['id'],$arRes))
				$arRes[$arGroup['id']]=array(
					'id'=>'-1',
					'group_id'=>$arGroup['id'],
					'module'=>$this->module,
					'level'=>10,
				);
		}
		$arAccess['levels']=$arRes;

		if(array_key_exists('action',$_POST) && $_POST['action']=='save')
		{
			try
			{
				if(isset($_POST['OS_content']))
					$obConfig->Set('content',$_POST['OS_content']);
				if(array_key_exists('OS_time',$_POST))
					$obConfig->Set('time',intval($_POST['OS_time']));
				if(isset($_POST['OS_auto']))
					$obConfig->Set('auto',intval($_POST['OS_auto']));
				else
					$obConfig->Set('auto',0);
				$obConfig->WriteConfig();
				//Выполняем сохранение прав доступа
				if(is_array($_POST['sc_groupLevel']))
				{
					foreach($_POST['sc_groupLevel'] as $key=>$value)
					{
						$obAccess->Set($key,$this->module,min($value));
					}
				}
				$this->obModules->AddNotify('REDIRECT_OPTIONS_SAVED','',NOTIFY_MESSAGE);
				$this->obUrl->Redirect("admin.php?module=".$this->module."&page=options");
			}
			catch (CError $e)
			{
				$this->smarty->assign('last_error',$e->__toString());
			}
		}
		$this->smarty->assign('data',$ks_config);
		$this->smarty->assign('access',$arAccess);
		return '_options';
	}
}