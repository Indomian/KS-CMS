<?php
/**************************************************************
/	KS ENGINE
/	(c) 2008 ALL RIGHTS RESERVED
/**************************************************************
/**************************************************************
/	Author: BlaDe39 <blade39@kolosstudio.ru>
/	http://kolos-studio.ru/
/	http://dotj.ru/
/**************************************************************
/**************************************************************
/	Назначение: работа с блогами
/	Версия:	0.1
/	Последняя модификация: 25.03.2008
/**************************************************************
*/
/*
ИНФОРМАЦИЯ ПО ОТДАВАЕМОЙ ИНФОРМАЦИИ
модуль должен отдать переменную $output, в общем случае, с отформатированными данными
*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}
//Задаем название модуля
$module='banners';
//Определяем глобальные переменные которые могут понадобиться внутри модуля
global $smarty;
//Получаем переменные прав на доступ к модулю и разделам модуля
$smarty->plugins_dir[] = MODULES_DIR.'/'.$module.'/widgets/';
if($module_parameters['is_widget']==1)
{
	if(file_exists(MODULES_DIR.'/'.$module.'/widgets/function.'.$module_parameters['action'].'.php'))
	{
		include_once(MODULES_DIR.'/'.$module.'/widgets/function.'.$module_parameters['action'].'.php');
		$res=call_user_func('smarty_function_'.$module_parameters['action'],$module_parameters,$smarty);
	}
	else
	{
		throw new CError('MAIN_WIDGET_NOT_REGISTERED');
	}
	$output['main_content']=$res;
	return $output['main_content'];
}
else
{
	if($_GET['go']!='')
	{
		require_once MODULES_DIR.'/banners/libs/class.CBannersApi.php';
		$obBanner=CBannersAPI::get_instance();
		$arBanner=$obBanner->obBanners->GetRecord(array('href'=>$_GET['go']));
		if(is_array($arBanner))
		{
			$obBanner->AddHit($arBanner['id']);
			$KS_URL->redirect($arBanner['href']);
		}
	}
	header('HTTP/1.0 404 Not found',true,404);
	throw new CError('SYSTEM_FILE_NOT_FOUND',404);
}
?>