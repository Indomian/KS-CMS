<?php
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

/**
 * Класс для поддержки многоязыковых интерфейсов
 */

abstract class CLanguage
{
	/**
	 * Метод возвращает значение текста по его коду
	 */
	function Text($code)
	{
		return $code;
	}

	/**
	 * Метод выполняет загрузку очередной части перевода в область переводов
	 */
	abstract function LoadSection($section='');
}
