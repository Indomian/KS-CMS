<?php
/**
 *
 * В файле находятся главные классы системы администрирования.
 * Классы основы дерева классов системы администрирования, основной класс для работы с базой данных
 * Файл проекта CMS-local.
 *
 * @filesource class.CMain.php
 * @author blade39 <blade39@kolosstudio.ru>, north-e <pushkov@kolosstudio.ru>
 * @version 2.4
 * @since 21.11.2007
 * Изменения:
 * 2.4 		- Добавлена поддержка выборки из нескольких таблиц для класса CObject
 * 2.3		- изменено имя объекта для работы с базой данных с $db на $ks_db
 * 			- добавлен тип данных binary в методе CObject::Save()
 */

// Список констант для ошибок
define('KS_ERROR_MAIN_ALREADY_EXISTS',1); //Запись уже существует

require_once 'class.CBaseObject.php';
require_once 'class.CBaseAPI.php';

abstract class CBaseList extends CBaseObject
{
	/**
	 * Количество записей в списке, доступно после выполнения метода Count или GetList
	 */
	public $iCount;
	/**
	 * Массив со списком полей в списке. Необходимо для проверки при выполнении запросов.
	 */
	protected $arFields;

	abstract protected function _ParseItem(&$item);
	abstract public function GetList($arOrder=false,$arFilter=false,$limit=false,$arSelect=false,$arGroupBy=false);
	/**
	 * Метод выполняет подсчет количества элементов соответствующих заданому фильтру
	 *
	 * @version 2.3
	 * @since 29.03.2009
	 * Изменения:
	 * 2.3		- добавлен параметр $fGroup
	 *
	 * @param array $arFilter ассоциативный массив филтрации, также смотрите CObject::_GenWhere.
	 * @param string $fGroup Используется для группировки количества элементов
	 * @return mixed Число элементов, подходящих к фильтру, или массив с числами элементов, соответствующих различным значениям fGroup
	 * @sa CObject::GetList(), CObject::_GenWhere()
	 */
	abstract function Count($arFilter = false, $fGroup = false);

	/**
	 * Метод возвращает список полей текущего объекта
	 */
	function GetFields()
	{
		return $this->arFields;
	}

	/**
	 * Метод создает массив данных из данных переданных в пост с префиксом.
	 * \param $prefix - префикс к данным
	 * \param $data - ассоциативный массив данных обычно $_POST
	 * \return ассоциативный массив с заполнеными полями данных
	 */
	public function GetFromPost($prefix,$data)
	{
		$arResult=array();
		foreach($this->arFields as $field)
		{
			if(array_key_exists($prefix.$field,$data))
			{
				$arResult[$field]=$data[$prefix.$field];
			}
		}
		return $arResult;
	}
}

/**
Класс CObject - базовый класс для работы с различными элементами в базе данных
позволяет производить автоматическое сохранение данных любой сложности
имеется возможность задавать Автозаполняемые поля, поля требующие проверки,
поля для которых необходимо произвести загрузку файла.

Изменения:
15.10.2008 - добавлен конструктор и деструктор в стиле ПХП5;
16.10.2008 - изменена фукция выборки, теперь фильтр поддерживает продвинутую выборку (больше, меньше, LIKE и т.д.);
22.11.2008 - добавлен тип проверки полей, по функции "И" или по "ИЛИ", по умолчанию используется "И" для обратной совместимости;
20.05.2009 - добавлена возможность сложных выборок из нескольких таблиц;
@version 2.3
*/
class CObject extends CBaseList
{
	protected $check_fields;	/*!<поля требующие уникального значения.*/
	protected $auto_fields;	/*!<поля заполняемые автоматчески (без взятия данных из параметров).*/
	public $sTable;		/*!<таблица с которой работает данный класс.*/
	protected $arTables;/*!<список таблиц участвующих в следующем запросе.*/
	protected $arJoinTables;/*!<массив таблиц участвующих в объединении типа join.*/
	/**
	 * @var $obDB - указатель на объект класса $ks_db осуществляющего связь с базой данных
	 */
	protected $obDB;
	/**
	 * @todo Сделать скрытым
	 */
	public $sUploadPath; 	/*!<Путь для загрузки файлов через функцию save.*/
	protected $checkMethod;	/*!<Метод проверки для нескольких полей.*/
	protected $sFieldsModule; /*!<Название модуля из которого брать значения доп полей*/
	protected $arFilter;		/*!<список полей для доп фильтрации*/

	static protected $arCache;
	static protected $dbStructure;

	protected $bDistinctMode;

	/*!Конструктор класса. Заполняет атрибуты класса значениями по умолчанию.
	 \param $sTable -- имя таблицы для работы (без префикса).*/
	function __construct($sTable='')
	{
		global $ks_db;
		$this->obDB=$ks_db;
		if(!is_array(self::$dbStructure))
		{
			if(file_exists(CONFIG_DIR.'/db_structure.php'))
			{
				$arStructure=array();
				include CONFIG_DIR.'/db_structure.php';
				self::$dbStructure=$arStructure;
			}
			else
			{
				self::$dbStructure=array();
			}
		}
		$this->sTable=$sTable;
		$this->auto_fields=array();
		$this->check_fields=array();
		$this->checkMethod='AND';
		$this->arTables=array();
		$this->bDistinctMode=false;
		if(array_key_exists($sTable,self::$dbStructure))
		{
			$this->arFields=array_keys(self::$dbStructure[$sTable]);
		}
		else
		{
			self::$dbStructure[$sTable]=$this->GetFieldsList();
			$this->arFields=array_keys(self::$dbStructure[$sTable]);
		}
	}

