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

require_once MODULES_DIR.'/catsubcat/libs/class.CCategorySubCategory.php';

/**
 * Класс реализует работу с записями текстовых страниц
 */
class CCommonElement extends CCategorySubCategory
{
	protected $obCategory;

	function __construct($sTable,$sUploadPath='',$sModule=false,IStorage $obStorage, $obCategory)
	{
		parent::__construct($sTable,$sUploadPath,$sModule,$obStorage);
		$this->obCategory=$obCategory;
	}

	/**
	 * Метод выполняет установку объекта категории для элемента
	 * @param $obCategory
	 */
	function SetCategory(CCommonCategory $obCategory)
	{
		$this->obCategory=$obCategory;
	}

	/**
	 * Метод генерирует уникальный ключ для записи
	 * @param array $arRecord
	 */
	function GenHash($arRecord)
	{
		return 'e'.$arRecord['id'];
	}

	/**
	 *	\copydoc CObject::GetRecord()
	 */
	function GetRecord($arFilter=false)
	{
		if(!$arFilter) return false;
		if(KS_RELEASE!=1) $this->obDB->add2log(__METHOD__.' at '.__LINE__.' in '.__FILE__);
		if(!is_null($this->obCategory))
			$arFilter['?'.$this->obCategory->GetTable().'.id']=$this->sTable.'.parent_id';
		//Генерируем строку поиска
		$sWhere=$this->_GenWhere($arFilter);
		if(strlen($sWhere)>0)
		{
			//Формируем запрос
			$arSelect=$this->arFields;
			if(!is_null($this->obCategory))
			{
				$arCatFields=$this->obCategory->GetFields();
				foreach($arCatFields as $sField)
					if($sField!='id') $arSelect[$this->obCategory->GetTable().'.'.$sField]='CAT_'.$sField;
			}
			if($arList=$this->GetList(false,$arFilter,1,$arSelect))
			{
				$arItem=array_pop($arList);
				if(!is_null($this->obCategory))
					$arItem['URL']=$this->obCategory->GetFullPath($arItem['parent_id']).$arItem['text_ident'].'.html';
				return $arItem;
			}
		}
		return false;
	}
}

