<?php

if(!defined('KS_ENGINE')){  die("Hacking attempt!");}

include_once MODULES_DIR.'/main/libs/class.CModuleManagment.php';

/**
 * Класс обеспечивает упрощенную выборку данных при редактировании модулей в административном интерфейсе
 */
class CUGModules extends CModuleManagment
{
	protected function _ParseItem(&$item)
	{
		global $KS_MODULES;
		$item['LEVELS']=$KS_MODULES->GetAccessArray($item['directory']);
		return true;
	}
}

