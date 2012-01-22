<?php
/**
 * Файл содержит класс для работы с доступом к модулям
 *
 * @since 19.11.2008
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */

if(!defined('KS_ENGINE')){  die("Hacking attempt!");}
//==================== Блок констант для уровней доступа ==============================
define('KS_ACCESS_FULL',0);
define('KS_ACCESS_DENIED',10);
//==================== Блок инклудов требуемых библиотек ==============================
include_once(MODULES_DIR.'/main/libs/class.CMain.php');
include_once(MODULES_DIR.'/main/libs/interface.BaseAccess.php');

/**
 * Класс для управления правами доступа групп пользователей к модулям
 */

class CModulesAccess extends CObject implements BaseAccess
{
	function __construct($sTable='usergroups_levels')
	{
		parent::__construct($sTable);
	}

	/**
	 * @todo Документировать!
	 * @param $group_id
	 * @param $module
	 * @param $level
	 */
	function Set($group_id,$module,$level)
	{
		if($res=$this->GetRecord(array('group_id'=>$group_id,'module'=>$module)))
			$this->Update($res['id'],array('level'=>intval($level)));
		else
			$this->Save('',array('group_id'=>intval($group_id),'module'=>$module,'level'=>intval($level)));
	}

	/**
	 * Метод выполняет выборку списка модулей со списком уровней доступа
	 */
	function GetListEx($arFilter=false)
	{
		if($arList=$this->GetList(false,$arFilter,false))
		{
			$arResult=array();
			$i=0;
			foreach($arList as $arRow)
			{
				$arResult[$arRow['module']]=$arRow;
				$arResult[$i++]=$arRow;
			}
			return $arResult;
		}
		return false;
	}
}


