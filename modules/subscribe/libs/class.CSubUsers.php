<?php
/**
 * @file class.CSubUsers.php
 * Файл с классом CSubUsers обеспечивающий операции над подписчиками
 * Файл проекта kolos-cms.
 *
 * Создан 16.09.2011
 *
 * @author Konstantin Kuznetsov <lopikun@gmail.com>, blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

class CSubUsers extends CObject
{
	function __construct($sTable="subscribe_subscribers")
	{
		parent::__construct($sTable);
	}

	/**
	 * Метод выполняет сохранение подписок пользователя
	 * @param $uin - номер пользователя в таблице subscribe_users
	 * @param $arNewsletters array - массив номеров рассылок
	 */
	function SaveEx($uin,array $arNewsletters)
	{
		$arFilter=array('uin'=>$uin);
		$arNewIds=array_flip($arNewsletters);
		if($arTmpList=$this->GetList(false,$arFilter))
		{
			$arList=$arTmpList;
			foreach($arTmpList as $key=>$arItem)
				if(in_array($arItem['newsletter'],$arNewsletters))
				{
					unset($arList[$key]);
					unset($arNewIds[$arItem['newsletter']]);
				}
			if(count($arList)>0)
				$this->DeleteItems(array('->id'=>array_keys($arList)));
		}
		if(count($arNewIds)>0)
			foreach($arNewIds as $id=>$nothing)
				$this->Save('',array('uin'=>$uin,'newsletter'=>$id));
	}
}