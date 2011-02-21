<?php
/**
 * \file self.simple_expanding_menu.php
 * Файл для вывода простого меню c вложенными элементами
 * Файл проекта CMS-local.
 * 
 * Создан 25.02.2009
 *
 * \author north-e <pushkov@kolosstudio.ru>
 * \version 0.1
 * \todo
 * 
 * @var $menu_params - входной массив данных о типе навигации (меню),
 * структура: id 	text_ident 	name 	description 	script_name 	active;
 */
 
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/navigation/libs/class.CNav.php';
$obMenu = new CNavElement();
$output = $obMenu->GetMenuList($menu_params['id'], 1);

?>