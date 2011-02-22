<?php
/**
 * @file admin.init.php
 * Файлы init.inc.php организуют подключение необходимых библиотек без исполнения модуля
 * Файл проекта kolos-cms.
 *
 * Создан 06.12.2010
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.5.4-14
 */
include_once(MODULES_DIR.'/interfaces/libs/class.CTabs.php');
include_once(MODULES_DIR.'/interfaces/libs/class.CAdminTextParser.php');
include_once(MODULES_DIR.'/interfaces/libs/class.CFilterFrame.php');
include_once(MODULES_DIR.'/interfaces/libs/class.CPageNavigation.php');

$KS_TABS=new CTabs();
$KS_TEXTPARSER=new CAdminTextParser();
