<?php
/**
 * Файл виджета dummy. Виджет ничего не делает
 *
 * @file dummy/widgets/function.dummy.php
 * @author BlaDe39 <blade39@kolosstudio.ru>
 *
 * @version 2.6
 * @since 31.08.2011
 *
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

function smarty_function_dummy($params, &$smarty)
{
	global $USER,$KS_MODULES;

	$access_level=$USER->GetLevel('dummy');
	if($access_level>8) throw new CAccessError("SYSTEM_NOT_ACCESS_MODULE");

	return $KS_MODULES->RenderTemplate($smarty,'/dummy/dummy',$params['global_template'],$params['tpl']);
}

function widget_params_dummy()
{
	return array('fields' => array());
}

