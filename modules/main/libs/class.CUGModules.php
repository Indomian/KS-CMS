<?php

if(!defined('KS_ENGINE')){  die("Hacking attempt!");}

include_once MODULES_DIR.'/main/libs/class.CAdminModuleManagment.php';

/**
 * Класс обеспечивает упрощенную выборку данных при редактировании модулей в административном интерфейсе
 */
class CUGModules extends CAdminModuleManagment
{
	function __construct()
	{
		parent::__construct();
	}

	protected function _ParseItem(&$item)
	{
		global $KS_MODULES;
		$item['LEVELS']=$KS_MODULES->GetAccessArray($item['directory']);
		return true;
	}
}

