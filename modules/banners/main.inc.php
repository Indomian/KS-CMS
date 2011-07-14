<?php
/**
 * Главный файл модуля баннеры
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 * @since 06.05.2011
 */

if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

//Задаем название модуля
$module='banners';

if(isset($_GET['go']) && $_GET['go']!='')
{
	require_once MODULES_DIR.'/banners/libs/class.CBannersApi.php';
	$obBanner=CBannersAPI::get_instance();
	$arFilter=array('href'=>$_GET['go']);
	if(isset($_GET['id'])) $arFilter['id']=intval($_GET['id']);
	if($arBanner=$obBanner->Banner()->GetRecord($arFilter))
	{
		if($arBanner['save_stats']==1)
			$obBanner->AddHit($arBanner['id']);
		$KS_URL->redirect($arBanner['href']);
	}
}
throw new CHttpError('SYSTEM_FILE_NOT_FOUND',404);
