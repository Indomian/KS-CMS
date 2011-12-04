<?php
/**
 * @filesource  main/libs/db/mysqli_innodb.php
 * @author BlaDe39 <blade39@kolosstudio.ru>, D. Konev
 * @since 2.7
 *
 * В файле находится класс обеспечивающий работу с базой данных с использованием интерфейса mysqli
 */

if( !defined('KS_ENGINE') )  die("Hacking attempt!");

require_once 'class.CDBInterface.php';

final class mysqli_innodb extends CDBInterface
{
	private $obDB; 			/**объект описывающий соедение с БД*/
	private $connected = false;			/**<флаг подключения к базе данных*/
	private $query_num = 0;				/**<количество выполненных запросов*/
	private $mysql_error = '';			/**<ошибка работы db*/
	private $mysql_error_num = 0;			/**<номер ошибки db*/
	private $mysql_time_taken = 0;			/**<время которое ушло на исполнение запросов*/
	private $obResult = false;				/**<результат запроса*/
	private $iVersion;				/**<Версия mysql*/
	private $arColumnTypes;		/**<Массив со списком доступных типов полей*/

	/**Конструктор класса.
	 * \param $debug - устанавливает уровень отладки
	 * \since 1.2
	 * \author blade39 <blade39@kolosstudio.ru>
	 */
	function __construct($debug=0)
	{
		$this->iDebug=$debug;
		$this->iVersion='';
		$this->arColumnTypes=array('char','varchar','int','float','text','enum');
	}

	/**Деструктор класса. Очищает память, закрывает соединение к БД (если оно было открыто ранее).*/
	function __destruct()
	{
		if($this->connected)
			$this->close();
	}

	/**
	 * Функция открывает подключение к базе данных. В случае ошибки подключения выводит критическую ошибку базы данных и останавливает
	 * выполнение скрипта. Возвращаемые значения true - в случае успеха. False - если не удалось подключиться.
	 * @param $ks_db_user - имя пользоваталя;
	 * @param $ks_db_pass - пароль к базе данных;
	 * @param $ks_db_name - имя базы данных (непонятно почему не на первом месте);
	 * @param $ks_db_location - размещение базы данных (обычно localhost);
	 * @param $show_error - флаг останова по ошибке, обычно 1, при значении 0 скрипт продолжает работу.
	 * @return true если удалось установить соединение
	 */
	function Connect($ks_db_user, $ks_db_pass, $ks_db_name, $ks_db_location = 'localhost', $show_error=1)
	{
		//пробуем подключиться
		$this->obDB=new mysqli($ks_db_location, $ks_db_user, $ks_db_pass, $ks_db_name);
		if($this->obDB->connect_error)
			throw new CDBError($this->obDB->connect_error, $this->obDB->connect_errno);
		//получаем номер версии
		$this->iVersion = $this->obDB->server_version;
		//успешно законнектились
		$this->connected = true;
		//просим данные в удобной кодировке
		$this->Query("SET NAMES 'UTF8'");
		return true;
	}

	/**
	 * Метод выполняет автоматическое подключение к БД по необходимости
	 */
	function AutoConnect()
	{
		if(!$this->connected) $this->Connect(DBUSER, DBPASS, DBNAME, DBHOST);
	}

	/**
	 * Метод выполняет SQL запрос к базе данных.
	 * Возращает id результата.
	 * @param $query - sql запрос;
	 * @param $show_error - флаг вывода ошибок.
	 * @return object - объект описывающий результат запроса
	 */
	function Query($query, $show_error=true)
	{
		$time_before = $this->GetRealTime();

		if(!$this->connected) $this->Connect(DBUSER, DBPASS, DBNAME, DBHOST);

		try
		{
			$this->obResult = new CMysqliResult($this->obDB->query($query));
		}
		catch(CDBError $e)
		{
			$this->mysql_error = $this->obDB->error;
			$this->mysql_error_num = $this->obDB->errno;
			throw new CDBError($this->mysql_error, $this->mysql_error_num, $query);
		}

		/*Запись запроса в лог, запись времени его исполнения в лог*/
		$this->add2log($query,$this->GetRealTime() - $time_before);
		$this->mysql_time_taken += $this->GetRealTime() - $time_before;
		$this->query_num ++;

		return $this->obResult;
	}

	/**
	 * Метод выполняет начало транзакции
	 */
	function Begin()
	{
		$this->obDB->autocommit(false);
	}

