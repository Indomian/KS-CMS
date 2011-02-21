<?php

/**
 * Главный файл модуля interfaces
 * 
 * @filesource main.inc.php
 * @author BlaDe39 <blade39@kolosstudio.ru>, North-E <pushkov@kolosstudio.ru>
 * @version 1.1
 * @since 03.06.2009
 * 
 * Добавлена поддержка виджетов
 */

if (!defined('KS_ENGINE'))
	die("Hacking attempt!");

/* Подключение необходимого класса */
include_once('libs/CInterface.php');

/* Установка папки шаблонов виджетов для модуля */
$smarty->plugins_dir[] = MODULES_DIR . '/interfaces/widgets/';

try
{
	if ($module_parameters['is_widget']==1)
	{
		global $smarty;
		if (file_exists(MODULES_DIR . '/interfaces/widgets/function.' . $module_parameters['action'] . '.php'))
		{
			include_once(MODULES_DIR . '/interfaces/widgets/function.' . $module_parameters['action'] . '.php');
			$output['main_content'] = call_user_func('smarty_function_' . $module_parameters['action'], $module_parameters, $smarty);
		}
		else
			throw new CError('SYSTEM_WIDGET_NOT_FOUND', 3001);
	}
}
catch (CError $e)
{
	$output['main_content']=$e;
}

?>