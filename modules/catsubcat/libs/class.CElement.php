<?php
/**
 * @file class.CElement.php
 * Контейнер для класса CElement
 * Файл проекта kolos-cms.
 *
 * Создан 25.02.2010
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.5.5
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/catsubcat/libs/class.CCommonElement.php';
require_once MODULES_DIR.'/catsubcat/libs/class.CElementLinks.php';

/**
 * Класс обработки элементов для модуля production
 * Наследуется от класса модуля CCommonElement, перекрывается конструктор.

* @version 2.5.5
 *
 * BlaDe39 Убран лишний код, переделана передача параметров для наследования
 */
class CElement extends CCommonElement
{
	private $obLinks;

	function __construct($sCategoryTable="catsubcat_catsubcat",$sElementsTable='catsubcat_element')
	{
		$this->sFieldsModule='catsubcat';
		parent::__construct($sCategoryTable,$sElementsTable);
		$this->obLinks=new CElementLinks('catsubcat_links');
		$this->obLinks->AddAutoField('id');
	}

	/**
	 * Перекрытие родительского метода получения записи. Дополнительно к основным данным
	 * получает список разделов в которых находится элемент
	 */
	function GetRecord($arFilter=false)
	{
		$arData=parent::GetRecord($arFilter);
		if(is_array($arData)&&($arData['id']>0))
		{
			$arLinks=$this->obLinks->GetList(array('id'=>'asc'),array('element_id'=>$arData['id']));
			$arData['links']=array();
			if(is_array($arLinks)&&count($arLinks)>0)
			{
				foreach($arLinks as $arItem)
				{
					$arData['links'][]=$arItem['category_id'];
				}
			}
		}
		return $arData;
	}

	/**
	 * Перекрытие метода сохранения записи для обновления привязок записи к разделам
	 */
	function Save($prefix='CSC_',$data=false)
	{
		if($data==false) $data=$_POST;
		$id=parent::Save($prefix,$data);
		if($id>0)
		{
			$this->obLinks->DeleteItems(array('element_id'=>$id));
			if(isset($data[$prefix.'links']) && is_array($data[$prefix.'links'])&&count($data[$prefix.'links'])>0)
			{
				foreach($data[$prefix.'links'] as $cat_id)
				{
					$arData=array('category_id'=>$cat_id,'element_id'=>$id);
					$this->obLinks->Save('',$arData);
				}
			}
		}
		return $id;
	}

	/**
	 * Метод получения списка, добавлена выборка дополнительных значений по линкам
	 */
	function GetList($arOrder=false,$arFilter=false,$arLimit=false,$arSelect=false,$arGroup=false)
	{
		if($arFilter && array_key_exists('parent_id',$arFilter))
		{
			$parent_id=$arFilter['parent_id'];
			unset($arFilter['parent_id']);
			$arFilter['<?'.$this->sTable.'.id']=$this->obLinks->sTable.'.element_id';
			$arFilter['OR']=array(
				'parent_id'=>$parent_id,
				$this->obLinks->sTable.'.category_id'=>$parent_id,
			);
			$this->bDistinctMode=true;
		}
		return parent::GetList($arOrder,$arFilter,$arLimit,$arSelect,$arGroup);
	}

	/**
	 * Метод подсчета количества элементов, перекрывает родительский, добавлена выборка из
	 * таблицы линков.
	 */
	function Count($arFilter = false, $fGroup = false)
	{
		if(array_key_exists('parent_id',$arFilter))
		{
			$parent_id=$arFilter['parent_id'];
			unset($arFilter['parent_id']);
			$arFilter['<?'.$this->sTable.'.id']=$this->obLinks->sTable.'.element_id';
			$arFilter['OR']=array(
				'parent_id'=>$parent_id,
				$this->obLinks->sTable.'.category_id'=>$parent_id,
			);
			$this->bDistinctMode=true;
		}
		if(!$fGroup)
		{
			$arRes=parent::Count($arFilter,'id');
			return count($arRes);
		}
		return parent::Count($arFilter,$fGroup);
	}
}
?>
