<?php
/**
 * \file class.CGB2Answers.php
 * Класс для работы с ответами гостевой книги 2.0
 * Файл проекта kolos-cms.
 * 
 * Создан 07.12.2009
 *
 * \author blade39
 * \version 2.5.3
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

class CGB2Answers extends CFieldsObject
{
	function __construct($sTable='gb2_answers')
	{
		parent::__construct($sTable);
		$this->arFields=array('id','active','post_id','title','content','date','user_id');
		$this->sFieldsModule='guestbook2';
		//Подключаем работу с пользовательскими полями.
		if (class_exists(CFields))
		{
			$this->bFields=true;
			$obFields=new CFields();
			$this->arUserFields=$obFields->GetFields($this->sFieldsModule,$this->sTable);
			foreach($this->arUserFields as $item)
			{
				$this->arFields[]='ext_'.$item['title'];
			}
		}
		//Устанавливаем папку для загрузки
		$this->sUploadPath='guestbook2/';
	}
}
?>
