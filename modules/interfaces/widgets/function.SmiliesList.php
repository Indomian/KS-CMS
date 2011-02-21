<?php
/**
 * \file function.SmiliesList.php
 * Виджет выводящий список смайликов
 * Файл проекта kolos-cms.
 * 
 * Создан 08.06.2009
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/interfaces/libs/class.CSmile.php';

function smarty_function_SmiliesList($params,&$smarty)
{
	global $MODULE_interfaces_db_config,$KS_MODULES;
	include MODULES_DIR.'/interfaces/config.php';
	$obSmile=new CSmile($MODULE_interfaces_db_config['smilies']);
	$arSmilies=$obSmile->GetList();
	$smarty->assign('data',$arSmilies);
	/* Поиск шаблона для виджета и возвращение результата */
	$sResult=$KS_MODULES->RenderTemplate($smarty,'/interfaces/SmiliesList',$params['global_template'],$params['tpl']);
	return $sResult;
}
?>
