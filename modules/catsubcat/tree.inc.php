<?php
/**
 * В файле содержится класс выполняющий построение дерева модуля catsubcat
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
		$this->sIcon='/icons_tree/catsubcat.gif';
	}

	function GetBrunch($key='')
	{
		$arFilter=array('parent_id'=>0);
		if($arItem=$this->obSiteTree->GetTreeItem($key))
			if(array_key_exists('id',$arItem['data']))
				$arFilter=array('parent_id'=>$arItem['data']['id']);
		if($arCategories=$this->obCategory->GetList(array('orderation'=>'asc'),$arFilter,false,array('id','title','parent_id','active')))
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
				{
					$arRow['icon']='/icons_tree/page.gif';
					$this->obSiteTree->AddTreeLeaf($key,$arRow);
				}
				else
				{
					$arRow['icon']='/icons_tree/folder.gif';
					$this->obSiteTree->AddTreeBrunch($key,$arRow);
				}
			}
		if($arElements=$this->obElement->GetList(array('orderation'=>'asc'),$arFilter,false,array('id','title','parent_id','active')))
			foreach($arElements as $arItem)
			{
				$arHash=array('catsubcat','elm',$arItem['id'],$arItem['parent_id']);
				$arItem['module']=$this->sModule;
				$arRow=array(
					'key'=>$this->obSiteTree->GenHash($arHash),
					'title'=>$arItem['title'],
					'icon'=>'/icons_tree/page.gif',
					'href'=>'',
					'actions'=>'',
					'data'=>$arItem
				);
				$this->obSiteTree->AddTreeLeaf($key,$arRow);
			}
	}
}