	/**
	 * Метод выполняет подтверждение результата транзакции
	 */
	function Commit()
	{
		$this->obDB->commit();
		$this->obDB->autocommit(true);
	}

	/**
	 * Метод выполняет откат последних изменений
	 */
	function Rollback()
	{
		$this->obDB->rollback();
		$this->obDB->autocommit(true);
	}

	/**
	 * Возвращает одну строку в виде ассоциативного массива из запроса.
	 * В качестве параметра можно передать код запроса. По умолчанию используется
	 * код последненго запроса.
	 * @param $obResult -- целое число, дескриптор результата.
	 * */
	function GetRow($obResult = '')
	{
		if ($obResult == '')
			$obResult = $this->obResult;
		return $obResult->Get()->fetch_assoc();
	}

	/**
	 * Возвращает одну строку в виде индексированного массива из запроса.
	 * В качестве параметра можно передать код запроса. По умолчанию используется
	 * код последненго запроса.
	 * @param $obResult -- целое число, дескриптор результата.
	 * */
	function GetArray($obResult = '')
	{
		if ($obResult == '')
			$obResult = $this->obResult;

		return $obResult->Get()->fetch_row();
	}

	/**
	 * Возвращает количество строк в результате запроса.
	 * В качестве параметра можно передать код запроса.
	 * По умолчанию используется код последненго запроса.
	 * @param $obResult -- целое число, дескриптор результата.
	 * */
	function NumRows($obResult = '')
	{
		if ($obResult == '')
			$obResult = $this->obResult;

		return $obResult->Get()->num_rows;
	}

	/**
	 * Возвращает номер последней вставленной записи. В качестве
	 * параметра можно передать код запроса.
	 * По умолчанию используется код последненго запроса.
	 * */
	function InsertId()
	{
		return $this->obDB->insert_id;
	}

	/**
	 * Метод возвращает количество строк затронутых при выполнении последней операции.
	 * @since версия класса 1.1, 25.11.2008
	 * @author blade39 <blade39@kolosstudio.ru>
	 * @return количество затронутых строк или -1 в случае ошибки.
	 */
	function AffectedRows()
	{
		return $this->obDB->affected_rows;
	}

	/**
	 * Возвращает список полей результата запроса.
	 * В качестве параметра можно передать код запроса.
	 * По умолчанию используется код последненго запроса.
	 * @param $obResult -- целое число, дескриптор результата.
	 * */
	function GetResultFields($obResult = '')
	{
		if ($obResult == '')
			$obResult = $this->obResult;
		return $obResult->Get()->fetch_fields();
   	}

	/**
	 * Метод преобразует переданную строку в sql безопасный вид. Производит предварительное
	 * отсечение слэшей, если включен magic_quotes.
	 * Включил дополнительную проверку переданной строки (mysql_real_escape_string)
	 * после отчистки слэшей от magic_quotes_gpc
	 * @param $source -- строка, данные требующие обработки.
	 * */
	function SafeSQL($source)
	{
		if(ini_get('magic_quotes_gpc')==1)
			$source=stripslashes($source);
		if(!$this->obDB) $this->AutoConnect();
		return $this->obDB->real_escape_string($source);
	}

	/**
	 * Освобождает память выделенную под результат запроса.
	 * В качестве параметра можно передать код запроса.
	 * По умолчанию используется код последненго запроса.
	 * @param $query_id -- целое число, дескриптор результата.
	 * */
	function Free($obResult = '')
	{
		if ($obResult == '')
			$obResult = $this->obResult;
		$this->obResult->Free();
	}

	/**
	 * Закрывает соединение с базой данных.
	 */
	function Close()
	{
		if($this->obResult)
			$this->obResult->Free();
		$this->obDB->close();
		$this->connected=false;
	}

	/**
	 * Метод возвращает список таблиц текущей базы данных
	 * @param $bGetFields - флаг указывающий необходимо ли выбирать все поля таблиц
	 * @return array - массив таблиц в базе данных
	 */
	public function ListTables($bGetFields=false)
	{
		//Получаем список таблиц
		$arResult=array();
		$obResult=$this->query('SHOW TABLES');
		while($arTable = $obResult->GetRow())
			$arResult[current($arTable)]=current($arTable);
		$obResult->Free();
		if($bGetFields)
			foreach($arResult as $sTable)
			{
				$obResult=$this->query('DESCRIBE '.$sTable);
				if($obResult->NumRows()>0)
				{
					$arResult[$sTable]=array();
					while($arRow=$obResult->GetRow())
						$arResult[$sTable][$arRow['Field']]=$arRow;
				}
			}
		return $arResult;
	}

