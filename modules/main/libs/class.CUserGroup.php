<?php

if(!defined('KS_ENGINE')){  die("Hacking attempt!");}
include_once MODULES_DIR.'/main/libs/class.CUsersCommon.php';

/**
 * Класс CUserGroup - управление настройками групп пользователей
 */
class CUserGroup extends CUsersCommon
{
	function __construct($sTable="usergroups")
	{
		parent::__construct($sTable);
		$this->fType='cat';
	}

	function DeleteItems($arFilter)
	{
		global $USER,$ks_db,$KS_ERROR;
		try
		{
			$arGroups=$this->GetList(array('id'=>'asc'),$arFilter);
			if(is_array($arGroups) && count($arGroups)>0)
			{
				$arIds=array();
				foreach($arGroups as $arItem)
				{
					$arIds[]=$arItem['id'];
				}
				$query="DELETE FROM ".PREFIX.$USER->sLinksTable." WHERE group_id in (".join(',',$arIds).")";
				$ks_db->query($query);
				$query="DELETE FROM ".PREFIX.$this->sTable." WHERE id in (".join(',',$arIds).")";
				$ks_db->query($query);
			}
		}
		catch (Exception $e)
		{
			throw new CError($e->GetMessage());
		}
		return false;
	}
}
