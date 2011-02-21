<?php

/**
 * Мастер для пошаговой настройки виджета для вставки в шаблон
 * 
 * Работает через Аякс
 * 
 * @author blade39 <blade39@kolosstudio.ru>, north-e <pushkov@kolosstudio.ru>
 * @version 1.0
 * @since 17.11.2008
 */

if (!defined('KS_ENGINE'))
	die("Hacking attempt!");
 
include_once MODULES_DIR.'/main/libs/class.CWidget.php';

/**
 * Функция возвращает шаблон Смарти со списком виджетов для заданного модуля (шаг 2) 
 * 
 * @return string
 */
function wlist()
{
	global $smarty;
	
	/* Модуль, список активных виджетов для которого будет получать */
	$widget_module = $_GET['wmod'];

	$obModule = new CModuleManagment();
	$module_params = $obModule->GetRecord(array("directory" => $widget_module));
	
	/* Устанавливаем переменные для Смарти */
	$data = CWidget::GetList($widget_module);
	$smarty->assign('data', $data);
	$smarty->assign('module', $widget_module);
	$smarty->assign('module_name', $module_params['name']);
	
	/* Возвращаем шаблон со списком виджетов */
	return $smarty->fetch('admin/main_templates_widget_step2.tpl');
}

/* Определяем действие, которое должен выполнить скрипт */
$action = $_GET['action'];

if ($action == 'wlist')
{
	/* Получение списка виджетов */
	$res = wlist();
}
elseif ($action == 'widget')
{
	/* Настройка виджета */
	try
	{
		/* Имя модуля, к которому относится виджет */
		$widget_module = $_GET['wmod'];
		
		/* Имя виджета */
		$widget = $_GET['w'];
		
		/* Значения параметров по умолчанию */
		$extra_params = array();
		if (isset($_POST))
			$extra_params = $_POST;
		
		/* Создание объекта для работы с виджетами */
		$ob = new CWidget($widget_module, $widget, $smarty);
		
		/* Получаем шаблон настроек параметров виджета */
		$res = $ob->GetParamsHTML(false, $extra_params);
	}
	catch (CError $e)
	{
		$smarty->assign('error', $e->error_text);
		$res = wlist();
	}
}
/*elseif($action=='preview')
{
	try
	{
		$ob=new CWidget($_GET['wmod'],$_GET['w'],$smarty);
		$params=$ob->ParseParams($_POST);
		$preview=$ob->Show($params);
		$smarty->assign('preview',$preview);
		$smarty->assign('module',$_GET['wmod']);
		$smarty->assign('widgetname',$_GET['w']);
		$res=$smarty->fetch('admin/main_templates_widget_step4.tpl');
	}
	catch (CError $e)
	{
		$smarty->assign('error',$e->error_text);
		$res=wlist();
	}
}*/
elseif ($action == 'code')
{
	/* Получение кода виджета для вставки в шаблон (шаг последний) */
	try
	{
		$ob = new CWidget($_GET['wmod'], $_GET['w'], $smarty);
		$params = $ob->ParseParams($_POST);
		$preview = $ob->GenSmartyCode($params);
		$smarty->assign('code', $preview);
		$smarty->assign('module', $_GET['wmod']);
		$smarty->assign('widgetname', $_GET['w']);
		$res=$smarty->fetch('admin/main_templates_widget_step5.tpl');
	}
	catch (CError $e)
	{
		$smarty->assign('error', $e->error_text);
		$res=wlist();
	}
}
else
{
	/* Отображение списка активных модулей (шаг 1) */
	$ob = new CModuleManagment();
	$data = $ob->GetList(false, array("active" => "1"));
	//pre_print($data);
	//Добавляем главный модуль читерским способом
	array_push($data,array(
		'directory'=>'main',
		'name'=>'Главный модуль',
	));
	/* Проверка модулей на наличие корректных виджетов */
	foreach ($data as $module_key => $module_item)
	{
		$has_widgets = 0;
		$wfilename = MODULES_DIR . "/" . $module_item["directory"] . "/widgets/.widgets.php";
		if (file_exists($wfilename))
		{
			unset($arWidgets);
			include($wfilename);
			if (is_array($arWidgets) && count($arWidgets))
				foreach ($arWidgets as $arWidget)
					if ($arWidget['has_widget'])
						$has_widgets = 1;
		}
		if (!$has_widgets)
			unset($data[$module_key]);
	}
	$smarty->assign('data', $data);
	$res = $smarty->fetch('admin/main_templates_widget_step1.tpl');
}

echo $res;
die();

?>
