<?php
/**
 * \file class.CCategory.php
 * Файл контейнер класса ccategory
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

class CCategory extends CCommonCategory
{
	private $obLinks;

	function __construct($tables = 'catsubcat_catsubcat',$sElementsTable = 'catsubcat_element')
	{
		$this->fType='cat';
		$this->sFieldsModule='catsubcat';
		parent::__construct($tables,$sElementsTable);
		$this->obLinks=new CObject('catsubcat_links');
	}

	/**
	 * Метод удаления разделов, перекрывает родительский
	 */
	function DeleteItems($arFilter)
	{
		$arList=$this->GetList(array('id'=>'asc'),array('>deleted'=>0),0,array('id'));
		if(is_array($arList)&&count($arList)>0)
		{
			foreach($arList as $arItem)
				$arIds[]=$arItem['id'];
			$this->obLinks->DeleteItems(array('->id'=>$arIds));
		}
		return parent::DeleteItems($arFilter);
	}
}

