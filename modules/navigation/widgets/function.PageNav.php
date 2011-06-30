<?php
/**
 * \file function.PageNav.php
 * Файл для работы с постраничной навигацией
 * Файл проекта kolos-cms.
 *
 * Создан 18.05.2009
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 1.0
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

function smarty_function_PageNav($params,&$smarty)
{
	global $KS_MODULES;
	try
	{
		//Разбираем параметры
		$arParams['pages']=$params['pages'];
		if(!is_array($arParams['pages'])) return '';
		$data=$arParams['pages'];
		if($data['num']==0)
		{
			$data['num']=1;
		}
		$smarty->assign('params',$params);
		$smarty->assign('data', $data);
    	//Код для генерации пути к шаблону или вывод ошибки об отсутсвтии шаблона
    	/* Поиск шаблона для виджета и возвращение результата */
		return $KS_MODULES->RenderTemplate($smarty,'/navigation/PageNav',$params['global_template'],$params['tpl']);
	}
	catch (CError $e)
	{
		return $e;
	}
}

function widget_params_PageNav($params)
{
	return array('fields'=>array(
		'pages'=>array(
			'title'=>'Массив страниц (обычно генерируется родительским виджетом)',
			'type'=>'text',
			'value'=>'$pages'
		)));
}
?>
