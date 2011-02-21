<?php
/*
 * CMS-local
 * 
 * Created on 19.11.2008
 *
 * Developed by blade39
 * 
 */

if(!defined('KS_ENGINE')){  die("Hacking attempt!");}

//==================== Блок констант для уровней доступа ==============================

define('KS_ACCESS_FULL',0);
define('KS_ACCESS_DENIED',10);

//==================== Блок инклудов требуемых библиотек ==============================
include_once(MODULES_DIR.'/main/libs/class.CFieldsObject.php');  

//==================== Блок классов и исполняемого кода  ==============================
/**
 * Абстрактный класс - корень для работы с системой распределения прав.
 */
 
abstract class CBaseAccess extends CFieldsObject
{
	/**
	 * Метод выполняет запись прав в базу данных.
	 * \param $group_id идентификатор группы
	 * \param $module идентификатор элемента для которого устанавливаются права
	 * \param $leve уровень прав доступа
	 */
	abstract function Set($group_id,$module,$level);
}

/**
 * Класс для управления правами доступа групп пользователей к модулям
 */

class CModulesAccess extends CBaseAccess
{
	function __construct($sTable='usergroups_levels')
	{
		parent::__construct($sTable);
		$this->arFields=array('id','group_id','module','level');
	}
	
	function Set($group_id,$module,$level)
	{
		global $ks_db;
		if($res=$this->GetRecord(array('group_id'=>$group_id,'module'=>$module)))
		{
			$ks_db->query("UPDATE ".PREFIX.$this->sTable." SET level='".intval($level)."' WHERE id='".$res['id']."'");
		}
		else
		{
			$ks_db->query("INSERT INTO ".PREFIX.$this->sTable."(group_id,module,level) VALUES ('$group_id','$module','$level')");
		}
	}
	
	function GetList($arOrder=false,$arFilter=false,$arLimit=false,$arSelect=false,$arGroupBy=false)
	{
		global $ks_db;
		$sWhere=$this->_GenWhere($arFilter);
		if($res=$ks_db->query("SELECT * FROM ".$this->_GenFrom().$sWhere))
		{
			$i=0;
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

?>
