<?php
/**
 * \file class.CElement.php
 * Сюда сделать описание файла
 * Файл проекта kolos-cms.
 *
 * Создан 25.02.2010
 *
 * \author blade39
 * \version
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

/**
 * Класс реализует работу с записями текстовых страниц
 */
class CCommonElement extends CCategorySubCategory
{
	static private $__arUserFields;

	function __construct($sCategoryTable="",$sElementsTable='')
	{
		parent::__construct($sElementsTable,$sCategoryTable);
		$this->AddFileField('img');
	}

	/**
	 * Метод восстанавливает запись, в случае если раздел в котором находился элемент
	 * удален - выводит сообщение о необходимости восстановления этого раздела
	 */
	function RestoreItems($arFilter)
	{
		if(!$this->ParseFilter($arFilter))
			$arFilter['>deleted']='0';
		$obCategory=new CCategory($this->sElTable,$this->sTable);
		$arItems=$this->GetList(array('id'=>'asc'),$arFilter);
		if(is_array($arItems)&&count($arItems)>0)
		{
			$arCats=array();
			foreach($arItems as $key=>$item)
			{
				$arCats[]=$item['parent_id'];
			}
			if(count($arCats)>0)
			{
				$arCategory=$obCategory->GetList(array('id'=>'asc'),array('->id'=>"(".join(',',$arCats).')','>deleted'=>-1));
				if(is_array($arCategory)&&count($arCategory)>0)
				{
					foreach($arCategory as $arRow)
					{
						if($arRow['deleted']>0)
						{
							$obCategory->RestoreItems(array('id'=>$arRow['id'],'childs'=>'N'));
						}
					}
				}
			}
			return parent::RestoreItems($arFilter);
		}
		return false;
	}

	/**
	 * Метод генерирует уникальный ключ для записи
	 * @param array $arRecord
	 */
	function GenHash($arRecord)
	{
		return 'e'.$arRecord['id'];
	}

	function GetParents($id)
	{
		echo "<pre>";
		print_r(parent::element_info(Array('element'=>$this->sTable,'catsubcat'=>$this->sCatTable),intval($id),true,0,'text_ident'));
		echo "</pre>";
	}

	/**
	 *	\copydoc CObject::GetRecord()
	 */
	function GetRecord($arFilter=false)
	{
		global $ks_db;
		if(KS_RELEASE!=1) $ks_db->add2log(__METHOD__.' at '.__LINE__.' in '.__FILE__);
		$arResult=$arFilter;
		$arResult['?'.$this->sElTable.'.id']=$this->sTable.'.parent_id';
		//Генерируем строку поиска
		$sWhere=$this->_GenWhere($arResult);
		if(strlen($sWhere)>0)
		{
			//Формируем запрос
			$query="SELECT ".$this->arTables[$this->sElTable].".id as parent_id";
			//Получаем список полей внутренних
			//Исправление бага с работой с доп полями
			$arFields=$this->arFields;
			foreach($arFields as $sField)
			{
				$query.= " ,".$this->arTables[$this->sTable].".$sField";
			}
			//Получаем список полей раздела
			$arCatFields=$this->_GetTableFields($this->sElTable);
			foreach($arCatFields as $sField=>$arField)
			{
				if($sField=='id') continue;
				$query.= " ,".$this->arTables[$this->sElTable].".$sField as CAT_$sField";
			}
			$query.="  FROM ".$this->_GenFrom()." $sWhere LIMIT 1";
			//echo $query;
			$res=$ks_db->query($query);
			if($ks_db->num_rows($res)>0)
			{
				$arRow=$ks_db->get_row($res);
				$this->_ParseItem($arRow);
				return $arRow;
			}
		}
		return false;
	}

	function BuildPath(&$arResult)
	{
	    $res[]=$arResult['text_ident'];
	    if (is_array($arResult['cat_info']))
	    {
	    	$subArray=$arResult['cat_info'];
			while($subArray!=0)
			{
				if($subArray['text_ident']!='')
				{
					$res[]=$subArray['text_ident'];
				}
					$subArray=$subArray['parents'][0];

			}
		}
		if(count($res)>0) return join('/',array_reverse($res));
	}

	/**
	 * Метод возращает запись по номеру, также генерирует путь к этой записи в ПЧ.
	 */
	function GetById($id)
	{
		$obCategory=new CCommonCategory($this->sElTable,$this->sTable);
		$arResult=$this->GetRecord(array('id'=>$id));
		$arResult['URL']=$obCategory->GetFullPath($arResult['parent_id']).$arResult['text_ident'].'.html';
		return $arResult;
	}
}

