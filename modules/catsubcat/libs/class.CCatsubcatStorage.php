<?php
/**
 * @filesource catsubcat/libs/class.CCatsubcatStorage.php
 * Файл содержит в себе класс реализующий Storage интерфейс для работы корзины модуля "Текстовые страницы"
 * Файл проекта kolos-cms.
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 * @since 19.02.2012
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/main/libs/interface.IStorage.php';

/**
 * Класс обеспечивает высокоуровневые функции для модуля wave
 */
class CCatsubcatStorage extends CObject implements IStorage
{
	/**
	 * Конструктор создаёт объект для связи с таблицей хранилища
	 * @param unknown_type $sTable
	 */
	function __construct($sTable='catsubcat_storage')
	{
		parent::__construct($sTable);
	}

	function Put($arHash,$sData)
	{
		$arData=array(
			'table'=>$arHash['table'],
			'element_id'=>$arHash['id']
		);
		if($arOldRecord=$this->GetRecord($arData))
		{
			$this->Update($arOldRecord['id'],array('data'=>$sData));
		}
		else
		{
			$arData['data']=$sData;
			$this->Save($arData);
		}
	}

	function Get($arHash)
	{
		$arFilter=array(
			'table'=>$arHash['table'],
			'element_id'=>$arHash['id']
		);
		if($arOldRecord=$this->GetRecord($arFilter))
		{
			$arResult=array(
				'id'=>$arOldRecord['element_id'],
				'table'=>$arHash['table'],
				'module'=>'catsubcat'
			);
			return $arResult;
		}
		return false;
	}

	function Delete($arHash)
	{
		$arFilter=array(
			'table'=>$arHash['table'],
			'element_id'=>$arHash['id']
		);
		$this->DeleteItems($arFilter);
	}

	function Clear()
	{
		$this->DeleteItems();
	}
}