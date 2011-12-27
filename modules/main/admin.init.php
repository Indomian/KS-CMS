<?php
/**
 * Файл инициализации административного интерфейса главного модуля
 * Выполняет инициализацию системных переменных и классов, подключение базовых модулей, генерацию системного меню
 *
 * @filesource admin.init.php
 * @author BlaDe39 <blade39@kolosstudio.ru>
 * @version 2.7
 * @since 30.11.2011
 */

/* Проверка легальности подключения файла */
if (!defined("KS_ENGINE"))	die("Hacking attempt!");

//Регистрируем базовый подключатель классов
include_once MODULES_DIR.'/main/autoload.php';

require_once MODULES_DIR.'/main/libs/class.CAdminModuleManagment.php';
require_once MODULES_DIR.'/main/libs/class.CSessionManager.php';
/* Полезные функции */
require_once MODULES_DIR."/main/libs/functions.php";

/* Подключения файла системной конфигурации */
include(CONFIG_DIR . "/sys_config.php");

/* Подключение класса для работы с базой данных и инициализация его объекта */
include_once(CONFIG_DIR . "/db_config.php");
if(file_exists(MODULES_DIR.'/main/libs/db/'.KS_DB_ENGINE.'.php'))
{
	require_once MODULES_DIR."/main/libs/db/".KS_DB_ENGINE.'.php';
	$sClassName=KS_DB_ENGINE;
	$ks_db = new $sClassName(KS_DEBUG);
}
else
	throw new CError("SYSTEM_DB_INIT_ERROR",500);

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
require_once "libs/class.CError.php";
set_error_handler(array('CError',"PhpErrorHandler"));

/* Подключение класса-обработчика событий */
require_once MODULES_DIR . "/main/libs/class.CEventsHandler.php";
$KS_EVENTS_HANDLER = new CEventsHandler(CONFIG_DIR . "/events_config.php");

/* Подключение поддержки каптчи */
require_once MODULES_DIR . "/main/libs/captcha/kcaptcha.php";
$smarty->register_function("captchaImageUrl", array("CCaptcha","GetCaptchaUrl"));
/*Инициализация работы с файловой системой*/
require_once "libs/class.CFileSystem.php";
$KS_FS = new CSimpleFs();
/* Подключение и опрос модулей */
require_once "libs/class.CModuleHookUp.php";

$KS_MODULES=CAdminModuleManagment::get_instance();
setlocale(LC_NUMERIC, 'C');
date_default_timezone_set('Europe/Moscow');

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
		$USER->Login();
if(array_key_exists('CU_ACTION',$_REQUEST) && $_REQUEST['CU_ACTION']=='logout')
	$USER->Logout();

$KS_MODULES->SetUser($USER);
$KS_MODULES->LinkModules();

$smarty->assign('SITE', $KS_MODULES->GetConfigArray("main"));
/* Подтверждение успешной инициализации */
$initParams = array();
$KS_EVENTS_HANDLER->Execute("main", "onInit", $initParams);
define("KS_MAIN_INIT",1);

if($USER->GetLevel('main')> 9)
	throw new CAccessError("MAIN_ACCESS_ADMINISTRATIVE_PART_CLOSED", 403);