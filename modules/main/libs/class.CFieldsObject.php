<?php
/**
 * @filesource main/libs/class.CFieldsObject.php
 * Класс для работы с пользовательскими полями
 * фактически имеет обновленную функцию save которая выполняет опереации перед сохранением данных
 * Файл проекта kolos-cms.
 *
 * @since 19.06.2009
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CFilesObject.php';
require_once MODULES_DIR.'/main/libs/class.CFields.php';

class CFieldsObject extends CFilesObject
{
	/**
	 * Массив для хранения списка дополнительных полей
	 */
	protected $arUserFields;
	protected $bFields;			/**<Флаг указывает на наличие пользовательских полей*/
	protected $sFieldsModule;

	function __construct($sTable,$sUploadPath='',$sModule=false)
	{
		parent::__construct($sTable,$sUploadPath);
		$this->sFieldsModule='';
		if($sModule!=false)
		{
			$this->sFieldsModule=$sModule;
			$this->bFields=true;
			$obFields=new CFields();
			$this->arUserFields=$obFields->GetModuleFields($this->sFieldsModule,$this->sTable);
			foreach($this->arUserFields as $item)
			{
				$this->arFields[]='ext_'.$item['title'];
				CObject::$dbStructure[$this->sTable]['ext_'.$item['id']]=$item;
			}
		}
	}

	/**
	 * Метод обрабатывает одно поле перед сохранением в базу данных
	 * @param $prefix - префикс имени поля в массиве данных
	 * @param $key - имя поля
	 * @param $input - массив входных данных
	 * @param $value - массив описывающий поле
	 */
	protected function _ParseField($prefix,$key,array &$input,&$value)
	{
		$sResult=false;
		if ($value['Type']=='user_field')
		{
			if (array_key_exists($prefix.$key,$input))
				$sResult=$this->obDB->safesql(CFieldsValues::ParseValue($value['Field'],$input[$prefix.$key],$prefix));
			elseif (array_key_exists($prefix.$key,$_FILES))
			{
				$isFile=$this->obDB->safesql(CFieldsValues::ParseValue($value['Field'],$_FILES[$prefix.$key],$prefix));
				if($isFile=='clear')
					$sResult='';
				elseif($isFile!='no')
					$sResult=$isFile;
			}
		}
		else
			$sResult=parent::_ParseField($prefix,$key,$input,$value);
		return $sResult;
	}

	/**
	 * Метод возвращает список полей таблицы
	 */
	protected function GetTableFields()
	{
		$arFields=parent::GetTableFields();
		$arUserFields=$this->LoadUserFields();
		foreach ($arUserFields as $field)
			$arFields['ext_'.$field['title']]=Array('Type'=>'user_field','Field'=>$field);
		return $arFields;
	}

	protected function _BeforeSave(&$arData)
	{
		global $KS_EVENTS_HANDLER;
		if (!$KS_EVENTS_HANDLER->Execute('main', 'onBeforeFieldsObjectSave', $arData))
			throw new CError("MAIN_HANDLER_ERROR", 0, $KS_EVENTS_HANDLER->GetLastEvent());
		return true;
	}

	protected function _AfterSave(&$arData)
	{
		global $KS_EVENTS_HANDLER;
		if (!$KS_EVENTS_HANDLER->Execute('main', 'onAfterFieldsObjectSave', $arData))
			throw new CError("MAIN_HANDLER_ERROR", 0, $KS_EVENTS_HANDLER->GetLastEvent());
	}

	/**
	 * Метод возвращает список пользовательских полей
	 * @param $arFilter array - фильтр для вывода
	 */
	function GetUserFields($arFilter=false)
	{
		$this->LoadUserFields();
		if(is_array($arFilter))
		{
			$arResult=array();
			foreach($this->arUserFields as $id=>$arRow)
			{
				$select=true;
				foreach($arFilter as $key=>$value)
				{
					$select=$select&&($arRow[$key]==$value);
					if(!$select) break;
				}
				if($select) $arResult[$id]=$arRow;
			}
			return $arResult;
		}
		return $this->arUserFields;
	}

	/**
	 * Метод выполняет загрузку пользовательских полей во внутреннюю переменную
	 */
	protected function LoadUserFields()
	{
		if(!is_array($this->arUserFields))
		{
			$this->bFields=true;
			$obFields=new CFields();
			$this->arUserFields=$obFields->GetModuleFields($this->sFieldsModule,$this->sTable);
			if(!is_array($this->arUserFields)) $this->arUserFields=array();
		}
		return $this->arUserFields;
	}
}

