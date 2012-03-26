<?php
/**
 * Управление настройками модулей, общий базовый класс
 * @author BlaDe39 <blade39@kolosstudio.ru>
 * @since 28.11.2011
 */
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CModuleAdmin.php';
require_once MODULES_DIR.'/main/libs/class.CUserGroup.php';
require_once MODULES_DIR.'/main/libs/class.CModulesAccess.php';
require_once MODULES_DIR.'/main/libs/class.CConfigParser.php';

class CModuleOptions extends CModuleAdmin
{
	protected $obUserGroup;
	protected $obAccess;
	protected $obConfig;
	
	function __construct($module, &$smarty, &$parent)
	{
		parent::__construct($module,$smarty,$parent);
		$this->sAction=(!empty($_REQUEST['action']))?strip_tags($_REQUEST['action']):false;
		$this->obUserGroup = new CUserGroup();
		$this->obAccess=new CModulesAccess();
		$this->obConfig=new CConfigParser($this->module);
		$this->obConfig->LoadConfig();
	}

	function GetAccessLevels()
	{
		$arAccess=array();
		$arAccess['groups'] = $this->obUserGroup->GetList(array('title'=>'asc'));
		$arAccess['module'] = $this->obModules->GetAccessArray($this->module);

		$arAccess['levels'] = $this->obAccess->GetList(array('id'=>'asc'),array('module'=>$this->module));
		unset($arAccess['levels'][$this->module]);

		$arRes = array();
		foreach($arAccess['levels'] as $key => $item)
			$arRes[$item['group_id']] = $item;
		foreach($arAccess['groups'] as $arGroup)
			if(!array_key_exists($arGroup['id'],$arRes))
				$arRes[$arGroup['id']]=array(
					'id'=>'-1',
					'group_id'=>$arGroup['id'],
					'module'=>$this->module,
					'level'=>10,
				);
		$arAccess['levels'] = $arRes;
		return $arAccess;
	}

	function SaveAccessLevels()
	{
		if(is_array($_POST['sc_groupLevel']))
			foreach($_POST['sc_groupLevel'] as $key=>$value)
				$this->obAccess->Set($key, $this->module, min($value));
	}
}
