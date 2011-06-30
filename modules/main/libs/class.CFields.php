<?php

if( !defined('KS_ENGINE') )
{
  die("Hacking attempt!");
}

include_once MODULES_DIR.'/main/libs/class.CMain.php';

/*
KS ENGINE

File: /catsubcat/libs/class.CFields.php
Original Code by BlaDe39
http://kolos-studio.ru/
(c) 2008

Назначение: Управление полями (создание редактрирование удаление)
*/

class CFields extends CObject
{
    static protected $arUserFields;	/*!<Массив с описанием пользовательских полей различных модулей и типов*/
    static protected $bInit;

	function __construct($sTable='main_fields')
	{
		global $smarty;
		parent::__construct($sTable);
		$this->arFields=Array(
			'id',
			'title',
			'description',
			'script',
			'module',
			'type',
			'orderation',
			'default',
			'option_1',
			'option_2',
		);
		if(!self::$bInit)
		{
			$sName="CFields";
			$smarty->register_function("showField", Array($sName,"_showField"));
			if(IS_ADMIN) $smarty->register_function("configField",array($sName,'_configField'));
			self::$bInit=true;
		}
	}

	static function _showField($params)
	{
		if(!array_key_exists('prefix',$params)) $params['prefix']='CSC_';
		$sParam2 = $params['field']['option_2'];
		if (file_exists(MODULES_DIR.'/main/fields/'.$params['field']['script'].'/show.php'))
		{
			$sResult='';
			include MODULES_DIR.'/main/fields/'.$params['field']['script'].'/show.php';
			return $sResult;
		}
		else
		{
			return "Не найден обработчик поля";
		}
	}

	static function _configField($params)
	{
		if($params['prefix']=='') $params['prefix']='CSC_';
		if (file_exists(MODULES_DIR.'/main/fields/'.$params['field']['script'].'/config.php'))
		{
			$sResult='';
			include MODULES_DIR.'/main/fields/'.$params['field']['script'].'/config.php';
			return $sResult;
		}
		else
		{
			return CFields::_showField($params);
		}
	}

	/**
	 * Перекрыт метод получения одной записи. Добавлено внутренее кэширование данных.
	 */
	function GetRecord($arFilter=false)
	{
		$key=$this->_GenFilterHash($arFilter);
		if(!array_key_exists($key,self::$arUserFields))
		{
			self::$arUserFields[$key]=parent::GetRecord($arFilter);
		}
		return self::$arUserFields[$key];
	}

	/**
	 * Метод выполняет перенос поля из одного модуля в другой, также меняется таблица
	 * @param array $from - откуда переносим
	 * @param array $to - куда переносим
	 */
	function MoveField($from,$to)
	{
		global $ks_db;
		$arToFields=$this->GetFieldsList("ext_",$to['type']);
		if(is_array($arToFields)&&array_key_exists('ext_'.$to['title'],$arToFields))
		{
			throw new CError("MAIN_DUPLICATE_TABLE_FIELD",241);
		}
		if($from['type']==$to['type']) throw new CError("MAIN_NOT_MOVE_FIELD_ITSELF",242);
		$arFromFields=$this->GetFieldsList('ext_',$from['type']);
		if(!array_key_exists('ext_'.$from['title'],$arFromFields))
		{
			throw new CError("MAIN_NO_TABLE_FIELDS",243);
		}
		$sType=$arFromFields['ext_'.$from['title']]['Type'];
		if($arFromFields['ext_'.$from['title']]['Size']>0)
		{
			$sType.='('.$arFromFields['ext_'.$from['title']]['Size'].')';
		}
		$sValue=$arFromFields['ext_'.$from['title']]['Default'];
		try
		{
			$query="ALTER TABLE ".PREFIX.$to['type']." ADD COLUMN ext_".$to['title']." $sType NOT NULL DEFAULT '".$sValue."'";
		//	Добавляем новое поле
			$ks_db->query($query);
			//Удаляем старое
			$query="ALTER TABLE ".PREFIX.$from['type'].' DROP COLUMN ext_'.$from['title'];
			$ks_db->query($query);
		}
		catch(Exception $e)
		{
			throw new CError("MAIN_ERROR_PROCESSING_FIELD", 245, $e->GetMessage());
		}
	}

