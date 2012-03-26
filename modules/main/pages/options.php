<?php
/**
 * @file main/pages/options.php
 * Файл обработки настроек модуля main
 * Файл проекта kolos-cms.
 *
 * Изменен 13.01.2011
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CModuleOptions.php';

class CmainAIoptions extends CModuleOptions
{
	function __construct($module='main',&$smarty,&$parent)
	{
		parent::__construct($module,$smarty,$parent);
		if($this->obUser->GetLevel($this->module)>0)
			throw new CAccessError('MAIN_ACCESS_SITE_PREFERENCES_CLOSED');
	}

	/**
	 * Метод выполняет очистку кэша шаблонов
	 */
	function DropCache()
	{
		global $KS_FS;
		$this->smarty->clear_all_cache();
		$this->smarty->clear_compiled_tpl();
		$this->obModules->AddNotify('MAIN_OPTIONS_CACHE_CLEARED','',NOTIFY_MESSAGE);
		try
		{
			if(!$KS_FS->ClearDir(UPLOADS_DIR.'/PicCache'))
				$this->obModules->AddNotify('MAIN_PICTURE_CACHE_CLEAN_FAIL');
			else
				$this->obModules->AddNotify('MAIN_OPTIONS_IMAGES_CACHE_CLEARED','',NOTIFY_MESSAGE);
		}
		catch(Exception $e)
		{
			$this->obModules->AddNotify('MAIN_PICTURE_CACHE_CLEAN_FAIL');
		}
		if(defined('KS_CACHE_HTML_DIR'))
			$sCachePath=KS_CACHE_DIR;
		else
			$sCacheDir=MODULES_DIR.'/main/cache/';
		try
		{
			if($KS_FS->ClearDir($sCacheDir))
				$this->obModules->AddNotify('MAIN_OPTIONS_SYSTEM_CACHE_CLEARED','',NOTIFY_MESSAGE);
			else
				$this->obModules->AddNotify('MAIN_OPTIONS_SYSTEM_CACHE_CLEAR_ERROR');
		}
		catch(Exception $e)
		{
			$this->obModules->AddNotify('MAIN_OPTIONS_SYSTEM_CACHE_CLEAR_ERROR');
		}
	}

	/**
	 * Метод выполняет установку всех шаблонов сайта, скриптов, собирает языковые файлы
	 */
	function RebuildAll()
	{
		$this->obModules->CopyModuleTemplates('main');
		$this->obModules->InstallJSFiles('main');
		if($arModules=$this->obModules->GetList(false,array('active'=>1)))
			foreach($arModules as $arModule)
			{
				$this->obModules->CopyModuleTemplates($arModule['directory']);
				$this->obModules->InstallJSFiles($arModule['directory']);
			}
		$this->obModules->AddNotify('MAIN_OPTIONS_TEMPLATES_COPIED','',NOTIFY_MESSAGE);
		$this->obModules->RecountTextStructure();
		$this->obModules->AddNotify('MAIN_OPTIONS_LANGUAGE_UPDATED','',NOTIFY_MESSAGE);
		$this->obModules->AddNotify('MAIN_OPTIONS_JS_UPDATED','',NOTIFY_MESSAGE);
	}

	/**
	 * Метод выполняет обновление всех шаблонов сайта
	 */
	function UpdateTemplates()
	{
		$this->obModules->CopyModuleTemplates('main');
		if($arModules=$this->obModules->GetList(false,array('active'=>1)))
			foreach($arModules as $arModule)
				$this->obModules->CopyModuleTemplates($arModule['directory']);
		$this->obModules->AddNotify('MAIN_OPTIONS_TEMPLATES_COPIED','',NOTIFY_MESSAGE);
	}

	/**
	 * Метод выполняет проверку структуры таблиц базы данных
	 */
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

	/**
	 * Основной метод, выполняет определение функции и её выполнение
	 */
	function Run()
	{
		if(array_key_exists('act_drop_cache',$_POST))
			$this->DropCache();
		elseif(array_key_exists('act_check_tables',$_POST))
			$this->CheckTables();
		elseif(array_key_exists('act_rebuild_all',$_POST))
			$this->RebuildAll();
		elseif(array_key_exists('act_update_templates',$_POST))
			$this->UpdateTemplates();
		elseif($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['action']) && $_POST['action']=='save')
		{
			try
			{
				$error=0;
				//Проверка правильности введенного урл
				if(preg_match('#^http://([\w\d\.\-_]+)$#',$_POST['sc_home_url']))
					$this->obConfig->Set('home_url',$_POST['sc_home_url']);
				else
					$error+=$this->obModules->AddNotify('MAIN_OPTIONS_WRONG_SITE_URL');
				$this->obConfig->Set('home_title',htmlentities($_POST['sc_home_title'],ENT_QUOTES,'UTF-8'));
				$this->obConfig->Set('home_descr',htmlentities($_POST['sc_home_descr'],ENT_QUOTES,'UTF-8'));
				$this->obConfig->Set('home_keywrds',htmlentities($_POST['sc_home_keywrds'],ENT_QUOTES,'UTF-8'));
				$this->obConfig->Set('copyright',htmlentities($_POST['sc_copyright'],ENT_QUOTES,'UTF-8'));
				$this->obConfig->Set('debugmode',1);//intval($_POST['sc_debugmode']);
				$this->obConfig->Set('start_adminpage',htmlentities($_POST["sc_start_adminpage"], ENT_QUOTES, 'UTF-8'));
				$this->obConfig->Set('text_ident_length',(intval($_POST['sc_text_ident_length'])>15?intval($_POST['sc_text_ident_length']):15));
				$this->obConfig->Set('highlight_new_elements',(in_array($_POST['sc_highlight_new_elements'],array('no','all','my'))?$_POST['sc_highlight_new_elements']:'no'));
				$this->obConfig->Set('highlight_time',300);
				$this->obConfig->Set('lifetime',(intval($_POST['sc_lifetime'])>0?intval($_POST['sc_lifetime']):864000));
				$this->obConfig->Set('highlight_color','fff74b');
				$this->obConfig->Set('highlight_odd_row_color','70808D');
				if(in_array($_POST['items_count'],array(10,20,50,100)))
					$this->obConfig->Set('admin_items_count',$_POST['items_count']);
				else
					$this->obConfig->Set('admin_items_count',10);
				//Auth
				$this->obConfig->Set('user_inactive_time',(intval($_POST['sc_user_inactive_time'])>150?intval($_POST['sc_user_inactive_time']):150));
				if(isset($_POST['sc_user_inactive_check']))
					$this->obConfig->Set('user_inactive_check',intval($_POST['sc_user_inactive_check']));
				else
					$this->obConfig->Set('user_inactive_check',0);
				if(isset($_POST['sc_enable_auth_save']))
					$this->obConfig->Set('enable_auth_save',intval($_POST['sc_enable_auth_save']));
				else
					$this->obConfig->Set('enable_auth_save',0);
				//Проверка валидности ключа обновлений
				if(strlen($_POST['sc_pkey'])==0)
					$error+=$this->obModules->AddNotify('MAIN_OPTIONS_NO_KEY');
				else
				{
					if(preg_match('#^KS[A-Z]-[0-9]{10,10}-[0-9]{4,4}-[0-9]{8,8}$#',$_POST['sc_pkey']))
						$this->obConfig->Set('pkey',$_POST['sc_pkey']);
					elseif($_POST['sc_pkey']=='demo')
						$this->obConfig->Set('pkey','demo');
					else
						$error+=$this->obModules->AddNotify('MAIN_OPTIONS_WRONG_KEY');
				}
				//Проверка валидности сервера обновлений
				if(preg_match('#^([a-z0-9]+\.)+[a-z]+$#',$_POST['sc_update_server']))
					$this->obConfig->Set('update_server',$_POST['sc_update_server']);
				else
					$error+=$this->obModules->AddNotify('MAIN_OPTIONS_WRONG_UPDATE_SERVER');
				if(preg_match('#^[\w\d\-_\.]+@[\w\d\-_\.]+\.[\w]+$#',$_POST['sc_admin_email']))
					$this->obConfig->Set('admin_email',$_POST['sc_admin_email']);
				else
					$error+=$this->obModules->AddNotify('MAIN_ERROR_ADMIN_EMAIL');
				if(IsEmail($_POST['sc_emailFrom']))
					$this->obConfig->Set('emailFrom',$_POST['sc_emailFrom']);
				else
					$error+=$this->obModules->AddNotify('MAIN_ERROR_FROM_EMAIL');
				$this->obConfig->Set('time_format',htmlentities($_POST['sc_time_format'],ENT_QUOTES,'UTF-8'));
				if(preg_match('#[a-z]{2,2}#',$_POST['admin_lang']))
					$this->obConfig->Set('admin_lang',$_POST['admin_lang']);
				else
					//Язык по умолчанию - русский
					$this->obConfig->Set('admin_lang','ru');
				$this->obModules->RecountTextStructure();
				if($error>0) throw new CDataError('MAIN_OPTIONS_ERRORS');
				$this->obConfig->WriteConfig();

				//Выполняем сохранение прав доступа
				$this->SaveAccessLevels();
				$this->obModules->AddNotify('MAIN_OPTIONS_UPDATE_OK','',NOTIFY_MESSAGE);
				$this->obUrl->Redirect("admin.php?module=main&modpage=options");
			}
			catch (EXCEPTION $e)
			{
				$this->smarty->assign('last_error',$e);
			}
		}
		$this->smarty->assign('showTreeView',$this->obModules->GetConfigVar('main','showTreeView','Y'));
		$this->smarty->assign('data',$this->obConfig->GetConfig());
		$this->smarty->assign('access',$this->GetAccessLevels());
		return '_options';
	}
}

