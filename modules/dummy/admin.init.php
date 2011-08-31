<?php
/**
 * Файл инициализации модуля dummy
 * @file admin.init.php
 * @author BlaDe39 <blade39@kolosstudio.ru>
 * @version 2.6
 * @since 31.08.2011
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once(MODULES_DIR . "/" . $arModule['directory'] . "/libs/class.CDummy.php");
