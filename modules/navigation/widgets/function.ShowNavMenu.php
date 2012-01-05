<?php
/**
 * Виджет производит вывод указанного меню, используются шаблоны и тип меню.
 *
 * @filesource navigation/widgets/function.ShowNavMenu.php
 * @since 1.0
 * @author BlaDe39 <blade39@kolosstudio.ru>
 * @version 2.7
 *
 * Доступные параметры:
 * type - тип меню;
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

function smarty_function_ShowNavMenu($params, &$smarty)
{
	global $KS_MODULES;
	if(isset($params['type']))
	{
		$menuItem = new CNavTypes;
		$menu_arr = $menuItem->GetMenu($params['type']);		// получаем полный список элементов меню
		$arMenuType=$menuItem->GetLastMenuType();
		$menuElement = new CNavElement;
		if ($current_page = $menuElement->GetCurrentPageMenuIds($KS_MODULES->GetCurrentRequest(),$arMenuType['id']))
			$smarty->assign("current_page", $current_page);		// устанавливаем массив с id выбранного элемента меню
		$smarty->assign('menuType',$menuItem->GetLastMenuType());
		$smarty->assign("menu", $menu_arr);
		return $KS_MODULES->RenderTemplate($smarty,'/navigation/ShowNavMenu',$params['global_template'],$params['tpl']);
	}
	throw new CDataError('NAVIGATION_MENU_TYPE_REQUIRED');
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