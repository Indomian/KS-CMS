<?php
/**
 * \file function.sitemap.php
 * Виджет выводит карту сайта по настройкам модуля
 * Файл проекта CMS-local.
 *
 * Создан 24.11.2008
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 0.1
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/sitemap/libs/class.CSitemap.php';
include_once MODULES_DIR.'/main/libs/class.CPHPCache.php';

/**
 * Функция производит вывод комментариев для определенного модуля.
 * \param $params массив параметров.
 * \param $subsmarty - указатель на объект смарти.
 * Параметры могут быть следующими:
 * 	themeId - номер темы в которой хранятся комментарии
 * 	count - количество выводимых элементов
 */
function smarty_function_sitemap($params,&$subsmarty)
{
	global $USER,$ks_db,$KS_MODULES,$KS_URL;
	$cacheId='sitemap'.join('_',$USER->GetGroups());
	$obCache=new CPHPCache($cacheId,$KS_MODULES->GetConfigVar('sitemap','cacheTime'),'sitemap');
	if(!$obCache->Alive())
	{
		$arTree=array();
		$obCache->SaveToCache($arTree);
	}
	else
	{
		$arTree=$obCache->GetData();
	}
	$subsmarty->assign('data',$arTree);
	//Код для генерации пути к шаблону или вывод ошибки об отсутсвтии шаблона
	return $KS_MODULES->RenderTemplate($subsmarty,'/sitemap/sitemap',$params['global_template'],$params['tpl']);
}

/**
 * Функция для настройки параметров виджета
 */
function widget_params_sitemap()
{
	$arFields = array
	(
	);

	return array
	(
		'fields' => $arFields
	);
}
?>