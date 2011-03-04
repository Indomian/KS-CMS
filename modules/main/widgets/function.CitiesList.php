<?php

/**
 * Плагин возвращает список городов в указанной стране
 *
 * @author Blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 * @since 03.03.2011
 */

include_once MODULES_DIR.'/main/libs/class.CGeographyAPI.php';

function smarty_function_CitiesList($params, &$smarty)
{
	global $KS_MODULES;
	$obApi=CGeographyAPI::get_instance();
	if($arCountry=$obApi->Country()->GetById($params['country_id']))
	{
		$sSort=in_array($params['sort'],$obApi->City()->GetFields())?$params['sort']:'id';
		$sDir=$params['dir']=='desc'?'asc':'desc';
		$arResult=$obApi->City()->GetList(array($sSort=>$sDir),array('country_id'=>$arCountry['id']));
	}
	if($params['mode']=='json')
	{
		return json_encode($arResult);
	}
	$smarty->assign('list',$arResult);
	return $KS_MODULES->RenderTemplate($smarty,'/main/CitiesList',$params['global_template'],$params['tpl']);
}

