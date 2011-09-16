<?php
/**
 * @file class.CSubscribe.php
 * Файл с классом CSubscribe обеспечивающий операции над подписками
 * Файл проекта kolos-cms.
 *
 * Создан 31.08.2011
 *
 * @author Konstantin Kuznetsov <lopikun@gmail.com>, blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

class CSubscribe extends CObject
{
	private $obUser;
	function __construct($sTable="subscribe_users")
	{
		global $USER;
		parent::__construct($sTable);
		$this->obUser= $USER;
	}

	/**
	 * Метод возвращает список активных пользователей
	 */
	function GetUsers($arOrder=array('title'=>'asc'))
	{
		return $this->obUser->GetList($arOrder,array('active'=>1), false, array('id','title'));
	}

	/**
	 * Метод возвращает email адреса и форматы писем по номеру рассылки
	 */
	function GetEmailByNewsletter($newsletter)
	{
		return $this->GetEmailByNewsletters(array($newsletter));
	}

	/**
	 * Метод возвращает email адреса и форматы писем по номерам рассылки
	 */
	function GetEmailByNewsletters($newsletters)
	{
		if($newsletters)
		{
			$arOrder = array('subscribe_users.id' => 'asc');
			$arFilter = array(
				'subscribe_users.active'=>1,
				'->subscribe_subscribers.newsletter'=>$newsletters,
				'?subscribe_users.id'=>'subscribe_subscribers.uin',
				'?subscribe_users.uin'=>'users.id'
			);
			$arSelect = array('email','users.title','format');
			$arGroupBy=array('subscribe_users.email');
			$users=$this->GetList($arOrder,$arFilter,null,$arSelect,$arGroupBy);
			$arOrder = array('subscribe_users.id' => 'asc');
			$arFilter = array(
				'subscribe_users.active'=>1,
				'->subscribe_subscribers.newsletter'=>$newsletters,
				'?subscribe_users.id'=>'subscribe_subscribers.uin',
				'subscribe_users.uin'=>'-1'
			);
			$arSelect = array('email','format');
			$arGroupBy=array('subscribe_users.email');
			$usersAnonim=$this->GetList($arOrder,$arFilter,null,$arSelect,$arGroupBy);
			if(is_array($users) && is_array($usersAnonim))
				return array_merge($users,$usersAnonim);
			elseif(is_array($users))
				return $users;
			elseif(is_array($usersAnonim))
				return $usersAnonim;
		}
	}

	/**
	 * Возвращает адреса email по номерам групп
	 */
	function GetEmailByGroup($groups)
	{
		$arOrder = array('users.id' => 'asc');
		$arFilter = array(
			'users.active'=>1,
			'->users_grouplinks.group_id'=>$groups,
			'?users_grouplinks.user_id'=>'users.id'
		);
		$arSelect = array('users.email','users.title');
		$arGroupBy=array('users.email');
		return $this->GetList($arOrder,$arFilter,null,$arSelect,$arGroupBy);
	}

	function Save($prefix = "KS_", $data = "")
	{
		if($data=='') $data=$_POST;
		$arFilter=array('email'=>$data[$prefix.'email']);
		if($data[$prefix.'id']) $arFilter['!id']=$data[$prefix.'id'];
		if($this->GetRecord($arFilter))
			throw new CError("SUBSCRIBE_MAIL_ALREADY_EXISTS",KS_ERROR_MAIN_ALREADY_EXISTS,'');
		return parent::Save($prefix, $data);
	}
}