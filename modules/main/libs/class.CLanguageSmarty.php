<?php
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/main/libs/class.CLanguage.php';
/**
 * Класс для поддержки многоязыковых интерфейсов на базе констант смарти
 */
class CLanguageSmarty extends CLanguage
{
	private $obSmarty;
	private $sFilename;

	function __construct($smarty,$filename)
	{
		$this->obSmarty=$smarty;
		$this->sFilename=$filename;
	}

	/**
	 * Метод подгружает указанную секцию в память
	 */
	function LoadSection($section='')
	{
		$this->obSmarty->config_load($this->sFilename,$section);
	}

	function Text($code)
	{
		$sResult=$this->obSmarty->get_config_vars($code);
		if($sResult!='')
		{
			return $sResult;
		}
		return $code;
	}
}

