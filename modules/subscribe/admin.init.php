<?php
/**
 * Файл инициализации модуля subscribe
 * @file modules/subscribe/admin.init.php
 * @author BlaDe39 <blade39@kolosstudio.ru>
 * @version 2.6
 * @since 16.09.2011
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once(MODULES_DIR . "/" . $arModule['directory'] . "/libs/class.CEmails.php");
require_once(MODULES_DIR . "/" . $arModule['directory'] . "/libs/class.CReleases.php");
require_once(MODULES_DIR . "/" . $arModule['directory'] . "/libs/class.CSubscribeAPI.php");
require_once(MODULES_DIR . "/" . $arModule['directory'] . "/libs/class.CSubUsers.php");
