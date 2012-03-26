<?php
/**
 * В файле содержится класс выполняющий построение и хранение кэша дерева сайта
 *
 * @filesource main/libs/class.CSiteTree.php
 * @author blade39 <blade39@kolosstudio.ru>
 * @since 10.01.2012
 * @version 2.6
 */

/* Обязательно вставляем во все файлы для защиты от взлома */
if (!defined("KS_ENGINE")) die("Hacking attempt!");

class CSiteTree
{
	private $arTree;
	private $arTreeList;
	private $obModules;

	function __construct(CModuleManagment $obModules)
	{
		$this->obModules=$obModules;
		if(isset($_SESSION['adminTree']))
		{
			$this->arTree=$_SESSION['adminTree'];
			$this->ConvertTreeToList($this->arTree);
		}
		else
		{
			$this->arTree=array();
			$this->arTreeList=array();
		}
	}

	function __destruct()
	{
	}

	function SaveTree()
	{
		unset($_SESSION['adminTree']);
		$this->arTree=$this->ConvertListToTree($this->arTreeList);
		$_SESSION['adminTree']=$this->GetTree();
	}

	function GetTreeList()
	{
		return $this->arTreeList;
	}

	function GetTree()
	{
		return $this->ConvertListToTree($this->arTreeList);
		return $this->arTree;
	}
	
	function Modules()
	{
		return $this->obModules;
	}

	/**
	 * Метод преобразует список в дерево
	 */
	private function ConvertListToTree(array $arList)
	{
		$arResult=array();
		foreach($arList as $key=>$arItem)
		{
			if(!isset($arItem['parent']))
				$arResult[$key]=$arItem;
		}
		return $arResult;
	}
	
	/**
	 * Метод преобразует дерево в список
	 * @param array $arTree
	 */
	private function ConvertTreeToList(array &$arTree)
	{
		if(is_array($arTree))
			foreach($arTree as $key=>&$arItem)
			{
				$this->arTreeList[$key]=$arItem;
				if(isset($arItem['children']))
					$this->ConvertTreeToList($arItem['children']);
			}
	}

	/**
	 * Метод возвращает ветвь дерева для отрисовки в пользовательской части
	 * @param string $key
	 */
	function GetTreeBrunch($key='')
	{
		$arResult=array();
		if($key=='')
			$arRoot=$this->arTree;
		elseif(array_key_exists($key,$this->arTreeList) && array_key_exists('children',$this->arTreeList[$key]))
			$arRoot=$this->arTreeList[$key]['children'];
		else
			$arRoot=array();
		foreach($arRoot as $key=>$arItem)
		{
			$arNewItem=$arItem;
			unset($arNewItem['data']);
			$arResult[$key]=$arNewItem;
		}
		return $arResult;
	}

	/**
	 * Метод добавляет лист на дерево
	 * @param string $sParentKey - ключ листа в дереве
	 * @param array $arLeaf - данные листа
	 */
	public function AddTreeLeaf($sParentKey='',array $arLeaf)
	{
		if($sParentKey!='')
		{
			if(array_key_exists($sParentKey,$this->arTreeList))
			{
				$arLeaf['parent']=$sParentKey;
				if(array_key_exists('children',$this->arTreeList[$sParentKey]))
				{
					$this->arTreeList[$sParentKey]['children'][$arLeaf['key']]=$arLeaf;
					$this->arTreeList[$arLeaf['key']]=&$this->arTreeList[$sParentKey]['children'][$arLeaf['key']];
				}
				else
					throw new CError('MAIN_TREE_CANT_ADD_LEAF_TO_LEAF');
			}
			else
			{
				throw new CError('MAIN_TREE_PARENT_NOT_FOUND');
			}
		}
		else
		{
			$this->arTree[$arLeaf['key']]=$arLeaf;
			$this->arTreeList[$arLeaf['key']]=&$this->arTree[$arLeaf['key']];
		}
	}

	public function Clean()
	{
		$this->arTree=array();
		$this->arTreeList=array();
	}

	/**
	 * Метод добавляет ветку к дереву
	 * @param string $sParentKey
	 * @param array $arLeaf
	 */
	public function AddTreeBrunch($sParentKey='',array $arLeaf)
	{
		$arLeaf['children']=array();
		$this->AddTreeLeaf($sParentKey,$arLeaf);
	}

	/**
	 * Метод генерирует хэш записи в дереве
	 * @param array $arData - массив данных по которым генерировать ключ
	 * @return string
	 */
	public function GenHash(array $arData)
	{
		return md5(join('|',$arData));
	}

	public function GetTreeItem($key)
	{
		if(array_key_exists($key,$this->arTreeList))
			return $this->arTreeList[$key];
		return false;
	}
}
