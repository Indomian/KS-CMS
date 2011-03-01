<?php
/**
 * @file options.php
 * Файл настроек модуля Гостевая книга 2.0
 * Файл проекта kolos-cms.
 *
 * Создан 08.09.2009
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 1.0
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CModuleAdmin.php';
require_once MODULES_DIR.'/main/libs/class.CUserGroup.php';
require_once MODULES_DIR.'/main/libs/class.CAccess.php';
require_once MODULES_DIR.'/main/libs/class.CConfigParser.php';

class Cguestbook2AIoptions extends CModuleAdmin
{
	function __construct($module='guestbook2',&$smarty,&$parent)
	{
		global $USER;
		parent::__construct($module,$smarty,$parent);
		$this->obUser=$USER;
	}

	function Run()
	{
		global $KS_URL;
		$module_name='guestbook2';
		//Проверка прав доступа
		if($this->obUser->GetLevel($module_name)>0) throw new CAccessError('SYSTEM_NOT_ACCESS_SETTINGS');

		$obConfig=new CConfigParser($module_name);
		$obConfig->LoadConfig();
		$ks_config=$obConfig->GetConfig();

		//Получаем права на доступ к модулю
		$USERGROUP=new CUserGroup;
		$arAccess['groups']=$USERGROUP->GetList(array('title'=>'asc'));
		//Получаем список доступов для модуля
		$arAccess['module']=$this->obModules->GetAccessArray($module_name);
		$obAccess=new CModulesAccess();
		$arAccess['levels']=$obAccess->GetList(array('id'=>'asc'),array('module'=>$module_name));
		unset($arAccess['levels'][$module_name]);
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
					'module'=>$module_name,
					'level'=>10,
				);
		}
		$arAccess['levels']=$arRes;

		if ($_POST['action']=='save')
		{
			try
			{
				$arGuestNames=preg_split("#[\n,]#i",$_POST['sc_restricted_guest_names']);
				if(is_array($arGuestNames))
				{
					foreach($arGuestNames as $key=>$name)
					{
						$arGuestNames[$key]=trim($name,",\n\r ");
					}
					$obConfig->Set('restricted_guest_names',$arGuestNames);
				}
				$obConfig->Set('use_captcha',intval($_POST['sc_use_captcha']));
				$obConfig->Set('use_tags',intval($_POST['sc_use_tags']));
				$obConfig->Set('no_empty_category',intval($_POST['sc_no_empty_category']));
				$obConfig->Set('int_htmleditor',intval($_POST['sc_int_htmleditor']));
				$obConfig->WriteConfig();
				//Выполняем сохранение прав доступа
				if(is_array($_POST['sc_groupLevel']))
				{
					foreach($_POST['sc_groupLevel'] as $key=>$value)
					{
						//echo min($value);
						$obAccess->Set($key,$module_name,min($value));
					}
				}
				$this->obModules->AddNotify('GB2_OPTIONS_SAVED','',NOTIFY_MESSAGE);
				CUrlParser::Redirect("admin.php?module=$module_name&page=options");
			}
			catch (CError $e)
			{
				$this->smarty->assign('last_error',$e);
			}
			catch (EXCEPTION $e)
			{
				$this->smarty->assign('last_error',$e);
			}
		}
		if (is_array($ks_config['restricted_guest_names']))
			$ks_config['restricted_guest_names']=join("\n",$ks_config['restricted_guest_names']);
		$this->smarty->assign('data',$ks_config);
		$this->smarty->assign('access',$arAccess);
		return '_options';
	}
}

