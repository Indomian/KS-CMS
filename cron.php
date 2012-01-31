<?php
/**
 * Файл обеспечивает выполнение разнообразных операций с системой без подключения функций внешнего интерфейса.
 * Обеспечивает использование стандартного соединения с БД и выполнение специализированных скриптов
 *
 * @file /cron.php
 * @author BlaDe39 <blade39@kolosstudio.ru>
 * @version 1.0
 */

define('KS_ENGINE',		true);
define('ROOT_DIR',		dirname (__FILE__)); //Перенастроить при переносе на другой сервер!
define('CRON_SCRIPT_LOCATION', ROOT_DIR.'/cron'); //Путь к скриптам выполняемым по команде
define('MODULES_DIR',	ROOT_DIR.'/modules');
define('CONFIG_DIR',	ROOT_DIR.'/cnf');
define('UPLOADS_DIR', 	ROOT_DIR.'/uploads');

define('EVENT_TEMPLATES_DIR', ROOT_DIR.'/templates/admin/eventTemplates');
define('IS_ADMIN',false);
$begin=microtime(1);
error_reporting(E_ALL);

try
{
	/* Устанавливаем локаль */
	setlocale(LC_ALL, "ru_RU.UTF-8");

	/* системная конфигурация */
	include(CONFIG_DIR.'/sys_config.php');
	define("ERROR_LEVEL", $ks_config['debugmode']);

	/* ошибки */
	require_once MODULES_DIR.'/main/libs/console/class.CConsoleError.php';
	set_error_handler(array('CError',"PhpErrorHandler"));
	/* smarty */
	require_once MODULES_DIR.'/main/libs/console/class.CConsoleSmarty.php';
	$smarty=new CSmartyDummy();
	/* сессия */
	require_once MODULES_DIR.'/main/libs/console/class.CConsoleSessionManager.php';
	/* БД */
	require_once MODULES_DIR.'/main/libs/db/' . $ks_config['DB_CLASS'] . '.php';
	include(CONFIG_DIR.'/db_config.php');

	/*Полезные функции*/
	require_once MODULES_DIR.'/main/libs/functions.php';

	/* подключение класса-обработчика событий */
	require_once MODULES_DIR . '/main/libs/class.CEventsHandler.php';
	$KS_EVENTS_HANDLER = new CEventsHandler(CONFIG_DIR . '/events_config.php');

	/* Пользовательские поля */
	if (file_exists(MODULES_DIR.'/main/libs/class.CFields.php'))
		include_once MODULES_DIR.'/main/libs/class.CFields.php';

	/* пользователи */
	require_once MODULES_DIR.'/main/libs/console/class.CConsoleUser.php';
	$USER = new CConsoleUser();
	/*Подключение системы событий*/
	require_once MODULES_DIR.'/main/libs/class.CEvents.php';
	$obEvents=new CEvents();

	/* Работа с файловой системой */
	require_once MODULES_DIR.'/main/libs/class.CSimpleFs.php';
	$KS_FS=new CSimpleFs();

	/* класс подключение модулей */
	require_once MODULES_DIR.'/main/libs/class.CModuleHookUp.php';
	$KS_MODULES = new CModuleHookUp();

	/* Подтверждение успешной инициализации */
	$initParams = array();
	if(!$KS_EVENTS_HANDLER->Execute('main', 'onInit', $initParams))
		die('Ошибка обработчика ' . $KS_EVENTS_HANDLER->GetLastEvent());
	define("KS_MAIN_INIT", 1);

	if($argc>1)
		if(IsTextIdent($argv[1]) && file_exists(CRON_SCRIPT_LOCATION.'/'.$argv[1].'.php'))
			include_once(CRON_SCRIPT_LOCATION.'/'.$argv[1].'.php');
	$obEvents->init();
	$obEvents->Run();
	$obEvents->Done();
}
catch (CError $e)
{
	echo $e;
}
catch(Exception $e)
{
	echo $e;
}