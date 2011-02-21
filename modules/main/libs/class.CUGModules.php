<?php

if(!defined('KS_ENGINE')){  die("Hacking attempt!");}

/**
 * Класс обеспечивает упрощенную выборку данных при редактировании модулей в административном интерфейсе
 */
class CUGModules extends CModuleAdmin
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

