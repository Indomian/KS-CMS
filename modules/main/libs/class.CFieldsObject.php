<?php
/**
 * \file class.CFieldsObject.php
 * Класс для работы с пользовательскими полями
 * фактически имеет обновленную функцию save которая выполняет опереации перед сохранением данных
 * Файл проекта kolos-cms.
 *
 * Создан 19.06.2009
 *
 * \author blade39
 * \version
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

class CFieldsObject extends CFilesObject
{
	/**
	 * Массив для хранения списка дополнительных полей
	 */
	protected $arUserFields;
	protected $bFields;			/**<Флаг указывает на наличие пользовательских полей*/

	function __construct($sTable,$sUploadPath='',$sModule=false)
	{
		parent::__construct($sTable,$sUploadPath);
		if($sModule!=false)
		{
			$this->sFieldsModule=$sModule;
			//Подключаем работу с пользовательскими полями.
			if(class_exists('CFields'))
			{
				$this->bFields=true;
				$obFields=new CFields();
				$this->arUserFields=$obFields->GetModuleFields($this->sFieldsModule,$this->sTable);
				foreach($this->arUserFields as $item)
					$this->arFields[]='ext_'.$item['title'];
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
	protected function _ParseField($prefix,$key,&$input,&$value)
	{
		global $ks_db;
		$sResult=false;
		if ($value['Type']=='user_field')
		{
			if (array_key_exists($prefix.$key,$input))
			{
				$sResult=$ks_db->safesql(CFieldsValues::ParseValue($value['Field'],$input[$prefix.$key],$prefix));
			}
			if (array_key_exists($prefix.$key,$_FILES))
			{
				$isFile=$ks_db->safesql(CFieldsValues::ParseValue($value['Field'],$_FILES[$prefix.$key],$prefix));
				if($isFile=='clear')
				{
					$sResult='';
				}
				elseif($isFile!='no')
				{
					$sResult=$isFile;
				}
			}
		}
		else
		{
			$sResult=parent::_ParseField($prefix,$key,$input,$value);
		}
		return $sResult;
	}

	function Save($prefix='KS_',$data='')
	{
		global $ks_db,$KS_EVENTS_HANDLER, $KS_MODULES;
		try
		{
			$ks_db->begin();
			// 	Определяем таблицу с которой будем работать
			$table=$this->sTable;
			// Определяем данные с которыми будем работать
			if ($data=='')
				$input=$_REQUEST;
			elseif(is_array($data))
				$input=$data;
			else
				throw new CError ("MAIN_INCORRECT_DATA_FORMAT",999);
			//Очищаем и подготавливаем массив полей
			$data=array();
			//Получаем список своих полей из таблицы
			$fields=$this->_GetTableFields();
			// Если есть доп поля то неплохо бы проверить и их
			$arUserFields=$this->LoadUserFields();
			foreach ($arUserFields as $field)
			{
				$fields['ext_'.$field['title']]=Array('Type'=>'user_field','Field'=>$field);
			}

			// Начинаем обход полей и сбор данных
			foreach ($fields as $key=>$value)
			{
				$sValue=$this->_ParseField($prefix,$key,$input,$value);
				if($sValue!==false)
				{
					$data[$key]=$sValue;
				}
			}
			// Вызываем обработчик событий перед сохранением
			if (!$KS_EVENTS_HANDLER->Execute('main', 'onBeforeFieldsObjectSave', $data)) throw new CError("MAIN_HANDLER_ERROR", 0, $KS_EVENTS_HANDLER->GetLastEvent());
			if(array_key_exists('id',$data))
			{
				$query_select = "SELECT id FROM " . PREFIX . $table . " WHERE id = '" . $data['id'] . "' LIMIT 1";
				$ks_db->query($query_select);
				if ($ks_db->num_rows()>0)
				{
					$query = "";
					foreach($data as $key=>$item)
						$query .= "`$key` = '" . $item . "', ";
					$query = chop($query, " ,");
					$update_query = "UPDATE " . PREFIX . $table . " SET $query WHERE id = '" . $data['id'] . "'";
					$ks_db->query($update_query);
					$res = $data['id'];
					if($res)
						unset(CObject::$arCache[$this->sTable][$res]);
					// Вызываем обработчик событий после сохранением
					if (!$KS_EVENTS_HANDLER->Execute('main', 'onAfterFieldsObjectSave', $data))
						throw new CError("MAIN_HANDLER_ERROR", 0, $KS_EVENTS_HANDLER->GetLastEvent());
					$ks_db->commit();
					return $res;
				}
			}

			if(!array_key_exists('date_add',$data) && in_array('date_add',$this->arFields))
				$data['date_add']=time();
			if(!array_key_exists('date_edit',$data) && in_array('date_edit',$this->arFields))
				$data['date_edit']=time();
			$fields="";
			$values="";
			foreach($data as $key=>$item)
			{
				if (!in_array($key,$this->auto_fields))
					{
						$fields.=$key.",";
						$values.="'$item',";
					}
			}
			$fields=chop($fields," ,");
			$values=chop($values," ,");
			$check=$this->GenCheck($data);
			if ($check!="")
			{
				$ks_db->query("SELECT id FROM ".PREFIX.$table." WHERE $check");
				$numrows=$ks_db->num_rows();
			}
			else
			{
				$numrows=0;
			}
			if ($numrows>0)
			{
				throw new CError("MAIN_RECORD_ALREADY_EXISTS",KS_ERROR_MAIN_ALREADY_EXISTS,$this->check_fields);
				$res=$_REQUEST[$prefix.'id'];
			}
			else
			{
				$query="INSERT INTO ".PREFIX.$table."($fields) VALUES ($values)";
				$ks_db->query($query);
				$res=$ks_db->insert_id();
			}

			// Вызываем обработчик событий после сохранением
			if (!$KS_EVENTS_HANDLER->Execute('main', 'onAfterFieldsObjectSave', $data))
				throw new CError("MAIN_HANDLER_ERROR", 0, $KS_EVENTS_HANDLER->GetLastEvent());
			$ks_db->commit();
			return $res;
		}
		catch(CError $e)
		{
			$ks_db->rollback();
			throw $e;
		}
		catch(Exception $e)
		{
			$ks_db->rollback();
			die();
			throw new CError($e);
		}
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
			if (class_exists('CFields'))
			{
				$this->bFields=true;
				$obFields=new CFields();
				$this->arUserFields=$obFields->GetModuleFields($this->sFieldsModule,$this->sTable);
				if(!is_array($this->arUserFields)) $this->arUserFields=array();
			}
		}
		return $this->arUserFields;
	}
}