	/**
	 * Метод выполняет переименовывание поля
	 * @param array $from - описание поля которое переименовываем
	 * @param string $newname - новое имя поля
	 *
	 */
	function RenameField($from,$newname)
	{
		global $ks_db;
		if($from['title']==$newname) throw new CError("MAIN_NO_RENAME_ITSELF");
		if(strlen($newname)<1) throw new CError("MAIN_NOT_DELETE_FIELD_NAME");
		$arFromFields=$this->GetFieldsList('ext_',$from['type']);
		if(array_key_exists('ext_'.$newname,$arFromFields))
		{
			throw new CError("MAIN_TABLE_CONTAINS_FIELD_NAME",246);
		}
		if(!array_key_exists('ext_'.$from['title'],$arFromFields))
		{
			throw new CError("MAIN_FIELD_DOES_NOT_EXIST_TABLE",247);
		}
		if(!preg_match('#^[a-z0-9_]+$#si',$newname)) throw new CError("MAIN_FIELD_NEW_NAME_ERROR",248);
		$sType=$arFromFields['ext_'.$from['title']]['Type'];
		if($arFromFields['ext_'.$from['title']]['Size']>0)
		{
			$sType.='('.$arFromFields['ext_'.$from['title']]['Size'].')';
		}
		$sValue=$arFromFields['ext_'.$from['title']]['Default'];
		try
		{
			$query="ALTER TABLE ".PREFIX.$from['type']." CHANGE COLUMN ext_".$from['title']." ext_".$newname." $sType NOT NULL DEFAULT '".$sValue."'";
		//	обновляем поле
			$ks_db->query($query);
		}
		catch(Exception $e)
		{
			throw new CError("MAIN_ERROR_PROCESSING_FIELD", 249, $e->GetMessage());
		}
	}

	/**
	 * Метод обновляет значение по умолчанию для указанного поля
	 * @param array $from - массив описывающий поле
	 */
	function UpdateDefault($from)
	{
		global $ks_db;
		$arFromFields=$this->GetFieldsList('ext_',$from['type']);
		if(!array_key_exists('ext_'.$from['title'],$arFromFields))
		{
			throw new CError("MAIN_FIELD_DOES_NOT_EXIST_TABLE",250);
		}
		$sType=$arFromFields['ext_'.$from['title']]['Type'];
		if($arFromFields['ext_'.$from['title']]['Size']>0)
		{
			$sType.='('.$arFromFields['ext_'.$from['title']]['Size'].')';
		}
		$sValue=$from['default'];
		try
		{
			$query="ALTER TABLE ".PREFIX.$from['type']." MODIFY COLUMN ext_".$from['title']." $sType NOT NULL DEFAULT '".$sValue."'";
		//	обновляем поле
			$ks_db->query($query);
		}
		catch(Exception $e)
		{
			throw new CError("MAIN_ERROR_PROCESSING_FIELD", 251, $e->GetMessage());
		}
	}

