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
	private $sRootPath;

	function __construct($module='catsubcat',CSiteTree $obSiteTree)
	{
		parent::__construct($module,$obSiteTree);
		$this->obCategory=new CCategory();
		$this->obElement=new CElement();
		$this->sIcon='/icons_tree/catsubcat.gif';
		$this->sRootPath=$this->GetRootPath();
	}

	/**
	 * Метод генерирует путь к разделу описанному в $arRootItem
	 */
	private function GenPath($arRootItem)
	{
		$sPath='';
		if(isset($arRootItem['data']['text_ident']))
		{
			if(isset($arRootItem['data']['parent']))
				$sPath.=$this->GenPath($arRootItem['data']['parent']);
			$sPath.=$arRootItem['data']['text_ident'].'/';
		}
		return $sPath;
	}
	
	function GetBrunch($key='')
	{
		$arFilter=array('parent_id'=>0,'active'=>1);
		if($arRootItem=$this->obSiteTree->GetTreeItem($key))
			if(isset($arRootItem['data']['id']))
				$arFilter=array('parent_id'=>$arRootItem['data']['id']);
		if($arCategories=$this->obCategory->GetList(array('orderation'=>'asc'),$arFilter,false,array('id','title','parent_id','active','text_ident','date_edit')))
			foreach($arCategories as $arItem)
			{
				$arHash=array('catsubcat','cat',$arItem['id'],$arItem['parent_id']);
				$arItem['module']=$this->sModule;
				$arRow=array(
					'key'=>$this->obSiteTree->GenHash($arHash),
					'title'=>$arItem['title'],
					'href'=>$this->sRootPath.$this->GenPath($arRootItem).$arItem['text_ident'].'/',
					'actions'=>'',
					'data'=>$arItem,
					'date_change'=>$arItem['date_edit']
				);
				if($arItem['id']==0 && $arItem['parent_id']==0)
				{
					$arRow['icon']='/icons_tree/page.gif';
					$arRow['href']=$this->sRootPath.'index.html';
					$arRow['freq']='always';
					$this->obSiteTree->AddTreeLeaf($key,$arRow);
				}
				else
				{
					$arRow['data']['parent']=$arRootItem;
					$arRow['icon']='/icons_tree/folder.gif';
					$this->obSiteTree->AddTreeBrunch($key,$arRow);
				}
			}
		if($arElements=$this->obElement->GetList(array('orderation'=>'asc'),$arFilter,false,array('id','title','parent_id','active','text_ident','date_edit')))
			foreach($arElements as $arItem)
			{
				$arHash=array('catsubcat','elm',$arItem['id'],$arItem['parent_id']);
				$arItem['module']=$this->sModule;
				$arRow=array(
					'key'=>$this->obSiteTree->GenHash($arHash),
					'title'=>$arItem['title'],
					'icon'=>'/icons_tree/page.gif',
					'href'=>$this->sRootPath.$this->GenPath($arRootItem).$arItem['text_ident'].'.html',
					'actions'=>'',
					'data'=>$arItem,
					'date_change'=>$arItem['date_edit']
				);
				$this->obSiteTree->AddTreeLeaf($key,$arRow);
			}
	}
}
