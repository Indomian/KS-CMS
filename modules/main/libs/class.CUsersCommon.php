<?php
/*
 * CMS-remote
 *
 * Created on 05.11.2008
 *
 * Developed by blade39
 *
 */
if(!defined('KS_ENGINE')){  die("Hacking attempt!");}

include_once(MODULES_DIR.'/main/libs/class.CAccess.php');

/**Промежуточный класс выполняющий объединение классов управляющих пользователями и
 * группами пользователей в общий. На данный момент содержит всего 1 общий метод и конструктор.
 * Также выполняет сохранение и обработку пользовательских полей.*/

class CUsersCommon extends CBaseAccess
{
	var $num_rows;
	var $sFieldsModule;
	/**
	 * @var таблица в которой храняться связи групп и пользователей
	 */
	var $sLinksTable;

	function __construct($sTable="usergroups")
	{
		parent::__construct($sTable);
		if($this->sFieldsModule=='') $this->sFieldsModule='users';
		$this->sUploadPath='users/';
		$this->sLinksTable='users_grouplinks';
		//Подгружаем пользовательские поля
		if (class_exists(CFields))
		{
			$this->bFields=true;
			$obFields=new CFields();
			$this->arUserFields=$obFields->GetFields($this->sFieldsModule,$this->sTable);
			foreach($this->arUserFields as $item)
			{
				$this->arFields[]='ext_'.$item['title'];
			}
		}
	}

	function GetNum()
	{
		global $ks_db;
		if($this->num_rows==0)
		{
			$res=$ks_db->query('SELECT count(id) as cnt FROM '.PREFIX.$this->sTable);
			$res=$ks_db->get_row($res);
			$this->num_rows=$res['cnt'];
		}
		return $this->num_rows;
	}

	function Set($group_id,$module,$level)
	{

	}
}

/**
 * Базовый класс для работы с пользователями, выполняет функционал работы с группами
 * списками, уровнями доступа и многим другим. Отсутствуют функции логина, разлогина.
 */
class CBaseUser extends CUsersCommon
{
	var $groups_list;


	/**
	 * Массив со списком групп к которым принадлежит данный пользователь.
	 *
	 * @var array $arInGroups
	 */
	protected $arInGroups;

	protected $arAccessLevels; 		/**<Хранит список уровней доступа к которым был сделан запрос*/

	function __construct($sTable='users')
	{
		parent::__construct($sTable);
	}
	/**
	 * Метод возвращает массив упорядоченных по номеру групп, к которым принадлежит текущий пользователь
	 *
	 * @version 1.1
	 * @since 11.05.2009
	 *
	 * Добавлено получение групп для незалогиненного пользователя
	 *
	 * @param int $user_id id незалогиненного пользователя
	 * @return array
	 */
	function GetGroups($user_id = 0)
	{
		global $ks_db;

		/* Если неоткуда взять id, тогда выходим */
		if ($this->ID()==0 && $user_id == 0)
			return array(0);

		/* Если указан id пользователя, то работаем по нему */
		$possible_user_id = intval($user_id);
		if ($possible_user_id > 0)
			$id = $possible_user_id;
		else
			$id = $this->ID();

		if ($this->arInGroups == false)
		{
			$time = time();
			$query = "SELECT * FROM " . PREFIX . $this->sLinksTable . " " .
					"WHERE user_id='" . $id . "' " .
					"AND ((date_end>='" . $time . "') OR (date_end='0'))" .
					"AND ((date_start<='" . $time . "') OR (date_start='0')) ORDER BY group_id ASC";
			$rGroups = $ks_db->query($query);
			$arResult = array(0);
			while ($arRow = $ks_db->get_row($rGroups))
				$arResult[] = $arRow['group_id'];
			$this->arInGroups = $arResult;
		}
		else
			$arResult=$this->arInGroups;
		return $arResult;
	}

	function ID()
	{
		return $this->userdata['id'];
	}

	function Email()
	{
		return $this->userdata['email'];
	}

	/**
	 * Метод, возвращает полный список групп к которым привязан указанный пользователь.
	 * Обычно используется в системе администрирования.
	 * @param id - номер пользователя
	 */

	function GetAllGroups($id)
	{
		global $ks_db;
		$query="SELECT * FROM ".PREFIX.$this->sLinksTable." " .
				"WHERE user_id='".$id."' ORDER BY group_id ASC";
		$rGroups=$ks_db->query($query);
		$arResult=array();
		while($arRow=$ks_db->get_row($rGroups))
		{
			$arResult[$arRow['group_id']]=$arRow;
		}
		return $arResult;
	}

	/**
	 * Возвращает уровень прав доступа к определенному модулю.
	 * @param $module - текстовый идентификатор модуля.
	 */

	function GetLevel($module)
	{
		$obAccess=new CModulesAccess();
		$arGroups=$this->GetGroups();
		$level=10;
		if(array_key_exists($module,$this->arAccessLevels))
		{
			$res=$this->arAccessLevels[$module];
		}
		elseif($res=$obAccess->GetList(array('group_id'=>'asc'),array('module'=>$module)))
		{
			$this->arAccessLevels[$module]=$res;
		}
		else
		{
			return $level;
		}
		foreach($res as $key=>$value)
		{
			//Проверяем принадлежит ли пользователь найденой группе, если да то смотрим больше ли текущий
			//уровень чем текущий, если больше - считаем его текущим.
			if((in_array($value['group_id'],$arGroups))&&($level>$value['level']))
			{
				$level=$value['level'];
			}
		}
		return $level;
	}

	/**
	 * Метод размещает пользователя в указанных группах, при этом стираются все привязки
	 * пользователя к другим группам
	 */
	function SetAllUserGroups($iUserID,$arGroups)
	{
		if(!is_array($arGroups)) throw new CDataError('SYSTEM_ARRAY_REQUIRED');
		if($arUser=$this->GetRecord(array('id'=>$iUserID)))
		{
			$query="DELETE FROM ".PREFIX.$this->sLinksTable." WHERE user_id='".$arUser['id']."'";
			$this->obDB->query($query);
			$query="INSERT INTO " . PREFIX.$this->sLinksTable." (user_id, group_id, date_start, date_end) VALUES ";
			foreach($arGroups as $group)
			{
				if(is_numeric($group))
				{
					$query.="('".$arUser['id']."','".$group."','0', '0')";
				}
				elseif(is_array($group))
				{
					$query.="('".$arUser['id']."','".$group['id']."','".$group['date_from']."', '".$group['date_to']."')";
				}
			}
			$this->obDB->query($query);
		}
	}
}

