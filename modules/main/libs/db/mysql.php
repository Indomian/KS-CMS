<?php
/*
KS ENGINE

Класс работы с БД
*/

if( !defined('KS_ENGINE') )  die("Hacking attempt!");

require_once 'class.CDBInterface.php';
/**
Класс выполняет роль интерфейса к базе данных типа mysql. Логгирует все SQL запросы
при работе в режиме отладки, также логирует время исполнения этих запросов. Добавлена функция вставки информационных
строк в лог запросов, что позволяет отделять из каких методов произведен вызов того или иного запроса.
\version 1.3
\author dotj <dotj@kolosstudio.ru>
*/
class mysql extends CDBInterface
{
	private $ks_db_id = false; 						/**<Ресурс подключения к базе данных*/
	var $connected = false;				/**<флаг подключения к базе данных*/
	var $query_num = 0;					/**<количество выполненных запросов*/
	var $query_list = array();			/**<список выполненных запросов*/
	var $mysql_error = '';					/**<ошибка работы db*/
	protected $iVersion;				/**<Версия mysql*/
	var $mysql_error_num = 0;			/**<номер ошибки db*/
	var $mysql_extend = "MySQL";		/**<фиг знает*/
	var $MySQL_time_taken = 0;			/**<время которое ушло на исполнение запросов*/
	var $query_id = false;					/**<номер запроса*/

