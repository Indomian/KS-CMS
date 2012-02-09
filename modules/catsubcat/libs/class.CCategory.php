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

require_once MODULES_DIR.'/catsubcat/libs/class.CCommonCategory.php';
require_once MODULES_DIR.'/catsubcat/libs/class.CElementLinks.php';

class CCategory extends CCommonCategory
{
	private $obLinks;

	function __construct($tables = 'catsubcat_catsubcat',$sElementsTable = 'catsubcat_element')
	{
		$this->fType='cat';
		$this->sFieldsModule='catsubcat';
		parent::__construct($tables,$sElementsTable);
		$this->obLinks=new CElementLinks('catsubcat_links');
	}

	/**
	 * Метод удаления разделов, перекрывает родительский
	 */
	function DeleteItems(array $arFilter)
	{
		if($arList=$this->GetList(array('id'=>'asc'),array('>deleted'=>0),0,array('id')))
			$this->obLinks->DeleteItems(array('->id'=>array_keys($arList)));
		return parent::DeleteItems($arFilter);
	}
}