	/**
	 * Метод выполняет сохранение записи, перекрывает родительский метод, в случае успеха записи
	 * в таблицу полей пробует изменить таблицу к которой добавляется поле
	 */
	function Save($prefix="",$data=false)
	{
		global $ks_db;
		if(!$data) $data=$_POST;
		try
		{
			$ks_db->begin();
			$arTypes=$this->GetTypes();
			$script=$data[$prefix.'script'];
			//Проверим данные на правильность
			if(!array_key_exists($script,$arTypes)) throw new CError("MAIN_NO_FIELD_NAME");
			if(!preg_match('#^[a-z0-9_]+$#i',$data[$prefix.'title'])|| $data[$prefix.'title']=='') throw new CError("MAIN_ENTER_FIELD_NAME");
			if($data[$prefix.'module']=='') throw new CError("MAIN_SELECT_MODULE");
			if(!isset($data[$prefix.'type']) || ($data[$prefix.'type']=='')) throw new CError("MAIN_SELECT_FIELD_ASSIGNMENT");
			if(isset($data['CM_ext_'.$data[$prefix.'title']]))
				$value=$data['CM_ext_'.$data[$prefix.'title']];
			else
				$value='';
			$sType='varchar(255)';
			$sParam2='';
			if(isset($data['CM_option_2']))
				$sParam2 = $data['CM_option_2'];
			$arField=$this->GetRecordFromPost($prefix,$data);
			if($data[$prefix.'id']<1) $arField['title']='';
			if (file_exists(MODULES_DIR.'/main/fields/'.$script.'/savedef.php'))
			{
				include MODULES_DIR.'/main/fields/'.$script.'/savedef.php';
			}
			elseif(file_exists(MODULES_DIR.'/main/fields/'.$script.'/save.php'))
			{
				include MODULES_DIR.'/main/fields/'.$script.'/save.php';
			}
			else
			{
				$sValue=$value;
			}
			$data[$prefix.'default']=$sValue;
			//Проверяем на наличие старого значения
			if($data[$prefix.'id']>0)
			{
				$arOldData=$this->GetRecord(array('id'=>$data[$prefix.'id']));
			}
			$id=parent::Save($prefix,$data);
			if($id)
			{
				//Если было старое значение, то надо проверить на переименование поля
				if(isset($arOldData) && is_array($arOldData)&&(
					($arOldData['title']!=$data[$prefix.'title'])||
					($arOldData['module']!=$data[$prefix.'module'])||
					($arOldData['type']!=$data[$prefix.'type'])||
					($arOldData['default']!=$data[$prefix.'default'])||
					($arOldData['option_1']!=$data[$prefix.'option_1'])||
					($arOldData['option_2']!=$data[$prefix.'option_2'])
					)
				)
				{
					//Что-то изменилось, значить надо проверить по шагам
					if(($arOldData['module']!=$data[$prefix.'module'])||($arOldData['type']!=$data[$prefix.'type']))
					{
						//Сменился модуль или тип, значит сменился и тип, делаем перемещение
						$to=array(
							'title'=>$data[$prefix.'title'],
							'type'=>$data[$prefix.'type'],
							'module'=>$data[$prefix.'module'],
							'default'=>$sValue,
						);
						$from=$arOldData;
						$this->MoveField($from,$to);
						$data[$prefix.'type']=$to['type'];
						$data[$prefix.'module']=$to['module'];
						//Хак, т.к. при переносе меняется автоматически имя
						$data[$prefix.'title']=$to['title'];
						$arOldData['title']=$to['title'];
						$arOldData['default']=$to['default'];
					}
					//Проверяем имя
					if($arOldData['title']!=$data[$prefix.'title'])
					{
						$to=array(
							'title'=>$arOldData['title'],
							'type'=>$data[$prefix.'type'],
							'module'=>$data[$prefix.'module'],
							'default'=>$sValue,
						);
						$this->RenameField($to,$data[$prefix.'title']);
					}
					//Проверяем тип поля в базе и переданный тип поля
					$arFields=$this->_GetTableFields($data[$prefix.'type']);
					$sOldType=$arFields['ext_'.$data[$prefix.'title']]['Type'];
					if($arFields['ext_'.$data[$prefix.'title']]['Size']>0)
					{
						$sOldType.='('.$arFields['ext_'.$data[$prefix.'title']]['Size'].')';
					}
					if($sType!=$sOldType)
					{
						$ks_db->UpdateColumnType($data[$prefix.'type'],'ext_'.$data[$prefix.'title'],$sType);
						if($sType=='text') $sValue='';
					}
					//Проверяем значение по умолчанию
					if($arOldData['default']!=$data[$prefix.'default'])
					{
						$to=array(
							'title'=>$arOldData['title'],
							'type'=>$data[$prefix.'type'],
							'module'=>$data[$prefix.'module'],
							'default'=>$sValue,
						);
						$this->UpdateDefault($to);
					}
				}
				else
				{
					$fields=$this->GetFieldsList("",$data[$prefix.'type']);
					if(!in_array('ext_'.$data[$prefix.'title'],array_keys($fields)))
					{
						$query="ALTER TABLE ".PREFIX.$data[$prefix.'type']." ADD COLUMN ext_".$data[$prefix.'title']." $sType NULL DEFAULT '".$sValue."'";
						if(!$ks_db->query($query))
						{
							throw new CError("MAIN_FIELD_ADDED_ERROR");
						}
					}
				}
			}
		}
		catch(CError $e)
		{
			$ks_db->rollback();
			throw $e;
		}
		catch(Exception $e)
		{
			$ks_db->rollback();
			throw new CError($e->GetMessage(),252);
		}
		$ks_db->commit();
		return $id;
	}

	function GetTypes()
	{
		$dir=MODULES_DIR.'/main/fields/';
		$arResult=array();
		if (is_dir($dir))
		{
    		if ($dh = opendir($dir))
    		{
        		while (($file = readdir($dh)) !== false)
        		{
        			if((filetype($dir.$file)=='dir')&&
        			(file_exists($dir.$file.'/desc.php')))
        			{
        				$arDesc=array();
        				include($dir.$file.'/desc.php');
        				$arResult[$file]=$arDesc['title'];
        			}
        		}
        		closedir($dh);
    		}
		}
		return $arResult;
	}

