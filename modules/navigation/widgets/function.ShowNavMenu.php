<?php

/**
 * Виджет производит вывод указанного меню, используются шаблоны и тип меню.
 *
 * Последняя модификация: 27.02.2009
 *
 * Доступные параметры:
 * type - тип меню;
 * tpl - шаблон виджета;
 * global_template - глобальный шаблон;
 * KS_IND_matches - массив с элементами URL, используется для определения id пункта меню текущей страницы
 */

include_once MODULES_DIR.'/navigation/libs/class.CNav.php';

function smarty_function_ShowNavMenu($params, &$smarty)
{
	global $global_template, $KS_IND_matches,$KS_MODULES;
	try
	{
		$menuItem = new CNavTypes;
		$menu_arr = $menuItem->GetMenu($params['type']);		// получаем полный список элементов меню
		$arMenuType=$menuItem->GetLastMenuType();
		$menuElement = new CNavElement;
		if ($current_page = $menuElement->GetCurrentPageMenuIds($KS_IND_matches[0],$arMenuType['id']))
			$smarty->assign("current_page", $current_page);		// устанавливаем массив с id выбранного элемента меню
		$smarty->assign('menuType',$menuItem->GetLastMenuType());
		$smarty->assign("menu", $menu_arr);
		//Код для генерации пути к шаблону или вывод ошибки об отсутсвтии шаблона
		/* Поиск шаблона для виджета и возвращение результата */
		$sResult=$KS_MODULES->RenderTemplate($smarty,'/navigation/ShowNavMenu',$params['global_template'],$params['tpl']);
		return $sResult;
	}
	catch (CError $e)
	{
		return $e;
	}
}

function widget_params_ShowNavMenu()
{
	/* Получаем список типов меню */
	$typeList = array();
	$obTypes = new CNavTypes();
	$res = $obTypes->GetList();
	foreach ($res as $item)
		$typeList[$item['text_ident']] = $item['name'];

	/* Массив, определяющий форму настройки виджета */
	$arFields = array
	(
		'type' => array
		(
			'title' => 'Тип меню',
			'type' => 'select',
			'value' => $typeList,
		)
	);

	return array
	(
		'fields' => $arFields,
	);
}

?>