	/**
	 * Метод опрашивает таблицу, и получает список её полей.
	 * @param $sTable - таблица для которой надо получить поля
	 * @param $sPrefix - префикс названия полей которые надо получить
	 * @return array - массив полей таблицы с описанием каждого поля
	 */
	public function GetTableFields($sTable,$sPrefix='')
	{
		$arResult=array();
		$obResult=$this->query('DESCRIBE '.$sTable);
		if($obResult->NumRows()>0)
		{
			while($arRow=$obResult->GetRow())
			{
				if($sPrefix!='')
				{
					if(preg_match('#^'.$sPrefix.'#i',$arRow['Field']))
						$arResult[$arRow['Field']]=$arRow;
				}
				else
					$arResult[$arRow['Field']]=$arRow;
			}
			return $arResult;
		}
		throw new CDBError('SYSTEM_TABLE_NOT_FOUND',1,$sTable);
	}

	/**
	 * Метод заполняет массив описания поля значения по умолчанию если они не были указаны
	 * @param unknown_type $arField
	 */
	protected function FillFieldArray($arField)
	{
		if(is_string($arField))
			return array(
				'Field'	=>$arColumn,
				'Type'	=> 	'char(255)',
				'Null'	=>	'NO',
				'Key'	=>	'',
				'Default'=>	'',
				'Extra'	=>	'',
			);
		elseif(is_array($arField))
		{
			if(!isset($arField['Field'])) throw new CError('SYSTEM_FIELD_TITLE_REQUIRED');
			if(!isset($arField['Type'])) $arField['Type']='char(255)';
			if(!isset($arField['Null'])) $arField['Null']='NO';
			if(!isset($arField['Key'])) $arField['Key']='';
			if(!isset($arField['Default'])) $arField['Default']='';
			if(!isset($arField['Extra'])) $arField['Extra']='';
			return $arField;
		}
		else
			throw new CError('SYSTEM_WRONG_FIELD_ARRAY');
	}

	/**
	 * Метод выполняет добавление таблицы в базу данных mysql
	 * @param $sTable string - название таблицы
	 * @param $arTableStructure array - массив описывающий таблицу
	 * @return true - если удалось выполнить запросы на создание БД
	 */
	function AddTable($sTable,$arTableStructure)
	{
		if(!is_array($arTableStructure)) return false;
		$sQuery='CREATE TABLE IF NOT EXISTS '.PREFIX.$sTable;
		$arFields=array();
		$arFullText=array();
		foreach($arTableStructure as $sField=>$arFieldParams)
		{
			$arFieldParams=$this->FillFieldArray($arFieldParams);
			$sLine='`'.$sField.'` '.$arFieldParams['Type'].' '.($arFieldParams['Null']=='NO'?'NOT NULL':'NULL').' ';
			if($arFieldParams['Extra']!='auto_increment')
				$sLine.=($arFieldParams['Default']!=''?" DEFAULT '".$arFieldParams['Default']."' ":" DEFAULT ''");
			else
				$sLine.=$arFieldParams['Extra'];
			$sLine.=($arFieldParams['Key']=='PRI'?' PRIMARY KEY':'');
			$arFields[]=$sLine;
			if($arFieldParams['Extra']=='fulltext')
				$arFullText[]=$arFieldParams['Field'];
		}
		if(count($arFullText)>0)
			$arFields[]=' FULLTEXT INDEX ('.join(',',$arFullText).')';
		if(count($arFields)>0)
			$sQuery.='('.join(',',$arFields).') TYPE=InnoDB';
		try
		{
			$this->query($sQuery);
			return true;
		}
		catch(CError $e)
		{
			throw new CDBError("DB_MYSQL_TABLE_CREATE_ERROR",$e->getCode(),$e->getMessage());
		}
	}

