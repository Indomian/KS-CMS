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
include_once(MODULES_DIR.'/main/libs/class.CFieldsObject.php');
include_once(MODULES_DIR.'/main/libs/interface.BaseAccess.php');

/**
 * Класс для управления правами доступа групп пользователей к модулям
 */

class CModulesAccess extends CFieldsObject implements BaseAccess
{
	function __construct($sTable='usergroups_levels',$sUploadPath='',$sModule=false)
	{
		parent::__construct($sTable,$sUploadPath,$sModule);
	}

	function Set($group_id,$module,$level)
	{
		if($res=$this->GetRecord(array('group_id'=>$group_id,'module'=>$module)))
		{
			$this->Update($res['id'],array('level'=>intval($level)));
		}
		else
		{
			$this->Save('',array('group_id'=>intval($group_id),'module'=>$module,'level'=>intval($level)));
		}
	}

	/**
	 * @todo Разобраться зачем здесь нужна нестандартная функция, понять что она собственно делает.
	 */
	function GetList($arOrder=false,$arFilter=false,$arLimit=false,$arSelect=false,$arGroupBy=false)
	{
		global $ks_db;
		$sWhere=$this->_GenWhere($arFilter);
		if($res=$ks_db->query("SELECT * FROM ".$this->_GenFrom().$sWhere))
		{
			$i=0;
			$arResult=array();
			while($arRow=$ks_db->get_row($res))
			{
				$arResult[$arRow['module']]=$arRow;
				$arResult[$i++]=$arRow;
			}
			return $arResult;
		}
		else
		{
			return false;
		}

	}
}


