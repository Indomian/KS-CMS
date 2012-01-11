<?php
/**
 * В файле содержится класс выполняющий запрос и построение дерева модуля
 *
 * @filesource main/libs/class.CModuleTree.php
 * @author blade39 <blade39@kolosstudio.ru>
 * @since 10.01.2012
 * @version 2.6
 */

/* Обязательно вставляем во все файлы для защиты от взлома */
if (!defined("KS_ENGINE")) die("Hacking attempt!");

include_once MODULES_DIR.'/main/libs/class.CSiteTree.php';

class CModuleTree
{
	protected $obSiteTree;
	protected $sModule;

	function __construct($module,CSiteTree $obSiteTree)
	{
		$this->obSiteTree=$obSiteTree;
		$this->sModule=$module;
	}

	function GetRootBrunch()
	{

	}

	function GetBrunch($key='')
	{

	}
}