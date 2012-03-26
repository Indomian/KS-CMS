<?php
/**
 * Файл отвечает за генерацию древовидной структуры лайт-версии сайта
 *
 * @filesource main.php
 * @author blade39 <blade39@kolosstudio.ru>
 * @since 17.01.2012
 * @version 2.6
 */

/* Обязательно вставляем во все файлы для защиты от взлома */
if (!defined("KS_ENGINE")) die("Hacking attempt!");

include_once MODULES_DIR.'/main/libs/class.CModuleAdmin.php';
include_once MODULES_DIR.'/main/libs/class.CSiteTree.php';

class CmainAImain extends CModuleAdmin
{
	private $arTree;
	private $arTreeList;
	private $obTree;

	function __construct($module='main',&$smarty,&$parent)
	{
		parent::__construct($module,$smarty,$parent);
		$this->obTree=new CSiteTree($this->obModules);
	}

	function GetLeaf()
	{
		$arResult=array('error'=>'nothing');
		if(isset($_REQUEST['key']) && $_REQUEST['key']!='')
		{
			$sKey=$_REQUEST['key'];
			if($arBrunch=$this->obTree->GetTreeItem($sKey))
				if(array_key_exists('data',$arBrunch) && array_key_exists('module',$arBrunch['data']) && IsTextIdent($arBrunch['data']['module']))
					if($this->obModules->IsModule($arBrunch['data']['module']))
					{
						include_once MODULES_DIR.'/'.$arBrunch['data']['module'].'/tree.inc.php';
						$sClassName='C'.$arBrunch['data']['module'].'Tree';
						$obModuleTree=new $sClassName($arBrunch['data']['module'],$this->obTree);
						$obModuleTree->GetBrunch($sKey);

					}
			$arTreeBrunch=$this->obTree->GetTreeBrunch($sKey);
			$arResult=array(
				'parent'=>$sKey,
				'list'=>$arTreeBrunch,
				'size'=>count($arTreeBrunch)
			);
		}
		else
		{
			/* Читаем список активных модулей */
			$this->obTree->Clean();
			if($arModules = $this->obModules->GetList(array("URL_ident"=>'asc'), array("active" => 1)))
				foreach($arModules as $arRow)
					if(file_exists(MODULES_DIR.'/'.$arRow['directory'].'/tree.inc.php'))
					{
						include_once MODULES_DIR.'/'.$arRow['directory'].'/tree.inc.php';
						$sClassName='C'.$arRow['directory'].'Tree';
						$obModuleTree=new $sClassName($arRow['directory'],$this->obTree);
						if($arRow['URL_ident']=='default')
							$obModuleTree->GetBrunch();
						else
							$obModuleTree->GetRootBrunch();
					}
			$arTreeBrunch=$this->obTree->GetTreeBrunch();
			$arResult=array(
				'parent'=>'root',
				'list'=>$arTreeBrunch,
				'size'=>count($arTreeBrunch)
			);
		}
		$arResult['debug']=$this->obTree->GetTree();
		$arResult['debug_list']=$this->obTree->GetTreeList();
		$this->obTree->SaveTree();
		echo json_encode($arResult);
		///@todo Убрать!
		die();
	}

	function Run()
	{
		$this->ParseAction();
		$page='';
		switch($this->sAction)
		{
			case 'l':
				$this->GetLeaf();
			break;
			default:
				$page='';
		}
		$this->obModules->UseJavaScript('/main/admintree.js');
		return $page;
	}
}
