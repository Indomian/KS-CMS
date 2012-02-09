<?php
/**
 * \file class.CSmile.php
 * В файле находится класс выполняющий работу со смайликами
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
/**
 * Класс работает со смайликами и текстом, выполняет преобразование текста
 */
class CSmile extends CTextParser
{
	function __construct($sTable='interfaces_smiles',$sUploadPath='/smiles')
	{
		parent::__construct($sTable,$sUploadPath);
	}

	function Parse($text)
	{
		if($arSmiles=$this->GetList(array('smile'=>'asc')))
		{
			$arCodes=array();
			$arImages=array();
			foreach($arSmiles as $i=>$item)
			{
				$arCodes[]=$item['smile'];
				$arImages[]='<img src="/uploads/'.$item['img'].'">';
			}
			return str_replace($arCodes,$arImages,$text);
		}
		return $text;
	}

	function Convert($text)
	{

	}
}
