<?php
/**
 * \file class.CDBInterface.php
 * Файл для корневого класса систем управления базами данных
 * Файл проекта CMS-local.
 *
 * Создан 03.12.2008
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 2.0
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}
include_once MODULES_DIR.'/main/libs/class.CDBError.php';
/**
 * Абстрактный класс для реализации интерфейсов работы с БД
 *
 * Класс представляет собой корневой элемент системы управления Базами Данных.
 * В классе описаны все методы и переменные, которые могут быть доступны в различных
 * интерфейсах БД. Часть методов, может быть недоступной в определенных интерфейсах
 * из-за ограниченных возможностей этих баз данных
 */

abstract class CDBInterface
{
	protected $arBackUpData;						/**<массив содержащий данные которые будут подвергнуты изменениям, необходим для сохранения данных.*/
	protected $arDBStructure;
	protected $iBegin=0;						/**<обозначет количество вложенных вызовов системы отката*/
	protected $arRequests;			/**<В массиве храняться тексты всех запросов к БД в рамках одного вызова (если включен режим отладки) \since 1.2*/
	protected $arLocalRequests;		/**<В массиве храняться локальные запросы, если ключ текущего режима отладки больше 1*/
	protected $iDebug;				/**<Флаг указывает на необходимость сохранения запросов к бд \since 1.2*/

	abstract function __construct($debug=0);
	/**
	 * Метод выполняет подключение к базе данных
	 */
	abstract function connect($ks_db_user, $ks_db_pass, $ks_db_name, $ks_db_location = 'localhost', $show_error=1);
	abstract function query($query, $show_error=true);

	/**
	 * Переключает режим отладки запросов в БД
	 * @param int $mode - режим отладки запросов
	 * @return int - предыдущее значение режима
	 *
	 * Значения флага режимов: 0 - запись в лог не ведется, 1 - доступна запись в лог.
	 * >1 - ведется запись в лог с соответсвующим номером
	 */
	function SetDebugMode($mode)
	{
		$oldmode=$this->iDebug;
		$this->iDebug=$mode;
		return $oldmode;
	}

	/**
	 * Метод очищает требуемый список запросов
	 * @param int $mode номер списка для очистки
	 */
	function ClearDebugMode($mode)
	{
		$this->arLocalRequests[$mode]=array();
	}
	/**
	 * Метод добавляет сообщение в список запросов
	 *
	 * @param string $message - сообщение которое необходимо добавить в лог
	 * @param int $time - время выполнения операции, необязательный
	 */
	function add2log($message,$time=0)
	{
		/*Запись запроса в лог, запись времени его исполнения в лог*/
		if($this->iDebug>0)
		{
			$arRow=array(
			'TIME'=>$time,
			'QUERY'=>$message,
			);
			$this->arRequests[]=$arRow;
			if($this->iDebug>1)
			{
				$this->arLocalRequests[$this->iDebug][]=$arRow;
			}
		}
	}

	/**Начинает запись всех изменений в БД. Сохранение идет в оперативную память, обязательно после выполнения этой функции, требуется
	либо подтверждение её выполнения, либо отмена. Не рекомендуется исполнять большое количество запросов в блоке begin -- commit, rollback. Это
	может привести к увеличенному времени обработки страницы.
	\author blade39 <blade39@kolosstudio.ru>
	\since 1.0
	*/
	function begin()
	{
		$this->iBegin++;
		$this->arBackUpData[$this->iBegin]=array();
	}

	abstract function rollback($show_error=false);

	/**Применяет все последние изменения в БД. Не выполняет никакой работы в базе данных, фактически производит очистку массива операций
	и снятие флага записи активности*/
	function commit()
	{
		if($this->iBegin>0)
		{
			unset($this->arBackUpData[$this->iBegin]);
			$this->iBegin--;
		}
		return true;
	}

