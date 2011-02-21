<?php
/**
 * \file init.inc.php
 * Файлы init.inc.php организуют подключение необходимых библиотек без исполнения модуля
 * Файл проекта kolos-cms.
 *
 * Создан 06.06.2009
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 1.0
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once (MODULES_DIR.'/interfaces/libs/class.CPageNavigation.php');
?>
