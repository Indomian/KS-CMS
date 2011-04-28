<?php
/**
 * \file function.rotBanner.php
 * Виджет выполняет ротацию баннеров
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
 * Функция производит ротацию баннеров одного типа
 */
function smarty_function_rotBanner($params,&$subsmarty)
{
	global $global_template,$KS_MODULES,$KS_URL,$ks_db;
	$ks_db->add2log('<b>banner begin</b>');
	$params['duration']=intval($params['duration'])>0?intval($params['duration']):3;
	$params['count']=intval($params['count'])>0?intval($params['count']):3;
	$obBanner=CBannersApi::get_instance();
	if($arType=$obBanner->obBannerTypes->GetRecord(array('text_ident'=>$params['btype'],'active'=>1)))
	{
		$arFilter=array(
			'active'=>1,
			'type_id'=>$arType['id'],
			'AND'=>array(
					array('OR'=>array('>=active_to'=>time(),'active_to'=>0)),
					array('OR'=>array('<=active_from'=>time(),'active_from'=>0)),
				)
		);
		$arSort=array(
			'rand()'=>'asc',
		);
		/*$arList=$obBanner->obBanners->GetList($arSort,$arFilter,$params['count']);
		$arBanners=array();
		foreach($arList as $arRow)
		{
			if($arRow['record_views']==1) $this->AddView($arRow['id']);
			$arBanners[]=$arRow;
		}*/
		if($_GET['type']!='AJAX')
		{
			$_SESSION['banner_views'][$arType['text_ident']]=array();
		}
		if($arBanners=$obBanner->SelectBanner($arType['text_ident'],$params['count']))
		{
			
			$subsmarty->assign('type',$arType);
			$subsmarty->assign('list',$arBanners);
			if($obBanner->totalThisBanners>$params['count'])
			{
				$subsmarty->assign('duration',$params['duration']);
			}
			else
			{
				$subsmarty->assign('duration',0);
			}
			$subsmarty->assign('currentPath',$KS_URL->GetPath());
			$subsmarty->assign('count',$params['count']);
			//Код для генерации пути к шаблону или вывод ошибки об отсутсвтии шаблона
			$sResult=$KS_MODULES->RenderTemplate($subsmarty,'/banners/rotBanner',$params['global_template'],$params['tpl']);
			$ks_db->add2log('<b>banner end</b>');
			return $sResult;
		}
		return '';
	}
	else
	{
		return '';
	}
}

function widget_params_rotBanner()
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
		'btype'=>array(
			'title'=>'Баннеропозиция',
			'type'=>'select',
			'value'=>$arBT,
		),
		'count'=>array(
			'title'=>'Количество',
			'type'=>'text',
			'value'=>'3'
		),
		'duration'=>array(
			'title'=>'Задержка, с',
			'type'=>'text',
			'value'=>'3',
		),
	);
	return array(
		'fields'=>$arFields,
	);
}
?>