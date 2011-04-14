<?php
/**
 * @file main/pages/options.php
 * Файл обработки настроек модуля main
 * Файл проекта kolos-cms.
 *
 * Изменен 13.01.2011
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.5.5
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CModuleAdmin.php';
require_once MODULES_DIR.'/main/libs/class.CUserGroup.php';
require_once MODULES_DIR.'/main/libs/class.CModulesAccess.php';
require_once MODULES_DIR.'/main/libs/class.CConfigParser.php';

class CmainAIoptions extends CModuleAdmin
{
	function __construct($module='main',&$smarty,&$parent)
	{
		parent::__construct($module,$smarty,$parent);
	}

	function UpdateLanguages()
	{
		$this->obModules->RecountTextStructure();
	}

	function DropCache()
	{
		$this->smarty->clear_all_cache();
		$this->smarty->clear_compiled_tpl();
	}

	/**
	 * Метод производит полную очистку кэша виджетов
	 */
	function DropSystemCache()
	{
		global $KS_FS;
		if(defined('KS_CACHE_HTML_DIR'))
		{
			$sCachePath=KS_CACHE_DIR;
		}
		else
		{
			$sCacheDir=MODULES_DIR.'/main/cache/';
		}
		return $KS_FS->cleardir($sCacheDir);
	}

	function DropImagesCache()
	{
		global $KS_FS;
		if(!$KS_FS->cleardir(UPLOADS_DIR.'/PicCache'))
		{
			$this->obModules->AddNotify('MAIN_PICTURE_CACHE_CLEAN_FAIL');
		}
	}

	function CopyTemplates()
	{
		$this->obModules->CopyModuleTemplates('main');
		if($arModules=$this->obModules->GetList(false,array('active'=>1)))
		{
			foreach($arModules as $arModule)
			{
				$this->obModules->CopyModuleTemplates($arModule['directory']);
			}
		}
	}

	function CheckTables()
	{
		$this->obModules->RecountDBStructure();
		$obConfig=new CConfigParser('main');
		$obConfig->LoadConfig();
		$obConfig->Set('update_db',1);
		$obConfig->WriteConfig();
		$this->obModules->AddNotify('MAIN_OPTIONS_TABLES_UPDATED','',NOTIFY_MESSAGE);
		CUrlParser::get_instance()->Redirect('/admin.php?module=main&modpage=options');
	}

	function Run()
	{
		if($this->obUser->GetLevel($this->module)>0) throw new CAccessError('MAIN_ACCESS_SITE_PREFERENCES_CLOSED');
		$obConfig=new CConfigParser('main');
		$ks_config=$obConfig->LoadConfig();

		//Получаем права на доступ к модулю
		$USERGROUP=new CUserGroup;
		$arAccess['groups']=$USERGROUP->GetList(array('title'=>'asc'));
		//Получаем список доступов для модуля
		$arAccess['module']=$this->obModules->GetAccessArray('main');
		$obAccess=new CModulesAccess();
		$arAccess['levels']=$obAccess->GetList(array('id'=>'asc'),array('module'=>'main'));
		unset($arAccess['levels']['main']);
		$arRes=array();
		foreach($arAccess['levels'] as $key=>$item)
		{
			$arRes[$item['group_id']]=$item;
		}
		$arAccess['levels']=$arRes;

		if(array_key_exists('act_update_lng',$_POST))
		{
			$this->UpdateLanguages();
			$this->obModules->AddNotify('MAIN_OPTIONS_LANGUAGE_UPDATED','',NOTIFY_MESSAGE);
		}
		elseif(array_key_exists('act_drop_cache',$_POST))
		{
			$this->DropCache();
			$this->obModules->AddNotify('MAIN_OPTIONS_CACHE_CLEARED','',NOTIFY_MESSAGE);
		}
		elseif(array_key_exists('act_check_tables',$_POST))
		{
			$this->CheckTables();
		}
		elseif(array_key_exists('act_drop_images_cache',$_POST))
		{
			$this->DropImagesCache();
			$this->obModules->AddNotify('MAIN_OPTIONS_IMAGES_CACHE_CLEARED','',NOTIFY_MESSAGE);
		}
		elseif(array_key_exists('act_update_templates',$_POST))
		{
			$this->CopyTemplates();
			$this->obModules->AddNotify('MAIN_OPTIONS_TEMPLATES_COPIED','',NOTIFY_MESSAGE);
		}
		elseif(array_key_exists('act_drop_system_cache',$_POST))
		{
			if($this->DropSystemCache())
			{
				$this->obModules->AddNotify('MAIN_OPTIONS_SYSTEM_CACHE_CLEARED','',NOTIFY_MESSAGE);
			}
			else
			{
				$this->obModules->AddNotify('MAIN_OPTIONS_SYSTEM_CACHE_CLEAR_ERROR');
			}
		}
		elseif($_SERVER['REQUEST_METHOD']=='POST' && $_POST['action']=='save')
		{
			try
			{
				$error=0;
				//Проверка правильности введенного урл
				if(preg_match('#^http://([\w\d\.\-_]+)$#',$_POST['sc_home_url']))
					$obConfig->Set('home_url',$_POST['sc_home_url']);
				else
					$error+=$this->obModules->AddNotify('MAIN_OPTIONS_WRONG_SITE_URL');
				$obConfig->Set('home_title',htmlentities($_POST['sc_home_title'],ENT_QUOTES,'UTF-8'));
				$obConfig->Set('home_descr',htmlentities($_POST['sc_home_descr'],ENT_QUOTES,'UTF-8'));
				$obConfig->Set('home_keywrds',htmlentities($_POST['sc_home_keywrds'],ENT_QUOTES,'UTF-8'));
				$obConfig->Set('copyright',htmlentities($_POST['sc_copyright'],ENT_QUOTES,'UTF-8'));
				$obConfig->Set('debugmode',1);//intval($_POST['sc_debugmode']);
				$obConfig->Set('start_adminpage',htmlentities($_POST["sc_start_adminpage"], ENT_QUOTES, 'UTF-8'));
				$obConfig->Set('text_ident_length',(intval($_POST['sc_text_ident_length'])>15?intval($_POST['sc_text_ident_length']):15));
				$obConfig->Set('highlight_new_elements',(in_array($_POST['sc_highlight_new_elements'],array('no','all','my'))?$_POST['sc_highlight_new_elements']:'no'));
				$obConfig->Set('highlight_time',300);
				$obConfig->Set('lifetime',(intval($_POST['sc_lifetime'])>0?intval($_POST['sc_lifetime']):864000));
				$obConfig->Set('highlight_color','fff74b');
				$obConfig->Set('highlight_odd_row_color','70808D');
				//Проверка валидности ключа обновлений
				if(strlen($_POST['sc_pkey'])==0)
				{
					$error+=$this->obModules->AddNotify('MAIN_OPTIONS_NO_KEY');
				}
				else
				{
					if(preg_match('#^KS[A-Z]-[0-9]{10,10}-[0-9]{4,4}-[0-9]{8,8}$#',$_POST['sc_pkey']))
						$obConfig->Set('pkey',$_POST['sc_pkey']);
					elseif($_POST['sc_pkey']=='demo')
						$obConfig->Set('pkey','demo');
					else
						$error+=$this->obModules->AddNotify('MAIN_OPTIONS_WRONG_KEY');
				}
				//Проверка валидности сервера обновлений
				if(preg_match('#^([a-z0-9]+\.)+[a-z]+$#',$_POST['sc_update_server']))
					$obConfig->Set('update_server',$_POST['sc_update_server']);
				else
					$error+=$this->obModules->AddNotify('MAIN_OPTIONS_WRONG_UPDATE_SERVER');
				if(preg_match('#^[\w\d\-_\.]+@[\w\d\-_\.]+\.[\w]+$#',$_POST['sc_admin_email']))
					$obConfig->Set('admin_email',$_POST['sc_admin_email']);
				else
					$error+=$this->obModules->AddNotify('MAIN_ERROR_ADMIN_EMAIL');
				if(IsEmail($_POST['sc_emailFrom']))
					$obConfig->Set('emailFrom',$_POST['sc_emailFrom']);
				else
					$error+=$this->obModules->AddNotify('MAIN_ERROR_FROM_EMAIL');
				$obConfig->Set('time_format',htmlentities($_POST['sc_time_format'],ENT_QUOTES,'UTF-8'));
				$obConfig->Set('user_inactive_time',(intval($_POST['sc_user_inactive_time'])>150?intval($_POST['sc_user_inactive_time']):150));
				if(preg_match('#[a-z]{2,2}#',$_POST['admin_lang']))
				{
					$obConfig->Set('admin_lang',$_POST['admin_lang']);
				}
				else
				{
					//Язык по умолчанию - русский
					$obConfig->Set('admin_lang','ru');
				}
				$this->obModules->RecountTextStructure();
				if($error>0) throw new CDataError('MAIN_OPTIONS_ERRORS');
				$obConfig->WriteConfig();

				//Выполняем сохранение прав доступа
				if(is_array($_POST['sc_groupLevel']))
				{
					foreach($_POST['sc_groupLevel'] as $key=>$value)
					{
						//echo min($value);
						$obAccess->Set($key,'main',min($value));
					}
				}
				/**
				 * @todo изменить строку о сохранении настроек на константу
				 */
				$this->obModules->AddNotify('MAIN_OPTIONS_UPDATE_OK','',NOTIFY_MESSAGE);
				CUrlParser::get_instance()->Redirect("admin.php?module=main&modpage=options");
			}
			catch (EXCEPTION $e)
			{
				$ks_config=$obConfig->GetConfig();
				$this->smarty->assign('last_error',$e);
			}
		}
		$this->smarty->assign('showTreeView',$this->obModules->GetConfigVar('main','showTreeView','Y'));
		$this->smarty->assign('access',$arAccess);
		$this->smarty->assign('data',$ks_config);
		return '_options';
	}
}

