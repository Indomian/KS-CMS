<?php
/**
 * \file statistics.php
 * Файл инициализации обработчиков событий записи статистики
 * Файл проекта kolos-cms.
 * 
 * Создан 25.09.2009
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 1.0
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once (MODULES_DIR.'/statistics/libs/class.CStatistics.php');
?>
