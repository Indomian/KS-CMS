<?php
/**
 * @filesource catsubcat/libs/class.CElement.php
 * Контейнер для класса CElement
 * Файл проекта kolos-cms.
 *
 * Создан 25.02.2010
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/catsubcat/libs/class.CCommonElement.php';
require_once MODULES_DIR.'/catsubcat/libs/class.CElementLinks.php';
require_once MODULES_DIR.'/catsubcat/libs/class.CCatsubcatAPI.php';

/**
 * Класс обработки элементов для модуля production
 * Наследуется от класса модуля CCommonElement, перекрывается конструктор.

* @version 2.6
 *
 * BlaDe39 Убран лишний код, переделана передача параметров для наследования
 */
final class CElement extends CCommonElement
{
	private $obLinks;
	private $arLinks; /**Массив кодов разделов к которым привязана запись*/

	function __construct($obCategory=NULL)
	{
		parent::__construct('catsubcat_element','/catsubcat','catsubcat',CCatsubcatAPI::get_instance()->Storage(), $obCategory);
		$this->obLinks=new CElementLinks('catsubcat_links');
		$this->AddFileField('img');
		$this->obLinks->AddAutoField('id');
	}

	/**
	 * Перекрытие родительского метода получения записи. Дополнительно к основным данным
	 * получает список разделов в которых находится элемент
	 */
	function GetRecord($arFilter=false)
	{
		if($arData=parent::GetRecord($arFilter))
		{
			$arData['links']=array();
			if($arLinks=$this->obLinks->GetList(array('id'=>'asc'),array('element_id'=>$arData['id'])))
				foreach($arLinks as $arItem)
					$arData['links'][]=$arItem['category_id'];
		}
		return $arData;
	}

	/**
	 * Метод выполняет подготовку данных для сохранения методом Save
	 * @param $arFields
	 * @param $arInput
	 * @param $sPrefix
	 */
	protected function _PrepareData($arInput,$sPrefix='')
	{
		$this->arLinks=false;
		$arResult=parent::_PrepareData($arInput,$sPrefix);
		if(isset($arInput[$sPrefix.'links']) && is_array($arInput[$sPrefix.'links'])&&count($arInput[$sPrefix.'links'])>0)
			$this->arLinks=$arInput[$sPrefix.'links'];
		return $arResult;
	}

	/**
	 * Метод выполняется после сохранения записи и обновляет привязки записи к разделам
	 * @param unknown_type $arData
	 */
	protected function _AfterSave(&$arData)
	{
		$this->obLinks->DeleteItems(array('element_id'=>$arData['id']));
		if(is_array($this->arLinks))
			foreach($this->arLinks as $cat_id)
			{
				$arLink=array('category_id'=>$cat_id,'element_id'=>$arData['id']);
				$this->obLinks->Save($arLink);
			}
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
			if(!$arSelect)
				$arSelect=$this->arFields;
			$arSelect[$this->obLinks->sTable.'.category_id']=$this->obLinks->sTable.'_category_id';
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
			$sLinksTable=$this->obLinks->GetTable();
			$arFilter['<?'.$this->sTable.'.id']=$sLinksTable.'.element_id';
			$arFilter['OR']=array(
				'parent_id'=>$parent_id,
				$sLinksTable.'.category_id'=>$parent_id,
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

