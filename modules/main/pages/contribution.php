<?php
/**
 * @file main/pages/contribution.php
 * Файл выводит список использованных технологий
 * Файл проекта kolos-cms.
 *
 * Изменен 16.03.2011
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CModuleAdmin.php';

class CmainAIcontribution extends CModuleAdmin
{
	function Run()
	{
		return '_contribution';
	}
}
