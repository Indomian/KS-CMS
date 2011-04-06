<?php

/**
 * Главный файл модуля текстовых страниц
 *
 * @author DoTJ
 * @version 0.1
 * @since 25.03.2008
 */

/* Защита от взлома */
if (!defined("KS_ENGINE"))
	die("Hacking attempt!");

/* Глобальные переменные */
global $USER, $KS_MODULES, $KS_IND_matches, $KS_IND_dir,  $CCatsubcat, $global_template, $smarty;

/* Идентификатор модуля */
$module_name = "catsubcat";

/* Проверка прав доступа пользователя */
if ($USER->GetLevel($module_name) == 10)
	throw new CAccessError("SYSTEM_NOT_ACCESS_MODULE");

/* Установка директории с плагинами модуля для Смарти */
$smarty->plugins_dir[] = MODULES_DIR . "/" . $module_name . "/widgets/";

/* Чтение конфигурации модуля */
$module_config = $KS_MODULES->GetConfigArray($module_name);

/* Если модуль подключается из шаблона, то не обращаем внимание на УРЛ */
if ($KS_IND_matches[3] == "index")
	$KS_IND_matches[2] = "";

/* Работаем как модуль, значит надо провести полную проверку переданного пути
	* на правильность и на права доступа, если что-то не так, лучше отдать ошибку */

	/* Путь к корню модуля */
$root_path = $this->GetSitePath($module_name);

if ($root_path != "/")
{
	/* Добавляем элемент навигационной цепочки */
	if ($this->GetConfigVar("catsubcat", "show_nav_chain",'1'))
		CNNavChain::get_instance()->Add( $this->arModules[$module_name]['name'],$root_path);

	$sUrl = $root_path;
	$iBase = 2;
}
else
{
	/* Модуль является модулем по умолчанию */
	$sUrl = "/";
	$iBase = 1;
}

/* Устанавливать заголовок страницы или нет */
$module_parameters['setPageTitle'] = $this->GetConfigVar($module_name, "set_title",1) ==1 ? "Y" : "N";

/* Родительский раздел */
$module_parameters['parent_id'] = 0;

/* Формирование навигационной цепочки */
if (count($KS_IND_dir) > $iBase)
{
	/* Объект для работы с категориями */
	$obCategory = new CCategory();
	$arFilter = array('parent_id' => 0);

	for ($i = $iBase; $i < count($KS_IND_dir); $i++)
	{
		$arFilter['text_ident'] = $KS_IND_dir[$i];
		$arCategory = $obCategory->GetRecord($arFilter);
		if($arCategory)
		{
			$arFilter['parent_id'] = $arCategory['id'];
			$sUrl .= $arCategory['text_ident'] . "/";
			if ($module_config['show_nav_chain'] == "1")
			{
				if ($this->IsActive("navigation"))
					CNNavChain::get_instance()->Add( $arCategory['title'],$sUrl);
			}

			/* Проверка прав доступа */
			if ($access_level > 8)
				if(!in_array($arCategory['access_view'], $arUserGroups))
					throw new CAccessError("CATSUBCAT_NOT_ACCESS_SECTION");
			$module_parameters['parent_id'] = $arCategory['id'];
		}
		else
		{
			throw new CHTTPError("SYSTEM_SECTION_NOT_FOUND", 404);
		}
	}
}
/* Определение виджета для подключения в качестве контента страницы */
if (strlen($KS_IND_matches[2]) > 0 OR ($module_parameters['action'] == "CatElement"))
{
	/* Элемент каталога */
	$module_parameters['text_ident'] = $KS_IND_matches[3];
	$module_parameters['setPageTitle']=$KS_MODULES->GetConfigVar('catsubcat','set_title',1)==1?'Y':'N';
	if(!function_exists('smarty_function_CatElement'))
		include_once("widgets/function.CatElement.php");
	$res = smarty_function_CatElement($module_parameters, $smarty);
}
elseif ($_GET['type'] == "rss")
{
	/* RSS */
	$module_parameters['tpl'] = "RSS";
	$module_parameters['sort_by'] = $KS_MODULES->GetConfigVar($module_name, "sort_by",'id');
	$module_parameters['sort_dir'] = $KS_MODULES->GetConfigVar($module_name, "sort_dir",'asc');
	$module_parameters['announces_count'] = $KS_MODULES->GetConfigVar($module_name, "count",'10');
	$module_parameters['parent_id'] = $module_parameters['parent_id'];
	$module_parameters['select_from_children'] = $KS_MODULES->GetConfigVar($module_name, "select_from_children",'y');

	if (!function_exists("smarty_function_CatAnnounce"))
		include_once("widgets/function.CatAnnounce.php");
	$res = smarty_function_CatAnnounce($module_parameters, $smarty);
	$output['include_global_template'] = 0;
}
else
{
	/* Категория */
	$module_parameters['ID'] = $module_parameters['parent_id'];
	include_once("widgets/function.CatCategory.php");
	$res = smarty_function_CatCategory($module_parameters, $smarty);
}
/* Возвращаем результат работы */
$output['main_content'] = $res;

