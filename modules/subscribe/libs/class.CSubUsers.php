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
	 * @todo Посмотреть что делает метод и зачем его назвали  как обычный
	 */
	function Save($uin='',$newsletters='')
	{
		global $ks_db;
		$del=" where uin='".$uin."'";
		if($newsletters)
		foreach($newsletters as $item)
		{
			if(!$res=$this->GetRecord(array('uin'=>$uin,'newsletter'=>$item)))
			{
				$ks_db->query("INSERT INTO ".PREFIX.$this->sTable."(uin,newsletter) VALUES ('$uin','$item')");
			}
			$del.=" AND newsletter!='".$item."'";
		}
		$ks_db->query("DELETE FROM ".PREFIX.$this->sTable.$del);
	}
}