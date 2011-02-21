<?php
/**
 * \file self.simple_menu.php
 * Файл для вывода простого одноуровневого меню
 * Файл проекта CMS-local.
 * 
 * Создан 02.12.2008
 * Последняя модификация 27.02.2009
 *
 * \author blade39 <blade39@kolosstudio.ru>
 *         north-e <pushkov@kolosstudio.ru>
 * \version 0.1
 * \todo
 * 
 * @var $r_sel_menu - входной массив данных о типе навигации (меню),
 * структура: id 	text_ident 	name 	description 	script_name 	active;
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/navigation/libs/class.CNav.php';
$obMenu = new CNavElement();
$output = $obMenu->GetMenuList($menu_params['id'], 0);

?>