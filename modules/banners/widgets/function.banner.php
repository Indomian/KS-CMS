<?php
/**
 * \file function.banner.php
 * Виджет выводит один баннер в соответствие с принятым алгоритмом
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
include_once MODULES_DIR.'/banners/libs/class.CBannersApi.php';
/**
 * Функция производит вывод одного баннера
 */
function smarty_function_banner($params,&$subsmarty)
{
	global $global_template,$KS_MODULES,$KS_URL,$ks_db;
	$ks_db->add2log('<b>banner begin</b>');
	$obBanner=CBannersApi::get_instance();
	$arBanner=array_shift($obBanner->SelectBanner($params['type']));
	$subsmarty->assign('data',$arBanner);
	//Код для генерации пути к шаблону или вывод ошибки об отсутсвтии шаблона
	$sResult=$KS_MODULES->RenderTemplate($subsmarty,'/banners/banner',$params['global_template'],$params['tpl']);
	$ks_db->add2log('<b>banner end</b>');
	return $sResult;
}

function widget_params_banner()
{
	require_once MODULES_DIR.'/banners/libs/class.CBannersApi.php';
	$symbols_to_cut = 20;
	$obBanner=CBannersAPI::get_instance();
	$tmpBanners=$obBanner->obBannerTypes->GetList(array('title'=>'asc'),array('active'=>1));
	$arBT=array();
	foreach ($tmpBanners as $bt)
	{
		if (mb_strlen( $bt["title"], "utf-8") > $symbols_to_cut)
			 $bt["title"] = mb_substr( $bt["title"], 0, $symbols_to_cut, "utf-8") . "...";
		$arBT[$bt["text_ident"]] = $bt['title'];
	}
	$arFields=array(
		'type'=>array(
			'title'=>'Баннеропозиция',
			'type'=>'select',
			'value'=>$arBT,
		),
	);
	return array(
		'fields'=>$arFields,
	);
}
?>