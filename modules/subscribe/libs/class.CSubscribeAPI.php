<?php
/**
 * @file subscribe/libs/class.CSubscribeAPI.php
 * Файл содержит в себе класс АПИ модуля subscribe
 * Файл проекта kolos-cms.
 *
 * @since 23.01.2011
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/main/libs/class.CBaseAPI.php';

/**
 * Класс обеспечивает высокоуровневые функции для модуля wave
 */
class CSubscribeAPI extends CBaseAPI
{
	static private $obInstance;
	private $obSubscribe;
	private $obSubUsers;
	private $obNewsletter;
	private $obReleases;
	private $obSubsUsergroupsLevels;

	/**
	 * Метод заменяющий конструктор. Используется для инициализации.
	 */
	private function init()
	{
		$this->obSubscribe=false;
		$this->obSubUsers=false;
		$this->obNewsletter=false;
		$this->obReleases=false;
		$this->obSubsUsergroupsLevels=false;
	}

	/**
	 * This implements the 'singleton' design pattern
   	 *
     * @return object CMain The one and only instance
     */
  	static function get_instance()
  	{
	    if (!self::$obInstance)
	    {
    		self::$obInstance = new CSubscribeAPI();
      		self::$obInstance->init();  // init AFTER object was linked with self::$instance
    	}
	    return self::$obInstance;
  	}

	/**
	 * Метод возвращает объект управления подписками пользователей
	 */
	function SubscribeUsers()
	{
		if(!$this->obSubscribe)
			$this->obSubscribe=new CObject('subscribe_users');
		return $this->obSubscribe;
	}

	/**
	 * Метод возвращает объект управления подписками пользователей
	 */
	function Subscribers()
	{
		if(!$this->obSubUsers)
			$this->obSubUsers=new CSubUsers();
		return $this->obSubUsers;
	}

	/**
	 * Метод возвращает объект управления темами подписок
	 */
	function Newsletter()
	{
		if(!$this->obNewsletter)
			$this->obNewsletter=new CObject('subscribe_newsletters');
		return $this->obNewsletter;
	}

	/**
	 * Метод возвращает объект управления выпусками рассылок
	 */
	function Release()
	{
		if(!$this->obReleases)
			$this->obReleases=new CReleases();
		return $this->obReleases;
	}

	/**
	 * Метод возвращает объект управления уровнями доступа к рассылкам
	 */
	function Access()
	{
		if(!$this->obSubsUsergroupsLevels)
			$this->obSubsUsergroupsLevels= new CObject('subscribe_usergroups_levels');
		return $this->obSubsUsergroupsLevels;
	}
	
	/**
	 * Метод возвращает список активных пользователей
	 */
	function GetUsers($arOrder=array('title'=>'asc'))
	{
		return $this->obUser->GetList($arOrder,array('active'=>1), false, array('id','title'));
	}

	/**
	 * Метод возвращает все активные подписки
	 */
	function GetNewslettersList()
	{
		return $this->Newsletter()->GetList(false,array('active'=>1));
	}

	/**
	 * Метод возвращает email адреса и форматы писем по номеру рассылки
	 * @param $iNewsletterId - номер рассылки
	 */
	function GetEmailByNewsletter($iNewsletterId)
	{
		return $this->GetEmailByNewsletters(array($iNewsletterId));
	}

	/**
	 * Метод возвращает email адреса и форматы писем по номерам рассылки
	 * @param $arNewslettersIds - массив номеров рассылок
	 */
	function GetEmailByNewsletters(Array $arNewslettersIds)
	{
		$arOrder = array('subscribe_users.id' => 'asc');
		$arFilter = array(
			'subscribe_users.active'=>1,
			'->subscribe_subscribers.newsletter'=>$arNewslettersIds,
			'?subscribe_users.id'=>'subscribe_subscribers.uin',
			'?subscribe_users.uin'=>'users.id'
		);
		$arSelect = array('email','users.title','format');
		$arGroupBy=array('subscribe_users.email');
		$users=$this->SubscribeUsers()->GetList($arOrder,$arFilter,null,$arSelect,$arGroupBy);
		$arOrder = array('subscribe_users.id' => 'asc');
		$arFilter = array(
			'subscribe_users.active'=>1,
			'->subscribe_subscribers.newsletter'=>$arNewslettersIds,
			'?subscribe_users.id'=>'subscribe_subscribers.uin',
			'subscribe_users.uin'=>'-1'
		);
		$arSelect = array('email','format');
		$arGroupBy=array('subscribe_users.email');
		$usersAnonim=$this->SubscribeUsers()->GetList($arOrder,$arFilter,null,$arSelect,$arGroupBy);
		if(is_array($users) && is_array($usersAnonim))
			return array_merge($users,$usersAnonim);
		elseif(is_array($users))
			return $users;
		elseif(is_array($usersAnonim))
			return $usersAnonim;
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
		return $this->SubscribeUsers()->GetList($arOrder,$arFilter,null,$arSelect,$arGroupBy);
	}

	/**
	 * Метод выполняет сохранение подписки в БД
	 */
	function SaveSubscribe($prefix='SB_',$arData=false)
	{
		if(!$arData)
			$arData=$_POST;
		$arFilter=array('email'=>$arData[$prefix.'email']);
		if(isset($arData[$prefix.'id']))
			$arFilter['!id']=$arData[$prefix.'id'];
		if($this->SubscribeUsers()->GetRecord($arFilter))
			throw new CError("SUBSCRIBE_MAIL_ALREADY_EXISTS",KS_ERROR_MAIN_ALREADY_EXISTS);
		return $this->SubscribeUsers()->Save($prefix, $arData);
	}
} 
