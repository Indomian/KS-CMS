<?php
/**
 * \file init.inc.php
 * Файл инициализации модуля навигации
 * Файл проекта kolos-cms.
 *
 * Создан 08.09.2009
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 1.0
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

global $smarty, $KS_IND_matches, $KS_MODULES, $KS_IND_dir, $global_template,  $CNMenu;
//Подключаем библиотеки
include_once MODULES_DIR.'/navigation/libs/class.CNav.php';
$smarty->plugins_dir[]=MODULES_DIR.'/navigation/widgets/';
require_once MODULES_DIR.'/navigation/libs/class.CNNavChain.php';