	/**
	 * Метод выполняет добавление колонки в таблицу
	 * @param $sTable string - название таблицы в которую необходимо добавить колонку
	 * @param $arColum mixed - строка с названием колонки или массив описывающий колонке
	 * @return true - если удалось выполнить запросы на создание колонки
	 */
	public function AddColumn($sTable,$arColumn)
	{
		//Если передали число - выходим
		if(!is_array($arColumn) && !is_string($arColumn)) return false;
		$arColumn=$this->FillFieldArray($arColumn);
		try
		{
			$query="ALTER TABLE ".PREFIX.$sTable." ADD COLUMN `".
				$arColumn['Field'].'` '.
				$arColumn['Type'].' '.
				($arColumn['Null']=='NO'?'NOT NULL':'NULL').' ';
			if($arColumn['Extra']!='auto_increment')
				$query.=($arColumn['Default']!=''?" DEFAULT '".$arColumn['Default']."' ":" DEFAULT ''");
			else
				$query.=$arColumn['Extra'];
			$query.=($arColumn['Key']=='PRI'?' PRIMARY KEY':'');
			$this->query($query);
			if($arColumn['Extra']=='fulltext')
			{
				$query="ALTER TABLE ".PREFIX.$sTable.' ADD FULLTEXT ('.$arColumn['Field'].')';
				$this->query($query);
			}
			if($arColumn['Key']=='UNI')
			{
				$query="ALTER TABLE ".PREFIX.$sTable.' ADD UNIQUE ('.$arColumn['Field'].')';
				$this->query($query);
			}
		}
		catch (CError $e)
		{
			throw new CDBError("DB_MYSQL_COLUMN_CREATE_ERROR",$e->getCode(),$e->getMessage());
		}
		return true;
	}

	/**
	 * Метод выполняет обновление типа данных указанного поля
	 * @param $sTable string - название таблицы
	 * @param $sColumn string - название поля
	 * @param $sType string - новый тип поля
	 * @return true - если удалось выполнить запросы на изменение типа колонки
	 */
	public function UpdateColumnType($sTable,$sColumn,$sType)
	{
		//Если передали число - выходим
		if(is_numeric($sType)) return false;
		//Если передали строку создаем поле по умолчанию
		try
		{
			$arColumn=$this->DescribeColumn($sTable,$sColumn);
			if(!$arColumn) return false;
			if($arColumn['Type']==$sType) return false;
			if(strpos($sType,'int')!==false || strpos($sType,'float')!==false)
				$arColumn['Default']="0";
			if($sType=='text') $arColumn['Default']='';
			$query="ALTER TABLE ".PREFIX.$sTable." CHANGE COLUMN $sColumn $sColumn $sType ".($arColumn['Null']=='NO'?'NOT NULL':'NULL')." default '".$arColumn['Default']."'";
			$this->query($query);
		}
		catch (CError $e)
		{
			throw new CError("DB_MYSQL_COLUMN_TYPE_ERROR",$e->getCode(),$e->getMessage());
		}
		return true;
	}

	/**
	 * Метод выполняет обновление колонки указанной таблицы
	 * @param $sTable string - название таблицы
	 * @param $sColumn string - название поля
	 * @param $arFieldParams array - массив описывающий как необходимо переделать поле
	 * @return true - если удалось выполнить запросы на изменение колонки
	 */
	public function UpdateColumn($sTable,$sColumn,$arFieldParams)
	{
		//Если передали строку создаем поле по умолчанию
		try
		{
			$arFieldParams=$this->FillFieldArray($arFieldParams);
			$arColumn=$this->DescribeColumn($sTable,$sColumn);
			if($arColumn['Key']=='UNI')
				//Есть ключ уникальности колонки
				if($arFieldParams['Key']!=$arColumn['Key'])
					//Ключи не совпадают, надо удалить ключ уникальности
					$this->query("ALTER TABLE ".PREFIX.$sTable." DROP INDEX ".$sColumn);
			$query="ALTER TABLE ".PREFIX.$sTable." CHANGE COLUMN `".$sColumn.'` `'.
				$arFieldParams['Field'].'` '.
				$arFieldParams['Type'].' '.
				($arFieldParams['Null']=='NO'?'NOT NULL':'NULL').' ';
			if($arFieldParams['Extra']!='auto_increment')
				$query.=($arFieldParams['Default']!=''?" DEFAULT '".$arFieldParams['Default']."' ":" DEFAULT ''");
			else
				$query.=$arFieldParams['Extra'];
			$this->query($query);
			if($arFieldParams['Extra']=='fulltext')
			{
				$query="ALTER TABLE ".PREFIX.$sTable.' ADD FULLTEXT ('.$arFieldParams['Field'].')';
				$this->query($query);
			}
			if($arFieldParams['Key']=='UNI')
			{
				$query="ALTER TABLE ".PREFIX.$sTable.' ADD UNIQUE ('.$arFieldParams['Field'].')';
				$this->query($query);
			}
		}
		catch (CError $e)
		{
			throw new CError("DB_MYSQL_COLUMN_UPDATE_ERROR",$e->getCode(),$e->getMessage());
		}
		return true;
	}

