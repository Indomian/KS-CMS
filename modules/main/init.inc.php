<?php
/**
 * Файл отвечает за инициализацию системы. Инициализация теперь единообразная для пользовательской и административной
 * частей системы
 */
if (!defined("KS_ENGINE")) die("Hacking attempt!");

if (!defined("KS_MAIN_INIT"))
{
	/* Запуск сессии */
	/**
	 * @todo Исправить!
	 */
	if(array_key_exists('KSSESSID',$_GET)) session_id($_GET['KSSESSID']);
	unset($_GET['KSSESSID']);
	session_name('KSSESSID');
	if (!session_start())
		echo "No sessions";

	setlocale(LC_NUMERIC, 'C');
	/* Полезные функции */
	require_once "libs/functions.php";

	/* Подключения файла системной конфигурации */
	include(CONFIG_DIR . "/sys_config.php");
	define("ERROR_LEVEL", $ks_config['debugmode']);

	/* Подключение класса для работы с базой данных и инициализация его объекта */
	require_once MODULES_DIR . "/main/libs/db/" . $ks_config["DB_CLASS"] . '.php';
	include(CONFIG_DIR . "/db_config.php");

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
	$smarty->compile_dir	= SYS_TEMPLATES_DIR."/templates_c/";
	$smarty->config_dir		= SYS_TEMPLATES_DIR."/configs/";
	$smarty->cache_dir		= SYS_TEMPLATES_DIR."/cache/";
	$smarty->plugins_dir 	= array(MODULES_DIR.'/main/libs/smarty/plugins/',MODULES_DIR.'/main/widgets/');
	/* Настройки безопасности смарти */
	$smarty->php_handling	= SMARTY_PHP_REMOVE;
	$smarty->security		= true;
	$smarty->security_settings=array(
                                'PHP_HANDLING'    => false,
                                'IF_FUNCS'        => array('array', 'list',
                                                           'isset', 'empty',
                                                           'count', 'sizeof',
                                                           'in_array', 'is_array',
                                                           'true', 'false', 'null','strlen'),
                                'INCLUDE_ANY'     => false,
                                'PHP_TAGS'        => false,
                                'MODIFIER_FUNCS'  => array('count','htmlspecialchars','intval','floatval','str_repeat','urlencode'),
                                'ALLOW_CONSTANTS'  => true
                               );

	/* Домен для Смарти */
	$smarty->assign("home_domain", $ks_config["home_url"]);
	$smarty->assign("uploads_folder", UPLOADS_DIR."/");
	$smarty->assign("templates_files_folder", UPLOADS_DIR."/templates/");

	/* Идентификатор (для ЧПУ) */
	$smarty->assign("SEP_ident", (($ks_config["SEF_URL"] === "1") ? "text_ident" : "id"));

	/* Подключение класса ошибок */
	set_error_handler(array('CError',"PhpErrorHandler"));

	/* Подключение класса-обработчика событий */
	require_once MODULES_DIR . "/main/libs/class.CEventsHandler.php";
	$KS_EVENTS_HANDLER = new CEventsHandler(CONFIG_DIR . "/events_config.php");

	/* Подключение поддержки каптчи */
	require_once MODULES_DIR . "/main/libs/captcha/kcaptcha.php";
	$smarty->register_function("captchaImageUrl", array("CCaptcha","GetCaptchaUrl"));

	/* Подключение и опрос модулей */
	require_once "libs/class.CMain.php";
	require_once "libs/class.CModuleHookUp.php";

	//$KS_MODULES = new CMain();
	$KS_MODULES=CMain::get_instance();

	/* Подключение класса обработки url */
	require_once "libs/class.CUrlParser.php";
	$KS_URL = new CUrlParser();

	/* Дополнительные библиотеки */
	require_once "libs/class.CTemplates.php";
	require_once "libs/class.CLanguageSmarty.php";
	require_once "libs/class.CFileSystem.php";
	$KS_FS = new CSimpleFs();

	/* Пользовательские поля */
	if (file_exists(MODULES_DIR."/main/libs/class.CFields.php"))
		include_once MODULES_DIR."/main/libs/class.CFields.php";

	/* Устанавливаем смарти */
	$KS_MODULES->SetSmarty($smarty);

	/* Инициализируем поддержку языков */
	$obLang=new CLanguageSmarty($smarty,$KS_MODULES->GetConfigVar('main','admin_lang','ru').'/admin.conf');
	$obLang->LoadSection();
	$KS_MODULES->SetLanguage($obLang);
	$KS_MODULES->SetLanguageError(new CLanguageSmarty($smarty,$KS_MODULES->GetConfigVar('main','admin_lang','ru').'/error.conf'));

	/* Подключение модуля управления учётными записями пользователей */
	require_once "libs/class.CUser.php";
	$USER = new CUser();
	if($_SERVER['REQUEST_METHOD']=='POST')
	{
		if($_POST['CU_ACTION']=='login')
		{
			$USER->login();
		}
	}

	$KS_MODULES->SetUser($USER);

	$KS_MODULES->LinkModules();

	$smarty->assign('SITE', $KS_MODULES->GetConfigArray("main"));
	/* Подтверждение успешной инициализации */
	$initParams = array();
	if(!$KS_EVENTS_HANDLER->Execute("main", "onInit", $initParams))
		die("Ошибка обработчика " . $KS_EVENTS_HANDLER->GetLastEvent());
	define("KS_MAIN_INIT",1);

	/* Список модулей, поддерживающик связь между элементами полей */
	$_ks_modules_linkable = array("catsubcat","blog","photogallery",'production');
}