	/**
	 * Функция возвращает список полей указанного типа
	 *
	 * @version 2.2
	 * Исправлен возврат
	 * Добавлен кэш для экономии запросов при инициализации
	 *
	 * @version 2.3
	 * Добавлен необязательный параметр $script, указывающий на имя обработчика полей
	 *
	 * @param string $module Имя модуля
	 * @param string $type Тип записи
	 * @param string $script Имя обработчика полей, которые выбираются для списка
	 * @return array
	 */
	function GetModuleFields($module, $type, $script = false)
	{
		global $ks_db;
		if (is_array(self::$arUserFields) && array_key_exists($module,self::$arUserFields) && array_key_exists($type,self::$arUserFields[$module]) && !$script)
		{
			return self::$arUserFields[$module][$type];
		}
		else
		{
			$arResult=array();
			$query = "SELECT * FROM " . PREFIX . $this->sTable . " WHERE module = '" . $module . "' AND type = '" . $type . "'";
			if ($script)
				$query .= " AND script = '" . $script . "'";
			$ks_db->query($query);
			if($ks_db->num_rows()>0)
			{
				while($arRow=$ks_db->get_row())
				{
					$arResult[$arRow['id']]=$arRow;
				}
			}
			if (!$script)
				self::$arUserFields[$module][$type]=$arResult;
			return $arResult;
		}
	}

	function Delete($id)
	{
		global $ks_db;
		$arField=$this->GetRecord(array('id'=>$id));
		if($arField)
		{
			try
			{
				$query="ALTER TABLE ".PREFIX.$arField['type'].' DROP COLUMN ext_'.$arField['title'];
				$ks_db->query($query);
			}
			catch(Exception $e)
			{

			}
			$query="DELETE FROM ".PREFIX.$this->sTable." WHERE id='".intval($id)."'";
			$ks_db->query($query);
		}
	}
}

/**
 * Класс работающий со значенниями дополнительных полей. Избегайте использовать
 * этот класс т.к. в будущем он будет исключен
 * @deprecated 2.4 - 04.06.2009
 */
class CFieldsValues extends CObject
{
	var $sTable;
	var $obField;	/*!<объект класса CField*/

	function __construct($sTable='catsubcat_fields_values')
	{
		parent::__construct($sTable);
		$this->sTable='catsubcat_fields_values';
		$this->arFields=Array(
			'id',
			'record_id',
			'field_id',
			'value',
			'value_id',
		);
		$this->obField=new CFields();
	}

	/**
	 * Метод получает значение одного поля. Метод изменен, с предположением что в будущем будет удален
	 * Теперь получение данных идет из таблицы где находится само поле
	 */
	function GetValue($recId,$fieldId)
	{
		echo "Deprecated since 2.4 Not for use in 2.5";
		die();
	}

	/**
	 * Избегайте использовать эту функцию! В будущем планируется её исключение.
	 */
	function GetValues($recId,$fieldIds)
	{
		echo "Deprecated since 2.4 Not for use in 2.5";
		die();
	}

	/**
	 * Метод выполняет обработку данных в соответсвие с указанным типом данных
	 */
	static function ParseValue($arField,$value,$prefix='CSC_')
	{
		global $ks_db;
		if($arField)
		{
			if (file_exists(MODULES_DIR.'/main/fields/'.$arField['script'].'/save.php'))
			{
				include MODULES_DIR.'/main/fields/'.$arField['script'].'/save.php';
			}
			else
			{
				$sValue=$value;
			}
			return $sValue;
		}
		return false;
	}

	/**
	 * Избегайте использовать эту функцию! В будущем планируется её исключение.
	 */
	function SetValue($recId,$fieldId,$value)
	{
		global $ks_db;
		$arField=$this->obField->GetRecord(array('id'=>$fieldId));
		if($arField)
		{
			if (file_exists(MODULES_DIR.'/main/fields/'.$arField['script'].'/save.php'))
			{
				include MODULES_DIR.'/main/fields/'.$arField['script'].'/save.php';
			}
			else
			{
				$sValue=$value;
			}
			$query="UPDATE ".PREFIX.$arField['type']." SET ext_".$arField['title']."='".$sValue."' WHERE id=".intval($recId);
			$res=$ks_db->query($query);
			return $ks_db->AffectedRows()>0;
		}
		return false;
	}
}
