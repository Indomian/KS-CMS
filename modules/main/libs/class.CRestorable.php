<?php
/**
 * @filesource main/libs/class.CRestorable.php
 * Класс который обладает функционалом работы с восстановлением данных
 * Файл проекта kolos-cms.
 *
 * @since 09.06.2009
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CFieldsObject.php';
require_once MODULES_DIR.'/main/libs/interface.IRestorable.php';
require_once MODULES_DIR.'/main/libs/interface.IStorage.php';

/**
 * Данный класс обладает измененными методами восстановления и удаления данных
 */
class CRestorable extends CFieldsObject implements IRestorable
{
	private $obStorage;

	/**
	 * Конструктор класса, выполняет вызов базового конструктора и задаёт хранилище
	 * @param $sTable
	 * @param $sUploadPath
	 * @param $sModule
	 * @param $obStorage
	 */
	function __construct($sTable,$sUploadPath='',$sModule=false,IStorage $obStorage)
	{
		parent::__construct($sTable,$sUploadPath,$sModule);
		$this->obStorage=$obStorage;
	}

	function Serialize($arRecord)
	{
		return json_encode($arRecord);
	}

	function DeSerialize($sRecord)
	{
		return json_decode($sRecord,true);
	}

	function DeleteToBasket(array $arFilter)
	{
		if($arList=$this->GetList(false,$arFilter))
			foreach($arList as $arItem)
				$this->obStorage->Put(array('module'=>$this->sModule,'table'=>$this->sTable,'id'=>$arItem['id']),$this->Serialize($arItem));
		parent::DeleteItems($arFilter);
	}

	function RestoreFromBasket(array $arFilter)
	{

	}
}