	/**
	 * Метод выполняет получение информации о колонке таблицы
	 * @param $sTable string - название таблицы
	 * @param $sColumn string - название поля
	 * @return mixed - массив описывающий поле или false если выполнить запрос не удалось
	 */
	protected function DescribeColumn($sTable,$sColumn)
	{
		if(!$sTable) return false;
		/* Чтение всех полей таблицы */
		$query="SHOW COLUMNS FROM " . PREFIX . $sTable." WHERE Field='$sColumn'";
		$obResult=$this->query($query);
		if($obResult->NumRows()>0)
			/* Формирование массива с параметрами полей - Type (тип данных), Size (размер в байтах) */
			if($arField = $obResult->GetRow())
				return $arField;
		return false;
	}

	/**
	 * Метод выполняет удаление таблицы или таблиц переданных методу
	 * @param $arTables mixed - список или одна таблица которые требуется удалить
	 */
	public function DeleteTables($arTables)
	{
		if(!is_array($arTables)) $arTables=array($arTables);
		$arDBTables=$this->ListTables();
		$arTablesToDelete=array();
		foreach($arTables as $sTable)
			if(in_array(PREFIX.$sTable,$arDBTables) && IsTextIdent($sTable)) $arTablesToDelete[]=PREFIX.$sTable;
		if(count($arTablesToDelete)>0)
		{
			$query="DROP TABLE IF EXISTS ".join(',',$arTablesToDelete);
			$this->query($query);
		}
	}

	/**
	 * Метод выполняет удаление колонки из таблицы
	 * @param $sTable
	 * @param $sColumn
	 * @return true если удалось выполнить удаление колонки
	 */
	public function DeleteColumn($sTable,$sColumn)
	{
		//Если передали строку создаем поле по умолчанию
		try
		{
			$query="ALTER TABLE ".PREFIX.$sTable." DROP COLUMN ".$sColumn;
			$this->query($query);
		}
		catch (CError $e)
		{
			throw new CError("DB_MYSQL_COLUMN_DROP_ERROR",$e->getCode(),$e->getMessage());
		}
		return true;
	}

	/**
	 * Метод выполняет переименование одной таблицы в другую
	 * @param $sTable
	 * @param $sNewName
	 */
	public function RenameTable($sTable,$sNewName)
	{
		//Если передали строку создаем поле по умолчанию
		try
		{
			$query="ALTER TABLE ".PREFIX.$sTable." RENAME TO ".$sNewName;
			$this->query($query);
		}
		catch (CError $e)
		{
			throw new CError("DB_MYSQL_TABLE_RENAME_ERROR",$e->getCode(),$e->getMessage());
		}
		return true;
	}

	/**
	 * Метод возвращает сколько времени заняло выполнение запросов к MySQL
	 */
	function GetTimeTaken()
	{
		return $this->mysql_time_taken;
	}

	/**
	 * Метод возвращает количество запросов отправленых к MySQL
	 */
	function GetQueriesCount()
	{
		return $this->query_num;
	}
}

/**
 * Класс обеспечивает работу с результатами запросов к базе данных InnoDB через mysqli
 * @author blade39 <blade39@kolosstudio.ru>
 * @since 2.7
 */
class CMysqliResult extends CDBResult
{
	private $obResult;
	private $bResult;

	function __construct($result)
	{
		$this->bResult=false;
		if($result===true)
		{
			$bResult=true;
			$this->bFree=true;
		}
		elseif($result instanceof MySQLi_Result)
		{
			$this->obResult=$result;
			$this->bFree=false;
		}
		else
			throw new CDBError('SYSTEM_WRONG_DB_RESULT');
	}

	function __destruct()
	{
		if($this->obResult && !$this->bFree)
		{
			$this->obResult->free();
			$this->bFree=true;
		}
	}

	function Free()
	{
		if(!$this->bFree)
		{
			$this->obResult->free();
			$this->bFree=true;
		}
	}

	function Get()
	{
		if($this->bResult) return $this->bResult;
		if($this->bFree) return NULL;
		return $this->obResult;
	}

	function GetRow()
	{
		if(!$this->bResult && !$this->bFree)
			return $this->obResult->fetch_assoc();
		return NULL;
	}

	function GetArray()
	{
		if(!$this->bResult && !$this->bFree)
			return $this->obResult->fetch_row();
		return NULL;
	}

	function NumRows()
	{
		if(!$this->bResult && !$this->bFree)
			return $this->obResult->num_rows;
		return 0;
	}
}