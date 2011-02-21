<?php
/**
 * @todo Разобраться зачем вообще нужен этот файл
 */
/**
 * @file catsubcat/pages/deletefromframe.php
 * Файл вызова шаблона при удалении из фрэйма
 * Файл проекта kolos-cms.
 *
 * Изменен 14.01.2011
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CModuleAdmin.php';

class CcatsubcatAIdeletefromframe extends CModuleAdmin
{
	function __construct($module='catsubcat',&$smarty,&$parent)
	{
		parent::__construct($module,$smarty,$parent);
	}

	function Run()
	{
		return '_deletefromframe';
	}
}

