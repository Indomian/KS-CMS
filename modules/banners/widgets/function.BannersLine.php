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
		if($arBanners=$obBanner->SelectBanners($arType['text_ident'],$params['count']))
		{
			usort($arBanners,'BannersLine_sort');
			$iFirst=0;
			$iMax=0;
			foreach($arBanners as $key=>$arBanner)
			{
				if($arBanner['show_rate']>$iMax)
				{
					$iFirst=$key;
					$iMax=$arBanner['show_rate'];
				}
			}
			$arBanners[$iFirst]=$obBanner->RecountCoeff($arBanners[$iFirst]);
			$arResult=array_slice($arBanners,$iFirst+1);
			$arResult=array_merge($arResult,array_slice($arBanners,0,$iFirst+1));
			$subsmarty->assign('type',$arType);
			$subsmarty->assign('big',$arBanners[$iFirst]);
			$subsmarty->assign('list',$arResult);
			$subsmarty->assign('count',count($arResult));
			//Код для генерации пути к шаблону или вывод ошибки об отсутсвтии шаблона
			$sResult=$KS_MODULES->RenderTemplate($subsmarty,'/banners/BannersLine',$params['global_template'],$params['tpl']);
			return $sResult;
		}
	}
	return '';
}


function BannersLine_sort($a,$b)
{
	return $a['ext_orderation']-$b['ext_orderation'];
}