	protected $arBackUpData;						/**<массив содержащий данные которые будут подвергнуты изменениям, необходим для сохранения данных.*/
	protected $iBegin=0;						/**<обозначет количество вложенных вызовов системы отката*/
	protected $arRequests;			/**<В массиве храняться тексты всех запросов к БД в рамках одного вызова (если включен режим отладки) \since 1.2*/
	protected $iDebug;				/**<Флаг указывает на необходимость сохранения запросов к бд \since 1.2*/
	protected $arColumnTypes;		/**<Массив со списком доступных типов полей*/

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
		{
			$this->close();
		}
	}

	/**Функция открывает подключение к базе данных. В случае ошибки подключения выводит критическую ошибку базы данных и останавливает
	выполнение скрипта. Возвращаемые значения true - в случае успеха. False - если не удалось подключиться.
	@param $ks_db_user -- имя пользоваталя;
	@param $ks_db_pass -- пароль к базе данных;
	@param $ks_db_name -- имя базы данных (непонятно почему не на первом месте);
	@param $ks_db_location -- размещение базы данных (обычно localhost);
	@param $show_error -- флаг останова по ошибке, обычно 1, при значении 0 скрипт продолжает работу.*/
	function connect($ks_db_user, $ks_db_pass, $ks_db_name, $ks_db_location = 'localhost', $show_error=1)
	{
		//пробуем подключиться
		if(!$this->ks_db_id = @mysql_connect($ks_db_location, $ks_db_user, $ks_db_pass)) {
			if($show_error == 1) {
				throw new CDBError(mysql_error(), mysql_errno());
			} else {
				return false;
			}
		}
		//пробуем выбрать бд для работы
		if(!@mysql_select_db($ks_db_name, $this->ks_db_id)) {
			if($show_error == 1) {
				throw new CDBError(mysql_error(), mysql_errno());
			} else {
				return false;
			}
		}
		//получаем номер версии
		$this->iVersion = mysql_get_server_info();

		//Убрал для принудительной обработки всего в утф
		//if (version_compare($this->mysql_version, '4.1', ">="))
		//успешно законнектились
		$this->connected = true;
		//просим данные в удобной кодировке
		$this->query("SET NAMES 'UTF8'");

		return true;
	}

	/**
	Метод выполняет SQL запрос к базе данных.
	Возращает id результата. В случае работы в режиме отката,
	записывает последние изменения в базе данных в кэш.
	@param $query -- sql запрос;
	@param $show_error -- флаг вывода ошибок.
	*/
	function query($query, $show_error=true)
	{
		$time_before = $this->get_real_time();

		if(!$this->connected) $this->connect(DBUSER, DBPASS, DBNAME, DBHOST);

		/*проверяем включенность режима записи операций, если работает производим
		 * выборку в зависимости от типа операции
		(добавление, обновление)*/
		if($this->iBegin>0)
		{
			$matches=array();
			$arRow=false;
			//echo $query;
			if(preg_match("#^UPDATE ([\w\d_]+) SET *((`?[\w\d`]+` ?= ?'[^']*',? *)+) *WHERE ([`\w\d= ',\(\)]+)#si",$query,$matches)>0)
			{
				$arRow=array();
				$arRow['OP']='UPDATE';
				$arRow['TABLE']=$matches[1];
				$arRow['WHERE']=$matches[4];
				$arRow['QUERY']=$query;
				$myquery="SELECT * FROM ".$arRow['TABLE']." WHERE ".$arRow['WHERE'];
				if(!($this->query_id = @mysql_query($myquery, $this->ks_db_id) ))
				{
					$this->mysql_error = mysql_error();
					$this->mysql_error_num = mysql_errno();
					if($show_error)
					{
						throw new CDBError("Ошибка работы системы отката:\n".$this->mysql_error, $this->mysql_error_num, "запрос:".$query."\n выборка из базы".$myquery."\n запрос на обновление".$arRow['QUERY']);
					}
				}
				while($arResRow=$this->get_row())
				{
					$arRow['DATA'][]=$arResRow;
				}
			}
			if(preg_match('#^INSERT INTO ([\w\d_]+)#',$query,$matches)>0)
			{
				$arRow=array();
				$arRow['OP']='INSERT INTO';
				$arRow['TABLE']=$matches[1];
				$arRow['QUERY']=$query;
			}
		}

		if(!($this->query_id = @mysql_query($query, $this->ks_db_id) ))
		{
			$this->mysql_error = mysql_error();
			$this->mysql_error_num = mysql_errno();
			throw new CDBError($this->mysql_error, $this->mysql_error_num, $query);
		}

		if($this->iBegin>0)
		{
			if($arRow['OP']=='INSERT INTO')
			{
				$arRow['DATA']=$this->insert_id();
			}
			if(is_array($arRow))
			{
				array_push($this->arBackUpData[$this->iBegin],$arRow);
			}
		}
		/*Запись запроса в лог, запись времени его исполнения в лог*/
		$this->add2log($query,$this->get_real_time() - $time_before);
		$this->MySQL_time_taken += $this->get_real_time() - $time_before;
		$this->query_num ++;
		return $this->query_id;
	}

	/**Отменяет все последние изменения в базе данных. Внимание! Если с БД работает несколько человек (особенно на запись) присутствует вероятность
	искажения данных. По окончанию работы очищает массив последних операций, снимает флаг записи операций.
	@param $show_error -- флаг отображения ошибок
	\author blade39 <blade39@kolosstudio.ru>
	\since 1.0
	*/
	function rollback($show_error=false)
	{
		if(($this->iBegin>0)&&is_array($this->arBackUpData[$this->iBegin])&&(count($this->arBackUpData[$this->iBegin])>0))
		{
			foreach($this->arBackUpData[$this->iBegin] as $arRow)
			{
				/*проверяем что делали, если добавляли, то надо удалить*/
				if($arRow['OP']=='INSERT INTO')
				{
					$query="DELETE FROM ".$arRow['TABLE']." WHERE id=".$arRow['DATA'];
					if(!($this->query_id = @mysql_query($query, $this->ks_db_id) ))
					{
						$this->mysql_error = mysql_error();
						$this->mysql_error_num = mysql_errno();
						if($show_error) {
							throw new CDBError($this->mysql_error, $this->mysql_error_num, $query);
						}
					}
				}
				if(($arRow['OP']=='UPDATE')&&is_array($arRow['DATA'])&&(count($arRow['DATA'])>0))
				{
					foreach($arRow['DATA'] as $arDataRow)
					{
						$sSet="";
						foreach ($arDataRow as $key=>$value)
						{
							$sSet.="`$key`='$value',";
						}
						$sSet=trim($sSet,",");
						$query="UPDATE ".$arRow['TABLE']." SET ".$sSet." WHERE id=".$arDataRow['id'];
						//echo $query;
						if(!($this->query_id = @mysql_query($query, $this->ks_db_id) ))
						{
							$this->mysql_error = mysql_error();
							$this->mysql_error_num = mysql_errno();
							if($show_error) {
								throw new CDBError($this->mysql_error, $this->mysql_error_num, $query);
							}
						}
					}
				}
			}
			unset($this->arBackUpData[$this->iBegin]);
			$this->iBegin--;
			return true;
		}
	}

	/**
	 * Возвращает одну строку в виде ассоциативного массива из запроса.
	 * В качестве параметра можно передать код запроса. По умолчанию используется
	 * код последненго запроса.
	 * @param $query_id -- целое число, дескриптор результата.
	 * */
	function get_row($query_id = '')
	{
		if ($query_id == '') $query_id = $this->query_id;

		return mysql_fetch_assoc($query_id);
	}

	/**
	 * Возвращает одну строку в виде индексированного массива из запроса.
	 * В качестве параметра можно передать код запроса. По умолчанию используется
	 * код последненго запроса.
	 * @param $query_id -- целое число, дескриптор результата.
	 * */
	function get_array($query_id = '') {
		if ($query_id == '') $query_id = $this->query_id;

		return mysql_fetch_array($query_id);
	}

	/**
	 * Пока без документации
	 * @deprecated 2.5.4 - 17.02.2010
	 * */
	function super_query($query, $multi = false)
	{
		throw new CError('DEPRECATED METHOD CALL');
	}

	/**
	 * Возвращает количество строк в результате запроса.
	 * В качестве параметра можно передать код запроса.
	 * По умолчанию используется код последненго запроса.
	 * @param $query_id -- целое число, дескриптор результата.
	 * */
	function num_rows($query_id = '')
	{
		if ($query_id == '') $query_id = $this->query_id;

		return @mysql_num_rows($query_id);
	}

	/**
	 * Возвращает номер последней вставленной записи. В качестве
	 * параметра можно передать код запроса.
	 * По умолчанию используется код последненго запроса.
	 * */
	function insert_id()
	{
		return @mysql_insert_id($this->ks_db_id);
	}

	/**
	 * Метод возвращает количество строк затронутых при выполнении последней операции.
	 * @since версия класса 1.1, 25.11.2008
	 * @author blade39 <blade39@kolosstudio.ru>
	 * @return количество затронутых строк или -1 в случае ошибки.
	 */
	function AffectedRows()
	{
		return @mysql_affected_rows($this->ks_db_id);
	}

	/**
	 * Возвращает список полей результата запроса.
	 * В качестве параметра можно передать код запроса.
	 * По умолчанию используется код последненго запроса.
	 * @param $query_id -- целое число, дескриптор результата.
	 * */
	function get_result_fields($query_id = '')
	{
		if ($query_id == '') $query_id = $this->query_id;
		while ($field = @mysql_fetch_field($query_id))
		{
            $fields[] = $field;
		}
		return $fields;
   	}

	/**
	 * Метод преобразует переданную строку в sql безопасный вид. Производит предварительное
	 * отсечение слэшей, если включен magic_quotes.
	 * Включил дополнительную проверку переданной строки (mysql_real_escape_string)
	 * после отчистки слэшей от magic_quotes_gpc
	 * @param $source -- строка, данные требующие обработки.
	 * */
	function safesql( $source )
	{
		if(ini_get('magic_quotes_gpc')==1)
		{
			$source=stripslashes($source);
		}
		if ($this->ks_db_id) return mysql_real_escape_string ($source, $this->ks_db_id);
		return @mysql_escape_string($source);
	}

	/**
	 * Освобождает память выделенную под результат запроса.
	 * В качестве параметра можно передать код запроса.
	 * По умолчанию используется код последненго запроса.
	 * @param $query_id -- целое число, дескриптор результата.
	 * */
	function free( $query_id = '' )
	{
		if ($query_id == '') $query_id = $this->query_id;
		@mysql_free_result($query_id);
	}

	/**
	 * Закрывает соединение с базой данных.
	 */
	function close()
	{
		@mysql_close($this->ks_db_id);
		$this->connected=false;
	}

	/**
	 * Метод возвращает список таблиц текущей базы данных
	 */
	public function ListTables($bGetFields=false)
	{
		//Получаем список таблиц
		$arDB=array();
		$rs=$this->query('SHOW TABLES');
		while ($table = @mysql_fetch_assoc($rs))
		{
			$arDB[]=current($table);
		}
		if($bGetFields)
		{
			$arResult=array();
			foreach($arDB as $key=>$table)
			{
				$res=$this->query('DESCRIBE '.$table);
				if(@mysql_num_rows($res)>0)
				{
					$arTable=array();
					while($arRow=@mysql_fetch_assoc($res))
					{
						$arTable[$arRow['Field']]=$arRow;
					}
					$arResult[$table]=$arTable;
				}
			}
		}
		else
		{
			$arResult=$arDB;
		}
		return $arResult;
	}

	/**
	 * Метод опрашивает таблицу, и получает список её полей.
	 * @param $sTable таблица для которой надо получить поля
	 * @param $sPrefix префикс названия полей которые надо получить
	 */
	public function GetTableFields($sTable,$sPrefix='')
	{
		$res=$this->query('DESCRIBE '.PREFIX.$sTable);
		if(@mysql_num_rows($res)>0)
		{
			$arTable=array();
			while($arRow=@mysql_fetch_assoc($res))
			{
				if($sPrefix!='')
				{
					if(preg_match('#^'.$sPrefix.'#i',$arRow['Field']))
					{
						$arTable[$arRow['Field']]=$arRow;
					}
				}
				else
				{
					$arTable[$arRow['Field']]=$arRow;
				}
			}
			return $arTable;
		}
		throw new CDBError('SYSTEM_TABLE_NOT_FOUND',1,$sTable);
	}

	/**
	 * Метод выполняет добавление таблицы в базу данных mysql
	 */
	function AddTable($sTable,$arTableStructure)
	{
		if(!is_array($arTableStructure)) return false;
		$sQuery='CREATE TABLE IF NOT EXISTS '.PREFIX.$sTable;
		$arFields=array();
		$arFullText=array();
		foreach($arTableStructure as $sField=>$arFieldParams)
		{
			$sLine='`'.$sField.'` '.
				$arFieldParams['Type'].' '.
				($arFieldParams['Null']=='NO'?'NOT NULL':'NULL').' ';
			if($arFieldParams['Extra']!='auto_increment')
			{
				$sLine.=($arFieldParams['Default']!=''?" DEFAULT '".$arFieldParams['Default']."' ":" DEFAULT ''");
			}
			else
			{
				$sLine.=$arFieldParams['Extra'];
			}
			$sLine.=($arFieldParams['Key']=='PRI'?' PRIMARY KEY':'');
			$arFields[]=$sLine;
			if($arFieldParams['Extra']=='fulltext')
			{
				$arFullText[]=$arFieldParams['Field'];
			}
		}
		if(count($arFullText)>0)
		{
			$arFields[]=' FULLTEXT INDEX ('.join(',',$arFullText).')';
		}
		if(count($arFields)>0)
		{
			$sQuery.='('.join(',',$arFields).') TYPE=MyISAM';
		}
		try
		{
			$this->query($sQuery);
		}
		catch(CError $e)
		{
			throw new CError("DB_MYSQL_TABLE_CREATE_ERROR",$e->getCode(),$e->getMessage());
		}
	}

	/**
	 * Метод выполняет добавление колонки в таблицу
	 * Внимание метод изменен! В качестве параметра колнки допускается
	 * передавать только строку или массив в формате возвращаемом
	 * при анализе таблицы mysql
	 */
	public function AddColumn($sTable,$arColumn)
	{
		//Если передали число - выходим
		if(is_numeric($arColumn)) return false;
		//Если передали строку создаем поле по умолчанию
		if(!is_array($arColumn)&&is_string($arColumn))
		{
			$arColumn=array(
				'Field'=>$arColumn,
				'Type'	=> 	'char(255)',
				'Null'	=>	'NO',
				'Key'	=>	'',
				'Default'=>	'',
				'Extra'	=>	'',
			);
		}
		try
		{
			$query="ALTER TABLE ".PREFIX.$sTable." ADD COLUMN `".
				$arColumn['Field'].'` '.
				$arColumn['Type'].' '.
				($arColumn['Null']=='NO'?'NOT NULL':'NULL').' ';
			if($arColumn['Extra']!='auto_increment')
			{
				$query.=($arColumn['Default']!=''?" DEFAULT '".$arColumn['Default']."' ":" DEFAULT ''");
			}
			else
			{
				$query.=$arColumn['Extra'];
			}
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
			throw new CError("DB_MYSQL_COLUMN_CREATE_ERROR",$e->getCode(),$e->getMessage());
		}
		return true;
	}

	/**
	 * Метод выполняет обновление типа данных указанного поля
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
			{
				$arColumn['Default']="0";
			}
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
	 */
	public function UpdateColumn($sTable,$sColumn,$arFieldParams)
	{
		//Если передали строку создаем поле по умолчанию
		try
		{
			$arColumn=$this->DescribeColumn($sTable,$sColumn);
			if($arColumn['Key']=='UNI')
			{
				//Есть ключ уникальности колонки
				if($arFieldParams['Key']!=$arColumn['Key'])
				{
					//Ключи не совпадают, надо удалить ключ уникальности
					$this->query("ALTER TABLE ".PREFIX.$sTable." DROP INDEX ".$sColumn);
				}
			}
			$query="ALTER TABLE ".PREFIX.$sTable." CHANGE COLUMN `".$sColumn.'` `'.
				$arFieldParams['Field'].'` '.
				$arFieldParams['Type'].' '.
				($arFieldParams['Null']=='NO'?'NOT NULL':'NULL').' ';
			if($arFieldParams['Extra']!='auto_increment')
			{
				$query.=($arFieldParams['Default']!=''?" DEFAULT '".$arFieldParams['Default']."' ":" DEFAULT ''");
			}
			else
			{
				$query.=$arFieldParams['Extra'];
			}
			//$query.=($arFieldParams['Key']=='PRI'?' PRIMARY KEY':'');
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
	 */
	protected function DescribeColumn($sTable,$sColumn)
	{
		if(!$sTable) return false;
		/* Чтение всех полей таблицы */
		$query="SHOW COLUMNS FROM " . PREFIX . $sTable." WHERE Field='$sColumn'";
		$this->query($query);
		/* Формирование массива с параметрами полей - Type (тип данных), Size (размер в байтах) */
		while ($field = $this->get_row())
		{
			return $field;
		}
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
		{
			if(in_array(PREFIX.$sTable,$arDBTables) && IsTextIdent($sTable)) $arTablesToDelete[]=PREFIX.$sTable;
		}
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
}


?>
