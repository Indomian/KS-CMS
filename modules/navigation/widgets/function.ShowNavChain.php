<?php

/**
 * Виджет, отвечает за вывод навигационной цепочки. Доступные параметры:
 * global_template - глобальный шаблон
 * tpl - шаблон вывода виджета
 * delimiter - символ разделения элементов 
 */

function smarty_function_ShowNavChain($params, &$smarty) 
{
	global $global_template,$KS_MODULES;
	$arNC=array(
		'arr'=>CNNavChain::get_instance()->NC,
		'delimiter'=>$params['delimiter']
	);
	$smarty->assign('NC_arr',$arNC);
	$smarty->assign("items", CNNavChain::get_instance()->NC);
	/* Поиск шаблона для виджета и возвращение результата */
	$sResult=$KS_MODULES->RenderTemplate($smarty,'/navigation/ShowNavChain',$params['global_template'],$params['tpl']);
	return $sResult;
}

function widget_params_ShowNavChain()
{
	$arFields=array(
		'delimiter'=>array(
		  'title'=>'Разделитель пунктов',
		  'type'=>'text',
		  'value'=>'&nbsp;/&nbsp;',
		)
	);
	return array(
		'fields'=>$arFields,
	);
}

?>