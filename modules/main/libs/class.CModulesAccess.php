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
			$this->Update($res['id'],array('level'=>intval($level)));
		else
			$this->Save('',array('group_id'=>intval($group_id),'module'=>$module,'level'=>intval($level)));
	}

	/**
	 * @todo Разобраться зачем здесь нужна нестандартная функция, понять что она собственно делает.
	 */
	function GetList($arOrder=false,$arFilter=false,$arLimit=false,$arSelect=false,$arGroupBy=false)
	{
		$sWhere=$this->_GenWhere($arFilter);
		$obResult=$this->obDB->Query("SELECT * FROM ".$this->_GenFrom().$sWhere);
		if($obResult->NumRows()>0)
		{
			$i=0;
			$arResult=array();
			while($arRow=$obResult->GetRow())
			{
				$arResult[$arRow['module']]=$arRow;
				$arResult[$i++]=$arRow;
			}
			return $arResult;
		}
		return false;
	}
}