	abstract function get_row($query_id = '');
	abstract function get_array($query_id = '');
	abstract function super_query($query, $multi = false);
	abstract function num_rows($query_id = '');
	abstract function insert_id();
	abstract function AffectedRows();
	abstract function get_result_fields($query_id = '');
	abstract function safesql( $source );
	abstract function free( $query_id = '' );
	abstract function close();
	abstract public function AddColumn($table,$arColumn);
	abstract public function UpdateColumnType($sTable,$sColumn,$sType);
	abstract function UpdateColumn($sTable,$sColumn,$arParams);
	abstract function DeleteTables($arTables);
	abstract function DeleteColumn($sTable,$sColumn);
	abstract function RenameTable($sTable,$sNewName);

	/**Возращает текущее время с миллисекундами.*/
	function get_real_time()
	{
		list($seconds, $microSeconds) = explode(' ', microtime());
		return ((float)$seconds + (float)$microSeconds);
	}

	/**
	 * Метод выполняющий анализ и сравнение структуры базы данных
	 * со структурой переданной в виде ассоциативного массива
	 * в качестве параметра
	 */
	function CheckDB($arDBStructure)
	{
		if(!is_array($arDBStructure) || count($arDBStructure)==0) return false;
		$this->arDBStructure=$this->ListTables(true);
		foreach($arDBStructure as $sTableName=>$arTableStructure)
		{
			if(array_key_exists(PREFIX.$sTableName,$this->arDBStructure))
			{
				$this->CheckTable($sTableName,$arTableStructure);
			}
			else
			{
				$this->AddTable($sTableName,$arTableStructure);
			}
		}
	}

	/**
	 * Метод добавляет таблицу в БД по описанию структуры
	 */
	abstract function AddTable($sTable,$arTableStructure);

	/**
	 * Метод осуществляет анализ таблицы
	 */
	function CheckTable($sTable,$arTableStructure)
	{
		if(!is_array($this->arDBStructure))
		{
			$this->arDBStructure=$this->ListTables(true);
		}
		if(!array_key_exists(PREFIX.$sTable,$this->arDBStructure))
		{
			throw new CDBError('TABLE_NOT_FOUND');
		}
		else
		{
			foreach($arTableStructure as $sField=>$arField)
			{
				if(array_key_exists($sField,$this->arDBStructure[PREFIX.$sTable]))
				{
					//Если поле таблице существует, надо проверить его параметры
					$bUpdate=false;
					foreach($arField as $sParam=>$sValue)
					{
						if($sParam=='Default' && $arField['Key']=='PRI')
						{
							continue;
						}
						if($sParam=='Extra' && $sValue=='fulltext')
						{
							if($this->arDBStructure[PREFIX.$sTable][$sField]['Key']=='MUL')
								continue;
							else
							{
								$bUpdate=true;
								break;
							}
						}
						if($this->arDBStructure[PREFIX.$sTable][$sField][$sParam]!=$sValue)
						{
							$bUpdate=true;
							break;
						}
					}
					if($bUpdate)
					{
						$this->UpdateColumn($sTable,$sField,$arField);
					}
				}
				else
				{
					//Поле таблицы не существует, надо создать
					$this->AddColumn($sTable,$arField);
				}
			}
		}
	}

	/**
	 * Метод возвращает список запросов в виде таблицы по текущиму коду отладки
	 * @return string - строка с таблицей запросов.
	 */
	function GetRequestsTable()
	{
		if($this->iDebug>1)
		{
			$arData=$this->arLocalRequests[$this->iDebug];
		}
		elseif($this->iDebug==1)
		{
			$arData=$this->arRequests;
		}
		$sResult='<table border="1" width="100%"><tr><th>Запрос</th><th>Время исполнения</th></tr>';
		for($i=0;$i<count($arData);$i++)
		{
			$sResult.='<tr><td>'.$arData[$i]['QUERY'].'</td><td>'.
					$arData[$i]['TIME'].'</td></tr>';
		}
		$sResult.='</table>';
		return $sResult;
	}
	/**
	 * Метод возвращает список выполненных запросов в виде массива.
	 */
	function GetRequests()
	{
		if($this->iDebug==1)
		{
			return $this->arRequests;
		}
		else
		{
			return false;
		}
	}
}
?>
