<?php
/**
 * Файл отвечает за генерацию древовидной структуры лайт-версии сайта
 *
 * @filesource main.php
 * @author blade39 <blade39@kolosstudio.ru>, north-e <pushkov@kolosstudio.ru>
 * @since 07.04.2009
 * @version 1.1
 * @see lite.php
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
		$this->obTree=new CSiteTree();
	}

	function GetLeaf()
	{
		$arResult=array('error'=>'nothing');
		if(isset($_REQUEST['key']) && $_REQUEST['key']!='')
		{
			if($arBrunch=$this->obTree->GetTreeItem($_REQUEST['key']))
			{
				if(array_key_exists('data',$arBrunch) && array_key_exists('module',$arBrunch['data']) && IsTextIdent($arBrunch['data']['module']))
				{
					if($this->obModules->IsModule($arBrunch['data']['module']))
					{
						include_once MODULES_DIR.'/'.$arBrunch['data']['module'].'/tree.inc.php';
						$sClassName='C'.$arBrunch['data']['module'].'Tree';
						$obModuleTree=new $sClassName($arBrunch['data']['module'],$this->obTree);
						$obModuleTree->GetBrunch($_REQUEST['key']);
						$arResult=array(
							'parent'=>$_REQUEST['key'],
							'list'=>$this->obTree->GetTreeBrunch($_REQUEST['key'])
						);
					}
				}
			}

		}
		else
		{
			/* Читаем список активных модулей */
			$this->obTree->Clean();
			if($arModules = $this->obModules->GetList(array("URL_ident"=>'asc'), array("active" => 1)))
			{
				foreach($arModules as $arRow)
				{
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
				}
			}
			$arResult=array(
				'parent'=>'',
				'list'=>$this->obTree->GetTreeBrunch(),
			);
		}
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
