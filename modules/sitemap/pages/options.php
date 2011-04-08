<?php
/**
 * @file sitemap/pages/options.php
 * Файл обработки настроек модуля sitemap
 * Файл проекта kolos-cms.
 *
 * Создан 08.12.2010
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.5.4-16
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CModuleAdmin.php';
require_once MODULES_DIR.'/main/libs/class.CUserGroup.php';
require_once MODULES_DIR.'/main/libs/class.CAccess.php';
require_once MODULES_DIR.'/main/libs/class.CConfigParser.php';

class CsitemapAIoptions extends CModuleAdmin
{
	function __construct($module='sitemap',&$smarty,&$parent)
	{
		global $USER;
		parent::__construct($module,$smarty,$parent);
		$this->obUser=$USER;
	}

	function Run()
	{
		global $KS_URL;
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

		//Получаем список модулей по которым будем генерировать дерево сайта
		$arModules = $this->obModules->GetList(array("URL_ident"=>'asc'), array("active" => 1,'!URL_ident'=>''));
		if (is_array($arModules)&&count($arModules)>0)
			foreach($arModules as $key=>$arRow)
			{
				if(!file_exists(MODULES_DIR.'/'.$arRow['directory'].'/.tree.php'))
				{
					$arModules[$key]['maxLevel']=0;
				}
				else
				{
					$arModules[$key]['maxLevel']=5;
				}
			}

		if ($_POST['action']=='save')
		{
			try
			{
				$arRes=array();
				foreach($arModules as $key=>$value)
				{
					if(array_key_exists($value['directory'],$_POST['show']) && $_POST['show'][$value['directory']]==1)
					{
						//Показывать модуль
						$arRes[$value['directory']]=intval($_POST['level'][$value['directory']]);
					}
				}
				$obConfig->Set('cacheTime',intval($_POST['sc_cacheTime']));
				$obConfig->Set('modules',$arRes);
				$obConfig->WriteConfig();
				//Выполняем сохранение прав доступа
				if(is_array($_POST['sc_groupLevel']))
				{
					foreach($_POST['sc_groupLevel'] as $key=>$value)
					{
						//echo min($value);
						$obAccess->Set($key,$this->module,min($value));
					}
				}
				$this->obModules->AddNotify('SITEMAP_OPTIONS_SAVED','',NOTIFY_MESSAGE);
				CUrlParser::get_instance()->Redirect("admin.php?module=".$this->module."&page=options");
			}
			catch (CError $e)
			{
				$smarty->assign('last_error',$e);
			}
			catch (EXCEPTION $e)
			{
				$smarty->assign('last_error',$e);
			}
		}
		$this->smarty->assign('modules',$arModules);
		$this->smarty->assign('data',$ks_config);
		$this->smarty->assign('access',$arAccess);
		return '_options';
	}
}
