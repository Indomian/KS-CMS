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
	protected $sIcon;

	function __construct($module,CSiteTree $obSiteTree)
	{
		$this->obSiteTree=$obSiteTree;
		$this->sModule=$module;
		$this->sIcon='/icons_tree/folder.gif';
	}

	function GetRootPath()
	{
		return $this->obSiteTree->Modules()->GetSitePath($this->sModule);
	}

	function GetRootBrunch()
	{
		$arHash=array($this->sModule);
		$arRow=array(
			'key'=>$this->obSiteTree->GenHash($arHash),
			'title'=>$this->obSiteTree->Modules()->GetTitle($this->sModule),
			'icon'=>$this->sIcon,
			'href'=>$this->obSiteTree->Modules()->GetSitePath($this->sModule),
			'actions'=>'',
			'date_change'=>0,
			'data'=>array(
				'module'=>$this->sModule
			)
		);
		$this->obSiteTree->AddTreeBrunch('',$arRow);
	}

	function GetBrunch($key='')
	{

	}
}
