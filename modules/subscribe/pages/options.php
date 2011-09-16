<?php
/**
 * @file modules/subscribe/pages/options.php
 * Файл настроек модуля Рассылки сообщений
 * Файл проекта kolos-cms.
 *
 * Создан 01.02.2010
 *
 * @author fox <fox@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CModuleAdmin.php';
require_once MODULES_DIR.'/main/libs/class.CUserGroup.php';
require_once MODULES_DIR.'/main/libs/class.CAccess.php';
require_once MODULES_DIR.'/main/libs/class.CConfigParser.php';

class CsubscribeAIoptions extends CModuleAdmin
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
			$arRes[$item['group_id']]=$item;
		foreach($arAccess['groups'] as $arGroup)
			if(!array_key_exists($arGroup['id'],$arRes))
				$arRes[$arGroup['id']]=array(
					'id'=>'-1',
					'group_id'=>$arGroup['id'],
					'module'=>$this->module,
					'level'=>10,
				);
		$arAccess['levels']=$arRes;
		if(isset($_POST['action']) && $_POST['action']=='save')
		{
			try
			{
				$obConfig->Set('format',intval($_POST['SB_format']));
				$obConfig->Set('encryption',$_POST['SB_encryption']);
				$obConfig->Set('from',$_POST['SB_from']);
				$obConfig->WriteConfig();
				//Выполняем сохранение прав доступа
				if(is_array($_POST['sc_groupLevel']))
					foreach($_POST['sc_groupLevel'] as $key=>$value)
						$obAccess->Set($key,$this->module,min($value));
				$this->obModules->AddNotify('SUBSCRIBE_OPTIONS_SAVED','',NOTIFY_MESSAGE);
				CUrlParser::get_instance()->Redirect("admin.php?module=".$this->module."&page=options");
			}
			catch (CError $e)
			{
				$this->smarty->assign('last_error',$e);
			}
		}
		$this->smarty->assign('data',$ks_config);
		$this->smarty->assign('access',$arAccess);
		return '_options';
	}
}
