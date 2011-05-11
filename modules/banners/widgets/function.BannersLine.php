<?php
/**
 * @file function.BannersLine.php
 * Виджет выполняет вывод линии баннеров
 * Файл проекта CMS-local.
 *
 * Создан 06.05.2011
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/banners/libs/class.CBannersApi.php';

/**
 * Функция производит ротацию баннеров одного типа
 */
function smarty_function_BannersLine($params,&$subsmarty)
{
	global $KS_MODULES,$KS_URL;
	if(isset($params['count']) && intval($params['count'])>0)
		$params['count']=intval($params['count']);
	else
		$params['count']=3;
	if(!isset($params['btype'])) return '';
	if(!IsTextIdent($params['btype'])) throw new CError('BANNERS_TYPE_REQUIRED');

	$obBanner=CBannersApi::get_instance();
	if($arType=$obBanner->Type()->GetRecord(array('text_ident'=>$params['btype'],'active'=>1)))
	{
		if($arBanners=$obBanner->SelectBanner($arType['text_ident'],$params['count']))
		{
			$subsmarty->assign('type',$arType);
			$subsmarty->assign('list',$arBanners);
			$subsmarty->assign('count',$params['count']);
			//Код для генерации пути к шаблону или вывод ошибки об отсутсвтии шаблона
			$sResult=$KS_MODULES->RenderTemplate($subsmarty,'/banners/BannersLine',$params['global_template'],$params['tpl']);
			return $sResult;
		}
	}
	return '';
}
