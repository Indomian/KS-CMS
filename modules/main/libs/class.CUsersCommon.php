<?php
/**
 * Файл содержит общие классы для работы с системой доступа к пользователям
 *
 * @since 05.11.2008
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
if(!defined('KS_ENGINE')){  die("Hacking attempt!");}

include_once(MODULES_DIR.'/main/libs/class.CFieldsObject.php');
include_once(MODULES_DIR.'/main/libs/interface.BaseAccess.php');

/**
 * Промежуточный класс выполняющий объединение классов управляющих пользователями и
 * группами пользователей в общий. На данный момент содержит всего 1 общий метод и конструктор.
 * Также выполняет сохранение и обработку пользовательских полей.
 */

class CUsersCommon extends CFieldsObject
{
	/**
	 * @var $obLinks Объект в котором храняться связи системы
	 */
	protected $obLinks;

	function __construct($sTable="usergroups",$sUploadPath='',$sModule=false)
	{
		parent::__construct($sTable,$sUploadPath,$sModule);
		$this->obLinks=new CObject('users_grouplinks');
	}

	/**
	 * Метод вызвается при удалении группы пользователей, при этом все пользователи удаляются из этой группы
	 */
	function OnDeleteUserGroup($iGroupId)
	{
		$this->obLinks->DeleteItems(array('group_id'=>$iGroupId));
	}
}