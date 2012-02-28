<?php
/**
 * @filesource catsubcat/libs/class.CCategory.php
 * Файл контейнер класса ccategory
 * Файл проекта kolos-cms.
 *
 * @since 25.02.2010
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/catsubcat/libs/class.CCommonCategory.php';
require_once MODULES_DIR.'/catsubcat/libs/class.CElementLinks.php';

final class CCategory extends CCommonCategory
{
	private $obLinks;

	function __construct($obElement=NULL)
	{
		parent::__construct('catsubcat_catsubcat','/catsubcat','catsubcat',CCatsubcatAPI::get_instance()->Storage(), $obElement);
		$this->AddFileField('img');
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
