<?php
/**
 * \file class.CDBError.php
 * Файл содержит код класса ошибки БД
 * Файл проекта kolos-cms.
 *
 * Создан 27.11.2009
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 2.6
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/main/libs/class.CError.php';

class CDBError extends CError
{
	private $query;
	/*!Конструктор класса, создает новое исключение.*/
	function __construct($message="",$code=0,$query='')
	{
		parent::__construct($message,intval($code));
		$this->query=$query;
		$this->error=$code;
 		$this->errorTpl='error.tpl';
 		if($this->query)
		{
			// Safify query
			$query = preg_replace("/([0-9a-f]){32}/", "********************************", $query); // Hides all hashes
			$query_str = "$query";
		}
		else
			$query_str="";
		$errorText="Ошибка вызова MySQL: ".$this->getMessage()."\n".
			"Код ошибки: ".$this->error."\n".
			"Текст запроса: ".$query_str."\n".
			"Стэк вызова функций: \n";
		$arTrace=$this->getTrace();
		foreach($arTrace as $item=>$arFunction)
		{
			$errorText.='В файле: '.$arFunction['file']."\n";
			if(array_key_exists('class',$arFunction) && ($arFunction['class']!=''))
				$errorText.=$arFunction['class'].$arFunction['type'].$arFunction['function'].'() - строка '.$arFunction['line'];
			else
				$errorText.=$arFunction['function'].'() - строка '.$arFunction['line'];
			$errorText.="\n".'----------------------------------------------------'."\n";
		}
		if(KS_LOG_DB_ERRORS)
		{
			error_log(date('d.m.Y H:i').' '.$errorText."\n===========================\n",3,ROOT_DIR.'/mysql.log');
			$errorText='';
		}
		$this->message="DB_MYSQL_ERROR";
		$this->code=$this->error;
		$this->errorText='';
	}
}
