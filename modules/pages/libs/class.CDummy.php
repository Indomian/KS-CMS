<?php
/**
 * @file class.CDummy.php
 * Файл с классом CDummy который ничего не делает, а просто наследует от CObject
 * Файл проекта kolos-cms.
 *
 * Создан 31.08.2011
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

class CDummy extends CObject
{
	function __construct($tables = 'dummy')
	{
		parent::__construct($tables);
	}
}