	/*!Деструктор класса. Не выполняет никакой определенной работы.*/
	function __destruct()
	{
	}

	/**
	 * Метод устанавливает тип проверки существования записей с такимиже полями
	 * перед сохранением элемента
	 * \param $method метод проверки, любая допустимая MYSQL строка.
	 * \since 2.2, 22.11.2008
	 */
	function SetCheckMethod($method="AND")
	{
		$this->checkMethod=$method;
	}

	/**
	 * Метод добавляет поле для проверки перед сохранением элемента в базу.
	 * Если добавить несколько методов, проверка будет идти по принципу "�?"
	 */
	function AddCheckField($field)
	{
		if (is_array($field))
		{
			$this->check_fields=array_merge($this->check_fields,$field);
		}
		else
		{
			if(!in_array($field,$this->check_fields)) $this->check_fields[]=$field;
		}
	}

	function AddAutoField($field)
	{
		if (is_array($field))
		{
			$this->auto_fields=array_merge($this->check_fields,$field);
		}
		else
		{
			$this->auto_fields[]=$field;
		}
	}

	function AddFileField($field)
	{
		$this->file_fields[]=$field;
	}

	function GenCheck($data)
	{
		$res=Array();
		if(is_array($this->check_fields))
		{
		foreach ($this->check_fields as $field)
		{
			if (array_key_exists($field,$data))
			{
				if (is_array($data[$field]))
				{
					foreach ($data[$field] as $value)
					{
						$res[]="$field='$value'";
					}
				}
				else
				{
					$res[]="$field='".$data[$field]."'";
				}
			}
		}
		}
		if (count($res)>0)
		{
			$result=implode(' '.$this->checkMethod.' ',$res);
		}
		else
		{
			$result="";
		}
		return $result;
	}

	/**
	 * Метод получает список полей для указанной таблицы или для
	 * таблицы стандартной для данного класса
	 * @param string $prefix префикс названия полей которые надо получить, пустой по умолчанию
	 * @param string $my_table название таблицы для которой надо получить список полей, если пустой
	 * то значение для текущего класса
	 * @return array массив со списком полей, где ключи - названия полей, значения - описание поля.
	 */
	function GetFieldsList($prefix="",$my_table="")
	{
		global $ks_db;

		$table=$my_table;
		if ($my_table=="")
		{
			$table=$this->sTable;
		}
		$arFields=$ks_db->GetTableFields($table,$prefix);
		foreach($arFields as $key=>$field)
		{
			$fType=$field['Type'];
			$lpos=strpos($fType,'(');
			if ($lpos>0)
			{
				$fSize=substr($fType,$lpos+1,strlen($fType)-$lpos);
				$fSize=chop($fSize,')');
				$fType=substr($fType,0,$lpos);
			}
			if ($prefix!="")
			{
				if (!(strpos($field['Field'],$prefix)===false))
				{
					$fields[$field['Field']]=Array('Type'=>strtolower($fType),'Size'=>$fSize,'Default'=>$field['Default']);
				}
			}
			else
			{
				$fields[$field['Field']]=Array('Type'=>strtolower($fType),'Size'=>$fSize,'Default'=>$field['Default']);
			}
		}
		return $fields;
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
		/* Преобразование входных данных в соответствии с форматом полей таблицы для записи */
		if (array_key_exists($prefix.$key, $input))
		{
			if (($value['Type'] == 'int')||($value['Type']=='smallint')||($value['Type']=='tinyint'))
				$result = intval($input[$prefix . $key]);
			if (($value['Type'] == 'char') || ($value['Type'] == 'varchar'))
				$result = $ks_db->safesql(mb_substr($input[$prefix . $key], 0, $value['Size'],'UTF-8'));
			if ($value['Type'] == 'text')
				$result = $ks_db->safesql($input[$prefix . $key]);
			if ($value['Type'] == 'float')
				$result = $ks_db->safesql($input[$prefix . $key]);
			if ($value['Type'] == 'enum')
				$result = $ks_db->safesql($input[$prefix . $key]);
			if ($value['Type'] == 'binary')
				$result = intval($input[$prefix . $key]);
			return $result;
		}
		return false;
	}

	/**
	 * Метод выполняет сохранение записи в БД
	 *
	 * @version 2.5
	 * Изменения:
	 * 2.5 - поддержка загрузки файлов убрана в дополнительный класс
	 * BlaDe39 13.03.10 Изменен способ загрузки файлов
	 * 2.3		- добавлен тип binary
	 * BlaDe39 07.09.09 Исправлена обреза текстовых полей при работе с УТФ-8
	 *
	 * @param $prefix префикс поля в массиве данных
	 * @param $data массив данных если пустой, используется массив $_REQUEST
	 * @param $my_table таблица в которую производить сохранение если пустая используется $this->sTable
	 * @todo Написать нормальную документацию.
	 * @return ID сохраненного элемента или false в случае ошибки.
	 */

