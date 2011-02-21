<?php
/**
 * \file class.CBBParser.php
 * Файл выполняющий парсинг ББ кодов
 * Файл проекта kolos-cms.
 * 
 * Создан 08.06.2009
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 1.0
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/interfaces/libs/class.CTextParser.php';
include_once MODULES_DIR.'/interfaces/libs/simple_bb_code.php';

class CBBParser extends CTextParser
{
	var $obBBCode;
	
	function __construct()
	{
		$this->obBBCode=new Simple_BB_Code();
	}
	/**
	 * Метод выполняет обработку текста в ББ коде и преобразует его в
	 * html
	 * @param string $text текст который необходимо преобразовать
	 */
	function Parse($text)
	{
		return $this->obBBCode->parse($text);
	}
	
	function Convert($text)
	{}
}
?>
