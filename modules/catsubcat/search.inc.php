<?php

/**
 * Файл, реализующий редирект по ссылке поиска
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 1.0 
 * @since 30.05.2009
 */
 
/* Обязательно вставляем во все файлы для защиты от взлома */ 
if (!defined("KS_ENGINE"))
	die("Hacking attempt!");

global $smarty, $KS_IND_matches, $KS_MODULES, $KS_IND_dir, $global_template, $CCatsubcat, $USER;

/* Идентификатор модуля */
$module_name = "catsubcat";

/* Проверка прав доступа */
if ($USER->GetLevel($module_name) == 10)
	throw new CAccessError("SYSTEM_NOT_ACCESS_MODULE");

/* Настройки БД */
$module_db_config = $KS_MODULES->GetDBConfigArray($module_name);

/* Подключение необходимых классов и библиотек */
require_once(MODULES_DIR . "/" . $module_name . "/libs/class.CCategoryEdit.php");

if (strlen($hash) > 0)
{
	$code = substr($hash, 0, 1);
	if($code == "c")
	{
		/* Раздел */
		$id = intval(substr($hash, 1));
		$obCategory = new CCategory($module_db_config);
		$path = $obCategory->GetFullPath($id);
		$KS_URL->redirect($path);
		die();
	}	
	elseif ($code == "e")
	{
		/* Страница */
		$id = intval(substr($hash, 1));
		$obCategory = new CCategory($module_db_config);
		$obElement = new CElement($module_db_config);
		$arRow = $obElement->GetRecord(array('id' => $id));
		if ($arRow['id'] == $id)
		{
			$path = $obCategory->GetFullPath($arRow['parent_id']) . $arRow['text_ident'] . ".html";
			$KS_URL->redirect($path);
		}
		else
			$KS_URL->redirect($obCategory->GetFullPath(0));
	}
}

$KS_URL->redirect("/");

?>