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
			$this->arTreeList=$this->GetTreeList($this->arTree);
		}
		else
		{
			$this->arTree=array();
			$this->arTreeList=array();
		}
	}

	function __destruct()
	{
		$_SESSION['adminTree']=$this->arTree;
	}

	function Modules()
	{
		return $this->obModules;
	}

	/**
	 * Метод преобразует дерево в список
	 * @param array $arTree
	 */
	function GetTreeList(array &$arTree)
	{
		$arList=array();
		if(is_array($arTree))
			foreach($arTree as $key=>$arItem)
			{
				$arList[$key]=&$arTree[$key];
				if(array_key_exists('children',$arItem))
					$arList=array_merge($arList,$this->GetTreeList($arItem['children']));
			}
		return $arList;
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
		if($sParentKey!='' && array_key_exists($sParentKey,$this->arTreeList))
		{
			if(array_key_exists('children',$this->arTreeList[$sParentKey]))
				$this->arTreeList[$sParentKey]['children'][$arLeaf['key']]=$arLeaf;
			else
				throw new CError('MAIN_TREE_CANT_ADD_LEAF_TO_LEAF');
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