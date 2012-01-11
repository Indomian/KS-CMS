<?php
/**
 * В файле содержится класс выполняющий дерева модуля catsubcat
 *
 * @filesource catsubcat/tree.inc.php
 * @author blade39 <blade39@kolosstudio.ru>
 * @since 12.01.2012
 * @version 2.6
 */

/* Обязательно вставляем во все файлы для защиты от взлома */
if (!defined("KS_ENGINE")) die("Hacking attempt!");

include_once MODULES_DIR.'/main/libs/class.CModuleTree.php';
include_once MODULES_DIR.'/catsubcat/libs/class.CCategory.php';
include_once MODULES_DIR.'/catsubcat/libs/class.CElement.php';

class CcatsubcatTree extends CModuleTree
{
	private $obCategory;
	private $obElement;

	function __construct($module='catsubcat',CSiteTree $obSiteTree)
	{
		parent::__construct($module,$obSiteTree);
		$this->obCategory=new CCategory();
		$this->obElement=new CElement();
	}

	function GetRootBrunch()
	{

	}

	function GetBrunch($key='')
	{
		if($arItem=$this->obSiteTree->GetTreeItem($key))
		{

		}
		else
		{
			if($arCategories=$this->obCategory->GetList(array('orderation'=>'asc'),false,false,array('id','title','parent_id','active')))
			{
				foreach($arCategories as $arItem)
				{
					$arHash=array('catsubcat','cat',$arItem['id'],$arItem['parent_id']);
					$arItem['module']=$this->sModule;
					$arRow=array(
						'key'=>$this->obSiteTree->GenHash($arHash),
						'title'=>$arItem['title'],
						'href'=>'',
						'actions'=>'',
						'data'=>$arItem
					);
					if($arItem['id']==0 && $arItem['parent_id']==0)
						$this->obSiteTree->AddTreeLeaf('',$arRow);
					else
						$this->obSiteTree->AddTreeBrunch('',$arRow);
				}
			}
			if($arElements=$this->obElement->GetList(array('orderation'=>'asc'),false,false,array('id','title','parent_id','active')))
			{
				foreach($arElements as $arItem)
				{
					$arHash=array('catsubcat','elm',$arItem['id'],$arItem['parent_id']);
					$arItem['module']=$this->sModule;
					$arRow=array(
						'key'=>$this->obSiteTree->GenHash($arHash),
						'title'=>$arItem['title'],
						'href'=>'',
						'actions'=>'',
						'data'=>$arItem
					);
					$this->obSiteTree->AddTreeLeaf('',$arRow);
				}
			}
		}
	}
}