	function Save($prefix = "KS_", $data = "")
	{
		global $ks_db, $bMagicGPC, $KS_FS;

		$table = $this->sTable;

		/* Определяем массив входных данных для сохранения в базе */
		if ($data == "")
			$input = $_REQUEST;
		elseif(is_array($data))
			$input = $data;
		else
			throw new CError ("MAIN_INCORRECT_DATA_FORMAT", 999);
		$data = array();

		$fields=$this->_GetTableFields();
		foreach ($fields as $key => $value)
		{
			$sValue=$this->_ParseField($prefix,$key,$input,$value);
			if($sValue!==false)
				{
				$data[$key]=$sValue;
			}
		}
		/* Определение, есть ли запись с заданным id в таблице или нет.
		   В зависимости от этого обновим старую запись или добавим новую. */
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
				return $res;
			}
		}

		$fields = "";
		$values = "";
		if (is_array($this->auto_fields))
			foreach($data as $key=>$item)
			{
				if (!in_array($key, $this->auto_fields))
				{
					$fields .= "`".$key."`,";
					$values .= "'$item',";
				}
			}

		$fields = chop($fields, " ,");
		$values = chop($values, " ,");
		$check = $this->GenCheck($data);		// Проверка на существование записи в таблице
		if ($check != "")
		{
			$ks_db->query("SELECT id FROM ".PREFIX.$table." WHERE $check");
			$numrows=$ks_db->num_rows();
		}
		else
			$numrows=0;
		if ($numrows > 0)
		{
			throw new CError("MAIN_RECORD_ALREADY_EXISTS",KS_ERROR_MAIN_ALREADY_EXISTS,$this->check_fields);
			$res = $_REQUEST[$prefix . 'id'];
		}
		else
		{
			$query_string = "INSERT INTO " . PREFIX . $table . "($fields) VALUES ($values)";
			$ks_db->query($query_string);
			$res = $ks_db->insert_id();
		}
		if($res)
			unset(CObject::$arCache[$this->sTable][$res]);
		return $res;
	}

	/**
	получает одну запись из таблицы по указанным параметрам
	@param $where - ассоциативный массив
	   поле => значение
	*/
	function GetRecord($where=false)
	{
		if(!is_array(CObject::$arCache)) CObject::$arCache=array();
		if($arItems=$this->GetList(array('id'=>'asc'),$where,1))
		{
			$arItem=array_pop($arItems);
			CObject::$arCache[$this->sTable][$arItem['id']]=$arItem;
			return $arItem;
		}
		return false;
	}

	/**
	 * Метод возвращает запись по её номеру, если есть возможность запись возвращается из кэша
	 */
	function GetById($id)
	{
		if(is_array(CObject::$arCache))
			if(array_key_exists($this->sTable,CObject::$arCache))
				if(array_key_exists($id,CObject::$arCache[$this->sTable]))
					return CObject::$arCache[$this->sTable][$id];
		return $this->GetRecord(array('id'=>$id));
	}

	/**
	 * Метод возвращает значения полей из массива данных
	 *
	 * Метод позволяет построить данные по правильной структуре исходя из переданных в
	 * массиве данных, также производится проверка на соответствие полей в данных и в таблице.
	 * \param $prefix - префикс для полей в массиве данных, необязательный
	 * \param $data - массив данных, необязеательный если не указан используется $_POST
	 * \return Ассоциативный массив данных
	 */
	function GetRecordFromPost($prefix='',$data='')
	{
		if($data=='') $data=$_POST;
		foreach ($this->arFields as $field)
		{
			$arResult[$field]=$data[$prefix.$field];
		}
		return $arResult;
	}

	/**
	 * Метод генерирует хэш для использования при кэшировании вывода записей
	 */
	protected function _GenFilterHash($arFilter)
	{
		return md5($this->_GenWhere($arFilter));
	}

	/**
	 * Функция генерирует строку WHERE для запросов в БД. В качестве названия поля может выступать строка с указанием
	 * таблицы.
	 * @param $arFilter -- массив описывающий принцип фильтрации.
	 * @param $method - метод сравнения (AND по умолчанию)
	 * @sa CObject::GetList()
	 * @version 1.3
	 * Добавлена поддержка работы с несколькими таблицами а также сложные операции объединения таблиц
	 * префиксы для сложных сравнений - >?, <?, ?.
	 * Добавлена поддержка функций mysql
	 * ? - просто произодит сравнение двух полей в разделе WHERE
	 * >? - выполняет правое объединение двух таблиц по указанным полям
	 * <? - выполняет левое объединение двух таблиц по указанным полям
	 * @version 2.5.3
	 * Добавлена поддержка проверки типа переданных данных для операции IN
	 * если передан массив или число - система сама сформирает строку
	 * если строка - работаем по старинке
	 * */
	protected function _GenWhere($arFilter,$method='AND',$step=0)
	{
		global $ks_db;
		if (is_array($arFilter))
		{
			if(is_array($this->arFilter))
			{
				$arFilter=array_merge($arFilter,$this->arFilter);
			}
			$arFil=Array();
			foreach ($arFilter as $field=>$value)
			{
				if(($field=='AND'||$field=='OR')&&is_array($value))
				{
					//Обработка сложных запросов
					$sWhere=substr($this->_GenWhere($value,$field,$step+1),7);
					if($sWhere!='')
					{
						$arFil[]='('.$sWhere.')';
					}
				}
				elseif(is_numeric($field))
				{
					$sWhere=substr($this->_GenWhere($value,$method,$step+1),7);
					if($sWhere!='')
					{
						$arFil[]='('.$sWhere.')';
					}
				}
				else
				{
					if(substr($field,0,1)=='?')
					{
						$noSafe=true;
						$field=substr($field,1);
						$iPointPos=strpos($value,'.');
						/*Добавлена проверка на сложное наименование полей*/
						if($iPointPos>0)
						{
							//Значит впереди идет имя таблицы
							$arField=explode(".",$value);
							if($arField[0]!='')
							{
								//Очень неудачный вариант для парсинга, но делать нечего, пробуем
								if(preg_match_all('#((([a-z_]+)\.)[a-z_]+)#si',$value,$matches))
								{
									$arTableNames=array();
									foreach($matches[3] as $tableName)
									{
										if(!array_key_exists($tableName,$this->arTables))
										{
											//Такая таблица еще не добавлена
											$code=chr(65+count($this->arTables));
											$arTableNames[$tableName.'.']=$code.'.';
											$this->arTables[$tableName]=$code;
										}
										else
										{
											$arTableNames[$tableName.'.']=$this->arTables[$tableName].'.';
										}
									}
									$value=str_replace(array_keys($arTableNames),array_values($arTableNames),$value);
								}
								else
									continue;
							}
							else
							{
								//Значит впереди идет имя таблицы
								$arField=explode(".",$value);
								if($arField[0]!='')
								{
									if(!array_key_exists($arField[0],$this->arTables))
									{
										//Такая таблица еще не добавлена
										$code=chr(65+count($this->arTables));
										$this->arTables[$arField[0]]=$code;
										$value=$code.'.'.$arField[1];
									}
									else
									{
										$value=$this->arTables[$arField[0]].'.'.$arField[1];
									}
								}
								else
									continue;
							}
						}
					}
					elseif((substr($field,0,2)=='<?')||(substr($field,0,2)=='>?'))
					{
						//Левый или правый джоин
						$noSafe=true;
						$arRow['operation']=(substr($field,0,1)=='<'?'LEFT JOIN':'RIGHT JOIN');
						$field=substr($field,2);
						/*Добавлена проверка на сложное наименование полей*/
						if(strpos($field,'.')>0)
						{
							//Значит впереди идет имя таблицы
							$arField=explode(".",$field);
							if($arField[0]!='')
							{
								if(!array_key_exists($arField[0],$this->arTables))
								{
									//Такая таблица еще не добавлена
									$code=chr(65+count($this->arTables));
									$this->arTables[$arField[0]]=$code;
									$field=$code.'.'.$arField[1];
								}
								else
								{
									$field=$this->arTables[$arField[0]].'.'.$arField[1];
								}
							} else continue;
							$arRow['fromTable']=$arField[0];
						}
						else
						{
							$arRow['fromTable']=$this->sTable;
						}
						if(strpos($value,'.')>0)
						{
							//Значит впереди идет имя таблицы
							$arField=explode(".",$value);
							if($arField[0]!='')
							{
								if(!array_key_exists($arField[0],$this->arTables))
								{
									//Такая таблица еще не добавлена
									$code=chr(65+count($this->arTables));
									$this->arTables[$arField[0]]=$code;
									$value=$code.'.'.$arField[1];
								}
								else
								{
									$value=$this->arTables[$arField[0]].'.'.$arField[1];
								}
							} else continue;
							$arRow['toTable']=$arField[0];
						}
						else
						{
							$arRow['toTable']=$this->sTable;
						}
						$arRow['ON']=$field.'='.$value;
						$this->arJoinTables[]=$arRow;
						continue;
					}
					elseif((substr($field,0,2)=='->')||(substr($field,0,3)=='!->'))
					{
						$noSafe=false;
					}
					else
					{
						$value=$ks_db->safesql($value);
						$noSafe=false;
					}
					/*Проверяем на допустимые операции, если одна из них указана,
					 * выполняем обработку введенных данных и формируем операцию.*/

					if(preg_match('#^(!~|!->|[><!~=]|>=|<=|->|%)?([\w_\.\-]+)#i',$field,$matches))
					{
						$operation=$matches[1];
						if($operation=='') $operation="=";
						$myfield=$matches[2];
						/*Добавлена проверка на сложное наименование полей*/
						if(strpos($myfield,'.')>0)
						{
							//Значит впереди идет имя таблицы
							$arField=explode(".",$myfield);
							if($arField[0]!='')
							{
								if(!array_key_exists($arField[0],$this->arTables))
								{
									//Такая таблица еще не добавлена
									$code=chr(65+count($this->arTables));
									$this->arTables[$arField[0]]=$code;
									$myfield=$code.'.'.$arField[1];
								}
								else
								{
									$myfield=$this->arTables[$arField[0]].'.'.$arField[1];
								}
							} else continue;
						}
						else
						{
							if (in_array($myfield,$this->arFields))
							{
								//Простое наименование, значит поле стандартной таблицы - преобразуем
								if(!array_key_exists($this->sTable,$this->arTables))
								{
									//Такая таблица еще не добавлена
									$code=chr(65+count($this->arTables));
									$this->arTables[$this->sTable]=$code;
									$myfield=$code.'.'.$myfield;
								}
								else
								{
									$myfield=$this->arTables[$this->sTable].'.'.$myfield;
								}
							}
							else
							{
								continue;
							}
						}
						if($operation=='!')
							if($noSafe)
								$operation=" $myfield!=$value ";
							else
								$operation=" $myfield!='".$ks_db->safesql($value)."' ";
						elseif($operation=='~') $operation=" $myfield LIKE '%".$ks_db->safesql($value)."%' ";
						elseif($operation=='!~') $operation=" $myfield NOT LIKE '%".$ks_db->safesql($value)."%' ";
						elseif($operation=='->')
						{
							if(is_numeric($value))
							{
								$operation=" $myfield IN (".$ks_db->safesql($value).")";
							}
							elseif(is_array($value))
							{
								$operation=" $myfield IN ('".join("','",$value)."')";
							}
							elseif(is_string($value))
							{
								$operation=" $myfield IN $value";
							}
							else
							{
								continue;
							}
						}
						elseif($operation=='!->')
						{
							if(is_numeric($value))
							{
								$operation=" $myfield NOT IN ($value)";
							}
							elseif(is_array($value))
							{
								$operation=" $myfield NOT IN ('".join("','",$value)."')";
							}
							elseif(is_string($value))
							{
								$operation=" $myfield NOT IN $value";
							}
							else
							{
								continue;
							}
						}
						elseif($operation=='%') $operation=" $myfield is '".$ks_db->safesql($value)."' ";
						elseif($noSafe) $operation=" $myfield $operation $value ";
						else $operation=" $myfield $operation '".$ks_db->safesql($value)."' ";
						$arFil[]=$operation;
					}
				}
			}
			if(count($arFil)>0)
			{
				return " WHERE ".join(" $method ",$arFil);
			}
		}
		return '';
	}

	/**
	 * Метод генерирует строку FROM запроса, по списку полей переданному в поля WHERE и FIELDS
	 */
	protected function _GenFrom()
	{
		if(is_array($this->arTables)&&(count($this->arTables)>0))
		{
			$arRes=array();
			$bJoinEnd=false;
			$sJoin='';
			$arResJoin=array();
			$arResTables=$this->arTables;
			if(is_array($this->arJoinTables)&&count($this->arJoinTables)>0)
			{
				$bJoinBegin=false;
				foreach($this->arJoinTables as $arJoin)
				{
					if($bJoinBegin==false)
					{
						$arResJoin[]=PREFIX.$arJoin['fromTable'].' AS '.$this->arTables[$arJoin['fromTable']].
							' '.$arJoin['operation'].
							' '.PREFIX.$arJoin['toTable'].' AS '.$this->arTables[$arJoin['toTable']].
							' ON '.$arJoin['ON'];
						$bJoinBegin=true;
						unset($arResTables[$arJoin['fromTable']]);
						unset($arResTables[$arJoin['toTable']]);
					}
					else
					{
						$arResJoin[]=' '.$arJoin['operation'].
							' '.PREFIX.$arJoin['toTable'].' AS '.$this->arTables[$arJoin['toTable']].
							' ON '.$arJoin['ON'];
						unset($arResTables[$arJoin['toTable']]);
					}
				}
			}
			foreach($arResTables as $sTable=>$sCode)
			{
				$arRes[]=PREFIX.$sTable.' AS '.$sCode;
			}
			if(count($arResJoin)>0)
			{
				$sResult=join(' ',$arResJoin);
			if(count($arRes)>0)
					$sResult.=','.join(', ',$arRes);
			}
			elseif(count($arRes)>0)
			{
				$sResult=join(', ',$arRes);
			}
		}
		else
		{
			$sResult=PREFIX.$this->sTable;
		}
		return $sResult;
	}

	/**
	 * Метод генерирует порядок вывода строк для SELECT запроса. Все переданные
	 * даннные проходят проверку по списку полей, поля которые не существуют, отбрасываются.
	 *
	 * @param $arOrder array - ассоциативный массив имеющий следующую структуру.
	 * <code>
	 * array(
	 * 	'&lt;field_name&gt;'=>'ASC|DESC',
	 * )
	 * </code>
	 * @return MYSQL строка упорядочивания выборки или пустая строка.
	 * @sa CObject::GetList(), CObject::_GenWhere(), CObject::_GenSelect()
	 * @since Используется начиная с версии класса 2.1
	 * @since 2.3 добавлена поддержка множественного выбора
	 */

	protected function _GenOrder($arOrder)
	{
		$sOrder='';
		if (is_array($arOrder))
		{
			$arOrderation=array();
			foreach ($arOrder as $field=>$dir)
			{
				/*Добавлена проверка на сложное наименование полей*/
				if(substr($field,0,1)=='?')
				{
					$arOrderation[]=substr($field,1).' '.$dir;
				}
				elseif(strpos($field,'.')>0)
				{
					//Значит впереди идет имя таблицы
					$arField=explode('.',$field);
					if($arField[0]!='')
					{
						if(!array_key_exists($arField[0],$this->arTables))
						{
							//Такая таблица еще не добавлена
							$code=chr(65+count($this->arTables));
							$this->arTables[$arField[0]]=$code;
							$field=$code.'.'.$arField[1];
						}
						else
						{
							$field=$this->arTables[$arField[0]].'.'.$arField[1];
						}
					} else continue;
					$dir=(strtoupper($dir)=='ASC')?'ASC':'DESC';
					$arOrderation[]=$field.' '.$dir;
				}
				else
				{
					if ($field!==0&&in_array($field,$this->arFields))
					{
						//Простое наименование, значит поле стандартной таблицы - преобразуем
						if(!array_key_exists($this->sTable,$this->arTables))
						{
							//Такая таблица еще не добавлена
							$code=chr(65+count($this->arTables));
							$this->arTables[$this->sTable]=$code;
							$field=$code.'.'.$field;
						}
						else
						{
							$field=$this->arTables[$this->sTable].'.'.$field;
						}
						$dir=(strtoupper($dir)=='ASC')?'ASC':'DESC';
						$arOrderation[]=$field.' '.$dir;
					}
					elseif (strtolower($field) == "rand()")
					{
						/* Сортируем выборку в случайном порядке */
						$arOrderation[] = $field;
					}
				}
			}
			if(count($arOrderation)>0)
			{
				$sOrder=" ORDER BY ".join(' ,',$arOrderation);
			}
		}
		return $sOrder;
	}

	/**
	 * Метод генерирует строку группировки данных запроса. Добавлена поддержка
	 * сложных массивов для группировки с поддержкой сортировки внутри массива
	 * группировки.
	 *
	 * @param $arOrder array - массив полей по которым осуществлять группировку
	 * <code>
	 * array(
	 * 	'&lt;field_name&gt;'=>'ASC|DESC',
	 * )
	 * </code>
	 * @return MYSQL строка упорядочивания выборки или пустая строка.
	 */

	protected function _GenGroup($arOrder)
	{
		$sOrder='';
		if (is_array($arOrder))
		{
			foreach ($arOrder as $key=>$field)
			{
				$sSort='';
				if(is_string($key)&&($field=='asc' || $field=='desc'))
				{
					$sSort=' '.strtoupper($field);
					$field=$key;
				}
				/*Добавлена проверка на сложное наименование полей*/
				if(strpos($field,'.')>0)
				{
					//Значит впереди идет имя таблицы
					$arField=explode('.',$field);
					if($arField[0]!='')
					{
						if(!array_key_exists($arField[0],$this->arTables))
						{
							//Такая таблица еще не добавлена
							$code=chr(65+count($this->arTables));
							$this->arTables[$arField[0]]=$code;
							$field=$code.'.'.$arField[1];
						}
						else
						{
							$field=$this->arTables[$arField[0]].'.'.$arField[1];
						}
					} else continue;
					$arOrderation[]=$field.$sSort;
				}
				else
				{
					if (in_array($field,$this->arFields))
					{
						//Простое наименование, значит поле стандартной таблицы - преобразуем
						if(!array_key_exists($this->sTable,$this->arTables))
						{
							//Такая таблица еще не добавлена
							$code=chr(65+count($this->arTables));
							$this->arTables[$this->sTable]=$code;
							$field=$code.'.'.$field;
						}
						else
						{
							$field=$this->arTables[$this->sTable].'.'.$field;
						}
						$arOrderation[]=$field.$sSort;
					}
				}
			}
			if(count($arOrderation)>0)
			{
				$sOrder=" GROUP BY ".join(' ,',$arOrderation);
			}
		}
		return $sOrder;
	}

	/**
	 * Метод генерирует список полей для SELECT запроса. Все переданные
	 * даннные проходят проверку по списку полей, поля которые не существуют, отбрасываются.
	 * @param $arSelect array - список полей которые надо преобразовать в строку.
	 * @param $bAddCount boolean - флаг указывающий, что надо добавлять поля count к полям выборки.
	 * @return MYSQL строка со списком полей или * для обозначения того, что надо выбрать все поля.
	 * @sa CObject::GetList(), CObject::_GenWhere()
	 * @since используется начиная с версии класса 2.1
	 *
	 * [+] 07.10.10 Добавлен параметр $bAddCount
	 */

	protected function _GenSelect($arSelect=false,$bAddCount=false)
	{
		$res=' * ';
		if ($arSelect!=false)
		{
			if (is_array($arSelect))
			{
				foreach ($arSelect as $key=>$myfield)
				{
					$prefix='';
					if(is_string($key))
					{
						$sNewName=$myfield;
						$myfield=$key;
					}
					else
					{
						$sNewName='';
					}
					/*Добавлена проверка на сложное наименование полей*/
					if(substr($myfield,0,1)=='?')
					{
						$myfield=substr($myfield,1);
						if($sNewName!='')
						{
							$prefix=' AS '.$sNewName;
						}
						$field[]=$myfield.$prefix;
					}
					elseif(strpos($myfield,'.')>0)
					{
						//Значит впереди идет имя таблицы
						$arField=explode('.',$myfield);
						if($arField[0]!='')
						{
							if(!array_key_exists($arField[0],$this->arTables))
							{
								//Такая таблица еще не добавлена
								$code=chr(65+count($this->arTables));
								$this->arTables[$arField[0]]=$code;
								$myfield=$code.'.'.$arField[1];
								if($arField[0]!=$this->sTable) $prefix=' AS '.($sNewName!=''?$sNewName:($arField[0].'_'.$arField[1]));
							}
							else
							{
								$myfield=$this->arTables[$arField[0]].'.'.$arField[1];
								if($arField[0]!=$this->sTable) $prefix=' AS '.($sNewName!=''?$sNewName:($arField[0].'_'.$arField[1]));
							}
						} else continue;
						$field[]=$myfield.$prefix;
					}
					else
					{
						if (in_array(strtolower($myfield),$this->arFields))
						{
							//Простое наименование, значит поле стандартной таблицы - преобразуем
							if(!array_key_exists($this->sTable,$this->arTables))
							{
								//Такая таблица еще не добавлена
								$code=chr(65+count($this->arTables));
								$this->arTables[$this->sTable]=$code;
								$newfield=$code.'.'.$myfield;
							}
							else
							{
								$newfield=$this->arTables[$this->sTable].'.'.$myfield;
							}
							if($sNewName!='')
							{
								$prefix=' AS '.$sNewName;
							}
							$field[]=$newfield.$prefix;
						}
					}
				}
				if(count($field)>0)
				{
					$res=' '.join(' ,',$field).' ';
				}
			}
		}
		return $res;
	}

	/**
	 * Защишенный метод выполняющий получение списка полей для любой таблицы
	 */
	protected function _GetTableFields($table=false)
	{
		global $ks_db;
		if(!$table) $table=$this->sTable;
		/* Глобальное изменение.
		   Теперь обработке подвергаются любые поставляемые данные.
		   Позволяет предотвратить ошибки при добавлении данных в БД. */

		/* Чтение всех полей таблицы */
		$ks_db->query("SHOW COLUMNS FROM " . PREFIX . $table);

		/* Формирование массива с параметрами полей - Type (тип данных), Size (размер в байтах) */
		$fields = array();
		while ($field = $ks_db->get_row())
		{
			$fType = $field['Type'];		// MySQL-тип поля
			$lpos = strpos($fType, '(');
			if ($lpos > 0)
			{
				$fSize = substr($fType, $lpos + 1, strlen($fType) - $lpos);
				$fSize = chop($fSize, ')');
				$fType = substr($fType, 0, $lpos);
			}
			$fields[$field['Field']] = array('Type' => strtolower($fType), 'Size' => $fSize);
		}
		return $fields;
	}

	/**
	 * Метод выполняет получение списка полей для текущей таблицы
	 */
	public function GetTableFields()
	{
		return $this->_GetTableFields();
	}

	/**
	 * Метод переключает режим выборок
	 */
	public function SetDistinctMode($mode)
	{
		$old=$this->bDistinctMode;
		if($mode) $this->bDistinctMode=true;
		else $this->bDistinctMode=false;
		return $old;
	}

	/**Функция возвращает отфильтрованный и упорядоченый список значений из базы данных.
	 * Возращаемые данные - массив строк, каждая строка - асоциатиный массив по структуре сходный
	 * либо с запросом либо с базой данных из которой осуществляется выборка.
	 * @version 2.2 Добавлена обработка сложных имен полей, добавлена поддержка выборки из нескольких
	 * таблиц
	 * @param $arOrder -- ассоциативный массив упорядочивания, ключ - поле для упорядочивания, значение - направление (asc|desc);
	 * @param $arFilter -- ассоциатвный массив фильтрации, ключ - поле, значение - значение поля (приводиться в sql безопасный вид)
	 * перед именем поля можно указывать одну из следующих операций:
	 * >,<,!,>=,<= - операции сравнения;
	 * ~ - опреация текстового сравнения LIKE %%;
	 * @param $limit -- количество выбираемых элементов;
	 * @param $arSelect -- список выбираемых полей.
	 */

	function GetList($arOrder=false,$arFilter=false,$limit=false,$arSelect=false,$arGroupBy=false)
	{
		global $ks_db;
		$this->arTables=array($this->sTable=>'A');
		$this->arJoinTables=array();
		/*Генерируем строку полей (SELECT)*/
		$fields=$this->_GenSelect($arSelect);
		/*Генерируем строку порядка (ORDER BY)*/
		$sOrder=$this->_GenOrder($arOrder);
		/*Генерируем строку фильтра (WHERE)*/
		$sWhere=$this->_GenWhere($arFilter);
		/*Генерируем строку группировки (GROUP BY)*/
		$sGroupBy=$this->_GenGroup($arGroupBy);
		/*Генерируем список таблиц (FROM)*/
		$sFrom=$this->_GenFrom();
		//Блокировка запросов если не указан список полей при сложном запросе
		if(!$arSelect && (count($this->arJoinTables)>0 || count($this->arTables)>1))
		{
			throw new CError('SYSTEM_QUERY_FIELDS_REQUIRE');
		}
		/*Считаем сколько элементов*/
		$limits='';
		if ($limit!=false)
		{
			if(is_array($limit))
			{
				$arLimits=$limit;
			}
			else
			{
				$arLimits[0]=$limit;
			}
			$limits="LIMIT ".join(',',$arLimits);
		}
		//$query="SELECT $fields FROM ".PREFIX.$this->sTable." $sWhere $sOrder $limits";
		if($this->bDistinctMode)
		{
			$query="SELECT DISTINCT $fields FROM $sFrom $sWhere $sGroupBy $sOrder $limits";
			$this->bDistinctMode=false;
		}
		else
 		{
			$query="SELECT $fields FROM $sFrom $sWhere $sGroupBy $sOrder $limits";
		}
		if(KS_DEBUG_QUERIES==1) echo $query.'<br/>';
		$ks_db_res=$this->obDB->query($query);
		if($ks_db->num_rows($ks_db_res)<1)
		{
			return false;
		}
		$res=array();
		while ($item=$ks_db->get_row($ks_db_res))
		{
			if($this->_ParseItem($item))
			{
				if(array_key_exists('id',$item))
					$res[$item['id']]=$item;
				else
					$res[]=$item;
			}
		}
		$this->data=$res;
		return $res;
	}

	/**
	 * Метод выполняет подсчет количества элементов соответствующих заданому фильтру
	 *
	 * @version 2.4
	 * @since 07.10.10
	 * [~] Изменен алгоритм расчета количества при работе с группировками.
	 * [+] Добавлена поддержка массива в качестве группировки
	 *
	 * @version 2.3
	 * @since 29.03.2009
	 * Изменения:
	 * 2.3		- добавлен параметр $fGroup
	 *
	 * @param array $arFilter ассоциативный массив филтрации, также смотрите CObject::_GenWhere.
	 * @param string $fGroup Используется для группировки количества элементов
	 * @return mixed Число элементов, подходящих к фильтру, или массив с числами элементов, соответствующих различным значениям fGroup
	 * \sa CObject::GetList(), CObject::_GenWhere()
	 */
	function Count($arFilter = false, $fGroup = false)
	{
		global $ks_db;
		$this->arTables=array($this->sTable=>'A');
		$this->arJoinTables=array();
		$sWhere = $this->_GenWhere($arFilter);
		if (!$fGroup)
		{
			if($this->bDistinctMode)
			{
				$query = "SELECT DISTINCT COUNT(A.id) FROM " . $this->_GenFrom(). $sWhere;
				$this->bDistinctMode=false;
			}
			else
			{
				$query = "SELECT COUNT(*) FROM " . $this->_GenFrom(). $sWhere;
			}
			$ks_db->query($query);
			if ($ks_db->num_rows() > 0)
			{
				$row = $ks_db->get_array();
				$this->items = $row[0];
			}
		}
		else
		{
			/* Группировка результатов по полю $arGroup */
			$results_count = array();
			if(!is_array($fGroup))
			{
				$fGroup=array($fGroup);
			}
			$sSelect=$this->_GenSelect($fGroup,true);
			$fGroup=$this->_GenGroup($fGroup);
			$query = "SELECT $sSelect FROM " . $this->_GenFrom() . $sWhere . $fGroup;
			$ks_db->query($query);
			while ($row = $ks_db->get_array())
			{
				$results_count[$row[0]] = $row[1];
			}
			$this->items = count($results_count);
			$this->arJoinTables=array();
			return $results_count;
		}
		return $this->items;
	}

	/**
	 * Метод позволяет увеличивать/уменьшать числовые значения полей
	 *
	 * @param string $field Имя поля в базе, числовое значение которого нужно изменить
	 * @param int $id Идентификатор записи, значение поля $field которой нужно изменить, или массив с ключами:
	 * 'where_field' - поле, по которому идентифицировать запись;
	 * 'where_field_value' - значение этого поля
	 * @param int $incremenet Приращение значения (может быть и отрицательным)
	 * @return bool
	 */
	function Increase($field, $arFilter, $increment = 1)
	{
		global $ks_db;
		if(!in_array($field,$this->arFields)) return false;
		$increment = intval($increment);
		$sWhere=$this->_GenWhere($arFilter);
		if ($increment)
		{
			if($increment>0)
			{
				$query = "UPDATE " . $this->_GenFrom() . " SET `" . $field . "` = IF((`$field`+$increment)<0,0,`$field`+$increment) $sWhere";
			}
			else
			{
				$query = "UPDATE " . $this->_GenFrom() . " SET `" . $field . "` = IF((`$field`-".abs($increment).")<0 OR `$field`=0,0,`$field`-".abs($increment).") $sWhere";
			}
			$ks_db->query($query);
			return $ks_db->AffectedRows();
		}
		return false;
	}

	/**
	 * Метод уменьшает значение счётчика на единицу
	 */
	function Decrease($field, $arFilter)
	{
		return $this->Increase($field, $arFilter,-1);
	}

	/**Функция выполняет обработку одного элемента при создании списка элементов.
	 * Можно подменять в потомках, что позволяет получать дополнительные данные без дополнительного
	 * обхода массива
	 * @param &$item указатель на запись которую требуется каким либо образом изменить.
	 * возвращает true если запись добавляется в результат или false если нет.
	 */
	protected function _ParseItem(&$item)
	{
		return true;
	}

	/**
	 * Метод выполняет обновление записи. Внимание! Опасный метод не выполняет проверок данных
	 * не использовать в качестве параметров массив $_POST
	 * @param mixed $id номер записи или массив номеров записей
	 * @param array $values ассоциативный массив полей которые надо обновить
	 * @param array $bFiltered массив номеров - это массив фильтрации
	 */
	function Update($id, $values,$bFiltered=false)
	{
		foreach ($values as $field => $value)
		{
			if(substr($field,0,1)=='?')
			{
				$field=substr($field,1);
				$arFil[]="`$field`=$value";
			}
			elseif (in_array($field,$this->arFields))
			{
				/* Необходимо преобразовать данные для запросов MySQL */
				$value = $this->obDB->safesql($value);
				$arFil[] = "`$field` = '" . $value . "'";
			}
		}

		if (count($arFil)>0): $sQuery=join(' ,',$arFil);endif;
		$sWhere='';
		if(!$bFiltered)
		{
			if(is_array($id))
			{
				foreach ($id as $ItemId)
				{
					$arWhere[]="`id`='$ItemId'";
				}
				if (count($arWhere)>0): $sWhere=join(' OR ',$arWhere);endif;
			}
			elseif (is_numeric($id))
			{
				$sWhere=" `id`='$id' ";
			}
		}
		else
		{
			$sWhere=$this->_GenWhere($id);
		}
		if (strlen($sWhere)>0)
		{
			$query="UPDATE ".PREFIX.$this->sTable." SET $sQuery WHERE ".$sWhere;
			//echo $query;
			$this->obDB->query($query);
			unset(CObject::$arCache[$this->sTable]);
			return mysql_affected_rows();
		}
		else
		{
			return -1;
		}
	}

	/**
	 * Метод производит удаление записи по известному id
	 * @param int $id номер записи
	 * @return boolean - результат исполнения
	 */
	function Delete($id)
	{
		return $this->DeleteItems(array('id'=>intval($id)));
	}

	/**
	 * Метод производит удаление элементов из таблицы по фильтру.
	 * \param $arFilter массив фильтрации
	 * \sa CObject::_GenWhere()
	 * \return true если удаение прошло успешно, false - если удаление не удалось
	 */

	function DeleteItems($arFilter)
	{
		global $ks_db;
		$sWhere=$this->_GenWhere($arFilter);
		$sWhere=preg_replace('# [a-z_\-]+\.#i',' ',$sWhere);
		if (strlen($sWhere)>0)
		{
			$query="DELETE FROM ".PREFIX.$this->sTable.$sWhere;
			$ks_db->query($query);
			return true;
		}
		return false;
	}

	/**
	 * Метод выполняет удаление элементов по массиву переданных идентификаторов
	 * записей, является оберткой для метода {@link CObject::DeleteItems() CObject::DeleteItems()}
	 * @param array $ids - массив записей которые необходимо удалить
	 * @return boolean - результат удаления
	 */
	function DeleteByIds($ids)
	{
		global $ks_db;
		if (is_array($ids))
		{
			$where=join('\', \'',$ids);
			return $this->DeleteItems(array('->id'=>"('".$where."')"));
		}
		return false;
	}
}
?>