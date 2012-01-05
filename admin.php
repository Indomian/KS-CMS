<?php

/**
 * Главный административный файл ЦМС
 *
 * Производит логин пользователя и восстановление забытого пароля
 *
 * @filesource admin.php
 * @author blade39 <blade39@kolosstudio.ru>, Ilya Doroshko <ilya@kolosstudio.ru>, north-e <pushkov@kolosstudio.ru>
 *
 * @version 1.0
 * @since 25.03.2009
 *
 * 1. Переход к поддержке разнообразных кодировок и языковых файлов.
 * 2. Установка кодировки UTF-8 базовой кодировкой.
 *
 * @version 1.1
 * @since 11.05.2009
 *
 * Исправлены ошибки при восстановлении пароля
 *
 * @version 2.5
 * Добавлена поддержка текстовых констант из файла version.php
 *
 * @version 2.5.4
 * Добавлена поддержка команды update, и команды restore
 * для выполнения операций связанных с работой системы обновления
 * и восстановления системы в случае провала обновления
 *
 * @version 2.5.5
 * @since 17.03.2010
 * @author blade39 <blade39@kolosstudio.ru>
 * Добавлен поиск шаблона, в случае если не обнаружен шаблон с именем модуля
 * и именем страницы, шаблон ищется в списке стандартных шаблонов
 */

// Получаем время запуска скрипта
$begin=microtime(1);

//Принудительно выключаем поддержку magic_quotes_gpc
ini_set('magic_quotes_gpc','0');

define('KS_ENGINE',		true); 						//< Установка константы движка ЦМС.
define('ROOT_DIR',		dirname (__FILE__));		//< Получения корневого каталога ЦМС.
define('MODULES_DIR',	ROOT_DIR . '/modules');		//< Установка папки для модулей.
define('CONFIG_DIR',	ROOT_DIR . '/cnf');			//< Установка папки для хранения конфигурационных файлов.
define('SYS_TEMPLATES_DIR',	ROOT_DIR . '/templates'); 	//< Папка для хранения шаблонов.
define('JS_DIR', '/js');
define('UPLOADS_DIR', 	ROOT_DIR.'/uploads');
define('TEMPLATES_DIR',	UPLOADS_DIR.'/templates');
define('EVENT_TEMPLATES_DIR', ROOT_DIR.'/templates/admin/eventTemplates');
define('IS_ADMIN',		true);						//< Флаг указывающий на то что работает административный раздел.

//Устанавливаем заголовок о том что отдаем все в UTF-8
header('Content-Type: text/html; charset=UTF-8');
setlocale(LC_ALL, "ru_RU.UTF-8");

//Работа системы обновления
if(array_key_exists('update',$_GET) && file_exists(MODULES_DIR.'/main/libs/class.CUpdate.php'))
{
	$obUpdate=new CUpdate();
	$obUpdate->Go();
}
elseif(array_key_exists('restore',$_GET) && file_exists(MODULES_DIR.'/main/libs/class.CUpdate.php'))
{
	$obUpdate=new CUpdate();
	$obUpdate->Restore();
}

if (file_exists(MODULES_DIR.'/main'))
{
	try
	{
		//! Подключение главного модуля ЦМС, определение пользователя и др.
		include_once(MODULES_DIR.'/main/admin.init.php');
	}
	catch(CAccessError $e)
	{
		if($smarty)
		{
			$smarty->assign('last_error',$e->GetErrorText());
			$KS_MODULES->LoadModulePage('main','password');
		}
		else
		{
			echo "<html><title>{$KS_VERSION['TITLE']} {$KS_VERSION['ID']} Init Error</title><body>$e</body></html>";
			die();
		}
	}
	catch(CDBError $e)
	{
		echo "<html><title>Init Error</title><body>$e</body></html>";
		die();
	}
	catch(CError $e)
	{
		if($smarty)
			$smarty->assign('last_error',$e->GetErrorText());
		else
		{
			echo "<html><title>{$KS_VERSION['TITLE']} {$KS_VERSION['ID']} Init Error</title><body>$e</body></html>";
			die();
		}
	}

	/* Пользователь не вошел на сайт */
	if (!$USER->IsLogin())
		$KS_MODULES->LoadModulePage('main','password');
	else
		// Выводим текущий модуль (определяется по параметрам в УРЛ).
		$KS_MODULES->AdminShowModule();
	$KS_MODULES->Draw($smarty);
	if(KS_RELEASE!=1 && KS_DEBUG==1)
	{
		$end=microtime(1);
		$sys_info['gen_tyme'] = ($end-$begin);
		$sys_info['sql_gen_tyme'] = $ks_db->GetTimeTaken();
		$sys_info['sql_queries_quant'] = $ks_db->GetQueriesCount();
		$sys_info['sql_requests']=$ks_db->GetRequests();
		$smarty->assign('sys_info', $sys_info);
		$smarty->display('admin/.sysfooter.tpl');
	}
}
else
{
	echo "Система KS-CMS не установлена! (KS-CMS system is not setup)";
	die();
}
