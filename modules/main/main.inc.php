<?php
/**
 * Файл инициализации главного модуля системы KS-CMS
 *
 * Выполняет инициализацию системных переменных и классов, подключение базовых модулей, генерацию системного меню
 *
 * @filesource admin.inc.php
 * @author BlaDe39, north-e <pushkov@kolosstudio.ru>
 * @version 2.6
 * Изменения:
 * 2.6 - обновлен порядок подключений файлов, внесены исправления в настройки
 * @since 24.03.2009
 */

if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}
if (!defined('KS_MAIN_INIT'))
{
	/* Запускаем сессию */
	require_once MODULES_DIR.'/main/libs/class.CSessionManager.php';
	include_once MODULES_DIR.'/main/libs/class.CError.php';
	/* Устанавливаем локаль */
	setlocale(LC_ALL, "ru_RU.UTF-8");
	setlocale(LC_NUMERIC, 'C');
	/* Полезные функции */
	require_once MODULES_DIR."/main/libs/functions.php";

	/* Отправляем заголовок с кодировкой */
	header('Content-Type: text/html; charset=UTF-8');

	/* системная конфигурация */
	include(CONFIG_DIR.'/sys_config.php');
	define("ERROR_LEVEL", $ks_config['debugmode']);

	/* БД */
	require_once MODULES_DIR.'/main/libs/db/' . $ks_config['DB_CLASS'] . '.php';
	include(CONFIG_DIR.'/db_config.php');

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

	/* смарти */
	require(MODULES_DIR.'/main/libs/smarty/Smarty.class.php');
	$smarty = new Smarty;
	$smarty->template_dir	= TEMPLATES_DIR.'/';
	$smarty->compile_dir	= SYS_TEMPLATES_DIR.'/templates_c/';
	$smarty->config_dir		= SYS_TEMPLATES_DIR.'/configs/';
	$smarty->cache_dir		= SYS_TEMPLATES_DIR.'/cache/';
	$smarty->plugins_dir 	= array(MODULES_DIR.'/main/libs/smarty/plugins/',MODULES_DIR.'/main/widgets/');
	/* Настройки безопасности смарти */
	include_once CONFIG_DIR.'/smarty.php';

 /* домен для смарти */
	$smarty->assign('home_domain', $ks_config['home_url']);

	/* Устанавливаем директорию загрузки файлов модулей и шаблонов */
	$smarty->assign("uploads_folder", SITE_UPLOADS_DIR);

	/* Устанавливаем директорию с файлами шаблонов относительно корня сайта (будет использоваться в самих шаблонах) */
	$smarty->assign("templates_files_folder", SITE_TEMPLATES_DIR);

	/* ошибки */
	set_error_handler(array('CError',"PhpErrorHandler"));

	/* подключение класса-обработчика событий */
	require_once MODULES_DIR . '/main/libs/class.CEventsHandler.php';
	$KS_EVENTS_HANDLER = new CEventsHandler(CONFIG_DIR . '/events_config.php');

	/*Подключаем поддержку каптчи*/
	require_once MODULES_DIR.'/main/libs/captcha/kcaptcha.php';
	$smarty->register_function("captchaImageUrl", array('CCaptcha','GetCaptchaUrl'));
	/* Работа с файловой системой */
	require_once MODULES_DIR.'/main/libs/class.CSimpleFs.php';
	$KS_FS=new CSimpleFs();
	/* класс подключение модулей */
	require_once MODULES_DIR.'/main/libs/class.CModuleHookUp.php';
	/*подключение и обработка Url*/
	require_once MODULES_DIR.'/main/libs/class.CUrlParser.php';
	$KS_MODULES = CModuleHookUp::get_instance();
	$KS_MODULES->SetSmarty($smarty);

	$KS_URL = CUrlParser::get_instance();

	/* Пользовательские поля */
	if (file_exists(MODULES_DIR.'/main/libs/class.CFields.php'))
		include_once MODULES_DIR.'/main/libs/class.CFields.php';

	/* пользователи */
	require_once MODULES_DIR.'/main/libs/class.CUser.php';
	$USER = new CUser();
	/*Подключение системы событий*/
	require_once MODULES_DIR.'/main/libs/class.CEvents.php';
	$obEvents=new CEvents();

	/* Работа с аякс */
	require_once MODULES_DIR.'/interfaces/libs/class.CAjax.php';
	//Язык
	require_once "libs/class.CLanguageSmarty.php";

	/* Подключение других модулей */
	$KS_MODULES->SetUser($USER);
	$KS_MODULES->AutoInit();
	$KS_MODULES->InitTemplates();
	$site_config = $KS_MODULES->GetConfigArray("main");
	$smarty->assign('SITE', $site_config);

	$obLang=new CLanguageSmarty($smarty,$KS_MODULES->GetConfigVar('main','user_lang','ru').'/admin.conf');
	$obLang->LoadSection();
	$KS_MODULES->SetLanguage($obLang);
	$KS_MODULES->SetLanguageError(new CLanguageSmarty($smarty,$KS_MODULES->GetConfigVar('main','user_lang','ru').'/error.conf'));

	/* Подтверждение успешной инициализации */
	$initParams = array();
	$KS_EVENTS_HANDLER->Execute('main', 'onInit', $initParams);
	define("KS_MAIN_INIT", 1);
}
