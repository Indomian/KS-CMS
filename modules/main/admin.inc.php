<?php
/**
 * Основной файл системы администрирования KS ENGINE
 *
 * Выполняет инициализацию системных переменных и классов, подключение базовых модулей, генерацию системного меню
 *
 * @filesource main/admin.inc.php
 * @author BlaDe39, north-e <pushkov@kolosstudio.ru>
 * @version 2.6
 * Изменения:
 * 2.6 - обновлен порядок подключений файлов, внесены исправления в настройки
 * 0.9.1	- подробные комментарии к действиям скрипта;
 * 			- подключён класс обработки событий, вызов обработчика инициализации главного модуля;
 * 0.9		- изменилась кодировка файла на UTF-8;
 *			- добавлена поддержка функции вывода текстовых сообщений
 * @since 24.03.2009
 */

/* Проверка легальности подключения файла */
if (!defined("KS_ENGINE"))	die("Hacking attempt!");

include_once MODULES_DIR.'/main/libs/class.CAdminModuleManagment.php';

/* Проверка на выполнение инициализации */
if (!defined("KS_MAIN_INIT"))
{
	/* Запуск сессии */
	/**
	 * @todo Исправить!
	 */
	date_default_timezone_set('Europe/Moscow');
	require_once MODULES_DIR.'/main/libs/class.CSessionManager.php';
	include_once MODULES_DIR.'/main/libs/class.CError.php';
	setlocale(LC_NUMERIC, 'C');
	/* Полезные функции */
	require_once "libs/functions.php";

	/* Подключения файла системной конфигурации */
	include(CONFIG_DIR . "/sys_config.php");
	define("ERROR_LEVEL", $ks_config['debugmode']);

	/* Подключение класса для работы с базой данных и инициализация его объекта */
	require_once MODULES_DIR . "/main/libs/db/" . $ks_config["DB_CLASS"] . '.php';
	include_once(CONFIG_DIR . "/db_config.php");
	if(!defined('KS_LOG_DB_ERRORS')) define('KS_LOG_DB_ERRORS',1);
	/* Проверка структуры базы данных по требованию */
	if($ks_config['update_db']==1)
	{
		include_once MODULES_DIR.'/main/libs/class.CConfigParser.php';
		$obConfig=new CConfigParser('main');
		$obConfig->LoadConfig();
		include_once(CONFIG_DIR.'/db_structure.php');
		$ks_db->CheckDB($arStructure);
		$obConfig->Set('update_db',0);
		$obConfig->WriteConfig();
	}

	/* Инициализация Смарти */
	require(MODULES_DIR  ."/main/libs/class.CSmartyExtender.php");
	$smarty = new CSmartyExtender;
	$smarty->template_dir	= SYS_TEMPLATES_DIR."/";
	$smarty->compile_dir	= SYS_TEMPLATES_DIR."/templates_c";
	$smarty->config_dir		= SYS_TEMPLATES_DIR."/configs";
	$smarty->cache_dir		= SYS_TEMPLATES_DIR."/cache";
	$smarty->plugins_dir 	= array(MODULES_DIR.'/main/libs/smarty/plugins',MODULES_DIR.'/main/widgets');
	/* Настройки безопасности смарти */
	include_once CONFIG_DIR.'/smarty.php';

	/* Домен для Смарти */
	$smarty->assign("home_domain", $ks_config["home_url"]);
	/* Устанавливаем директорию загрузки файлов модулей и шаблонов */
	if(!defined('SITE_UPLOADS_DIR')) define('SITE_UPLOADS_DIR',ROOT_DIR.'/uploads');
	$smarty->assign("uploads_folder", SITE_UPLOADS_DIR);

	if(!defined('SITE_TEMPLATES_DIR')) define('SITE_TEMPLATES_DIR',SITE_UPLOADS_DIR.'/templates');
	/* Устанавливаем директорию с файлами шаблонов относительно корня сайта (будет использоваться в самих шаблонах) */
	$smarty->assign("templates_files_folder", SITE_TEMPLATES_DIR);

	/* Подключение класса ошибок */
	set_error_handler(array('CError',"PhpErrorHandler"));

	/* Подключение класса-обработчика событий */
	require_once MODULES_DIR . "/main/libs/class.CEventsHandler.php";
	$KS_EVENTS_HANDLER = new CEventsHandler(CONFIG_DIR . "/events_config.php");

	/* Подключение поддержки каптчи */
	require_once MODULES_DIR . "/main/libs/captcha/kcaptcha.php";
	$smarty->register_function("captchaImageUrl", array("CCaptcha","GetCaptchaUrl"));
	/*Инициализация работы с файловой системой*/
	require_once "libs/class.CSimpleFs.php";
	$KS_FS = new CSimpleFs();
	/* Подключение и опрос модулей */
	require_once "libs/class.CMain.php";
	require_once "libs/class.CModuleHookUp.php";

	$KS_MODULES=CAdminModuleManagment::get_instance();

	/* Подключение класса обработки url */
	require_once "libs/class.CUrlParser.php";
	$KS_URL = CUrlParser::get_instance();

	/* Дополнительные библиотеки */
	require_once "libs/class.CTemplates.php";
	require_once "libs/class.CLanguageSmarty.php";


	/* Пользовательские поля */
	if (file_exists(MODULES_DIR."/main/libs/class.CFields.php"))
		include_once MODULES_DIR."/main/libs/class.CFields.php";

	/* Устанавливаем смарти */
	$KS_MODULES->SetSmarty($smarty);
	$smarty->assign('VERSION',$KS_MODULES->GetVersionData());

	/* Инициализируем поддержку языков */
	$obLang=new CLanguageSmarty($smarty,$KS_MODULES->GetConfigVar('main','admin_lang','ru').'/admin.conf');
	$obLang->LoadSection();
	$KS_MODULES->SetLanguage($obLang);
	$KS_MODULES->SetLanguageError(new CLanguageSmarty($smarty,$KS_MODULES->GetConfigVar('main','admin_lang','ru').'/error.conf'));

	/* Подключение модуля управления учётными записями пользователей */
	require_once "libs/class.CUser.php";
	$USER = new CUser();
	if($_SERVER['REQUEST_METHOD']=='POST')
		if(array_key_exists('CU_ACTION',$_POST) && $_POST['CU_ACTION']=='login')
			$USER->login();
	if(isset($_REQUEST['CU_ACTION']) && $_REQUEST['CU_ACTION']=='logout')
		$USER->logout();

	$KS_MODULES->SetUser($USER);

	$KS_MODULES->LinkModules();

	$smarty->assign('SITE', $KS_MODULES->GetConfigArray("main"));
	/* Подтверждение успешной инициализации */
	$initParams = array();
	$KS_EVENTS_HANDLER->Execute("main", "onInit", $initParams);
	define("KS_MAIN_INIT",1);

	if($USER->GetLevel('main')> 9)
		throw new CAccessError("MAIN_ACCESS_ADMINISTRATIVE_PART_CLOSED", 403);
	/* Список модулей, поддерживающик связь между элементами полей */
	$_ks_modules_linkable = array("catsubcat","blog","photogallery",'production');
}
else
{
	$bInclude = false;
	$iLevel=$USER->GetLevel('main');
	if ($iLevel>9)
		throw new CAccessError("MAIN_ACCESS_ADMINISTRATIVE_PART_CLOSED", 403);
	elseif($iLevel==9)
	{
		$smarty->assign('bShowOnlyUsers','Y');
		$start_adminpage='users';
	}
	else
	{
		/* Определение страницы администрирования (если не указана, берётся по умолчанию из настроек сайта) */
		if (array_key_exists("page", $_GET))
			$start_adminpage = $_GET['page'];
		elseif (array_key_exists("modpage", $_GET))
			$start_adminpage = $_GET["modpage"];
		else
			$start_adminpage = $this->GetConfigVar("main", "start_adminpage");
	}
	if (file_exists(MODULES_DIR . "/main/pages/" . $start_adminpage . ".php"))
	{
		$page=$this->LoadModulePage('main',$start_adminpage);
		$bInclude = true;
	}
	else
		throw new CError("SYSTEM_WRONG_ADMIN_PATH", 1003);
}
