<?php
/**
 * @file class.CGBPosts.php
 * Класс для работы с разделами гостевой книги 2.0
 * Файл проекта kolos-cms.
 * 
 * Создан 08.09.2010
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.5.4
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

class CGB2Categories extends CFieldsObject
{
	function __construct($sTable='gb2_categories')
	{
		parent::__construct($sTable);
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
