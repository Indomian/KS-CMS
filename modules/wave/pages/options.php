<?php
/**
 * @file options.php
 * Файл обработки настроек модуля wave
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
require_once MODULES_DIR.'/wave/libs/class.CWavePosts.php';

class CwaveAIoptions extends CModuleAdmin
{
	private $obWave;

	function __construct($module='wave',&$smarty,&$parent)
	{
		global $USER;
		parent::__construct($module,$smarty,$parent);
		$this->obUser=$USER;
		$this->obWave=new CWavePosts();
	}

	function Run()
	{
		global $KS_URL;
		//Проверка прав доступа
		if($this->obUser->GetLevel($this->module)>0) throw new CAccessError('WAVE_ACCESS_DENIED');
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
		$arStandartFields=array(
			array(
				'title'=>'content',
				'description'=>$this->obModules->GetText('field_content')
			),
			array(
				'title'=>'user_name',
				'description'=>$this->obModules->GetText('field_user_name')
			),
			array(
				'title'=>'user_email',
				'description'=>$this->obModules->GetText('field_user_email')
			),
			array(
				'title'=>'captcha',
				'description'=>$this->obModules->GetText('field_captcha')
			),
		);
		$arUserFields=$this->obWave->GetUserFields();
		$arWavePosts=array_merge($arStandartFields,$arUserFields);
		if ($_POST['action']=='save')
		{
			try
			{
				$obConfig->Set('use_captcha',intval($_POST['use_captcha']));
				//Настройка режима комментирования
				if(in_array($_POST['mode'],array('tree','list','answer')))
				{
					if($_POST['mode']=='tree')
					{
						$obConfig->Set('max_depth',intval($_POST['max_depth']));
					}
					else
					{
						$obConfig->Remove('max_depth');
					}
					$obConfig->Set('mode',$_POST['mode']);
				}
				else
				{
					$obConfig->Set('mode','list');
					$obConfig->Remove('max_depth');
				}
				//Настройка режима оценок
				if($_POST['use_ratings']=='usefullness')
				{
					$obConfig->Set('use_ratings','usefullness');
					$obConfig->Set('usefullness_useless_min',intval($_POST['usefullness_useless_min']));
					$obConfig->Set('usefullness_dvr',intval($_POST['usefullness_dvr']));
					$obConfig->Set('usefullness_dsv',intval($_POST['usefullness_dsv']));
				}
				else
				{
					$obConfig->Set('use_ratings','no');
					$obConfig->Remove('usefullness_useless_min');
					$obConfig->Remove('usefullness_dvr');
					$obConfig->Remove('usefullness_dsv');
				}
				foreach($arWavePosts as $id=>$arItem)
				{
					$obConfig->Set('field_title_'.$arItem['title'],$_POST['field_title_user'][$arItem['title']]);
					$obConfig->Set('field_show_'.$arItem['title'],intval($_POST['field_show_user'][$arItem['title']]));
					$obConfig->Set('field_necessary_'.$arItem['title'],intval($_POST['field_necessary_user'][$arItem['title']]));
				}
				$obConfig->WriteConfig();
				//Выполняем сохранение прав доступа
				if(is_array($_POST['sc_groupLevel']))
				{
					foreach($_POST['sc_groupLevel'] as $key=>$value)
					{
						$obAccess->Set($key,$this->module,min($value));
					}
				}
				$this->obModules->AddNotify('WAVE_OPTIONS_SAVED','',NOTIFY_MESSAGE);
				CUrlParser::Redirect("admin.php?module=".$this->module."&page=options");
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
		$this->smarty->assign('data',$ks_config);
		$this->smarty->assign('access',$arAccess);
		$this->smarty->assign('fields',$arWavePosts);
		return '_options';
	}
}

