<?php
/**
 * Файл содержит общий классы для работы с группами пользователей
 *
 * @since 05.11.2008
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
if(!defined('KS_ENGINE')){  die("Hacking attempt!");}
include_once MODULES_DIR.'/main/libs/class.CUsersCommon.php';

/**
 * Класс CUserGroup - управление настройками групп пользователей
 */
class CUserGroup extends CUsersCommon
{
	function __construct($sTable="usergroups",$sUploadPath='/users',$sModule='user')
	{
		parent::__construct($sTable,$sUploadPath,$sModule);
	}

	/**
	 * Метод обеспечивает удаление групп пользователей, при этом также удаляются связные данные
	 * @param $arFilter
	 */
	function DeleteItems(array $arFilter)
	{
		if($arGroups=$this->GetList(array('id'=>'asc'),$arFilter))
		{
			$this->obLinks->DeleteItems(array('->group_id'=>array_keys($arGroups)));
			return parent::DeleteItems($arFilter);
		}
		return false;
	}
}
