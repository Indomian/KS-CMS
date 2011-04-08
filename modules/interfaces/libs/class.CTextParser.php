<?php
/**
 * \file class.CTextParser.php
 * В файле находится класс для парсинга текста
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

abstract class CTextParser extends CObject
{
	abstract function Parse($text);
	abstract function Convert($text);
}

