<?php
/**
 * \file modules/main/libs/class.CSmartyExtender.php
 * Файл обеспечивает расширения для смарти
 * Файл проекта kolos-cms.
 *
 * Создан 26.11.2011
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 2.6
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once(MODULES_DIR  ."/main/libs/smarty/Smarty.class.php");

class CSmartyExtender extends Smarty
{
	/**
	 * Метод позволяет программно заменить значение конфигурационной переменной
	 */
	function SetConfigVar($var,$value)
	{
		$this->_config[0]['vars'][$var]=$value;
	}
}