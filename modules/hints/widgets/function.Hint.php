<?php
/**
 * @file modules/hints/widgets/function.check.php
 * Виджет выводит подсказку на сайте
 *
 * @since 31.05.2011
 *
 * @author BlaDe39 <blade39@kolosstudio.ru>
 *
 * @version 2.6
 */

/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/hints/libs/class.CHintsAPI.php';

function smarty_function_Hint($params, &$subsmarty)
{
	global $KS_MODULES;
	if(!isset($params['h'])) return '';
	CHintsAPI::get_instance()->Get($params['h']);
	$subsmarty->assign('data',$params['h']);
	return $KS_MODULES->RenderTemplate($subsmarty,'/hints/hint',$params['global_template'],$params['tpl']);
}
