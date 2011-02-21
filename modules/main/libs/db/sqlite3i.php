<?php
/**
 * \file sqlite3.php
 * Интерфейс базы данных SQLite 3
 * Файл проекта CMS-local.
 * 
 * Создан 03.12.2008
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 1.0  
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once 'class.CDBInterface.php';

class sqlite3i extends CDBInterface
{
	var $ks_db_id; 						/**<Объект класса для взаимодействия с БД*/
	var $connected = false;				/**<флаг подключения к базе данных*/
	var $query_num = 0;					/**<количество выполненных запросов*/
	var $query_list = array();			/**<список выполненных запросов*/
	var $mysql_error = '';					/**<ошибка работы db*/
	var $mysql_error_num = 0;			/**<номер ошибки db*/
	var $MySQL_time_taken = 0;			/**<время которое ушло на исполнение запросов*/
	var $query_id = false;					/**<номер запроса*/
	protected $lastError;				/**<текст последней ошибки*/
	
	function __construct($debug=0)
	{
		$this->iDebug=$debug;
		$this->connected=false;
	}
	/**
	 * Метод выполняет подключение к базе данных
	 * 
	 * Метод выполняет открытие файла базы данных, важным параметром является только 
	 * $ks_db_name который указывает путь к этому файлу, остальные параметры можно передавать любыми.
	 * Оставлены только для совместимости с другими интерфейсами баз данных
	 * \param $ks_db_user - не имеет значения
	 * \param $ks_db_pass - не имеет значения
	 * \param $ks_db_name - путь к файлу базы данных
	 * \param $ks_db_location - не имеет значения
	 * \param $show_error - флаг, указывает на необходимость отображения ошибок
	 * \return true - если все хорошо, false если подключение не удалось
	 */
	function connect($ks_db_user, $ks_db_pass, $ks_db_name, $ks_db_location = 'localhost', $show_error=1)
	{
		//пробуем подключиться
		if(!$this->db_id = new PDO('sqlite:'.$ks_db_name)) 
		{
			if($show_error == 1) {
				$this->display_error($this->db_id->lastErrorMsg,0,$ks_db_name);
			} else {
				return false;
			}
		}
		//успешно законнектились
		$this->connected = true;
		//просим данные в удобной кодировке
		//$this->query("SET NAMES 'UTF8'");

		return true;
	}
	
	function query($query, $show_error=true)
	{
		$time_before = $this->get_real_time();

		if($this->connected==false) $this->connect(DBUSER, DBPASS, DBNAME, DBHOST);

		/*проверяем включенность режима записи операций, если работает производим выборку в зависимости от типа операции
		(добавление, обновление)*/
		if($this->iBegin>0)
		{
			$matches=array();
			if(preg_match("#^UPDATE ([\w\d_]+) SET ?(('?[\w\d]+'?='[\w\d]+',? )+) ?WHERE ([\w\d= ',\(\)]+)#",$query,$matches)>0)
			{
				$arRow['OP']='UPDATE';
				$arRow['TABLE']=$matches[1];
				$arRow['WHERE']=$matches[4];
				//print_r($matches);
				$arRow['QUERY']=$query;
				//print_r($arRow);
				$myquery="SELECT * FROM ".$arRow['TABLE']." WHERE ".$arRow['WHERE'];
				if(!($this->query_id = new sqlite3row($this->db_id->query($query)) )) 
				{
					$this->lastError = $this->db_id->lastErrorMsg();
					if($show_error) 
					{
						$this->display_error("Ошибка работы системы отката:\n".$this->lastError, 0, "запрос:".$query."\n выборка из базы".$myquery."\n запрос на обновление".$arRow['QUERY']);
					}
				}
				while($arResRow=$this->get_row())
				{
					$arRow['DATA'][]=$arResRow;
				}
			}
			if(preg_match('#^INSERT INTO ([\w\d_]+)#',$query,$matches)>0)
			{
				$arRow['OP']='INSERT INTO';
				$arRow['TABLE']=$matches[1];
				$arRow['QUERY']=$query;
			}
		}
		echo $query; 
		$this->query_id = new sqlite3row($this->db_id->query($query));
		if(!$this->query_id){
			$this->lastError = $this->db_id->errorInfo();
			if($show_error) {
				$this->display_error($this->lastError[2], $this->db_id->errorCode(), $query);
			}
		}
		
		if(($this->iBegin>0)&&($arRow['OP']=='INSERT INTO'))
		{
			$arRow['DATA']=$this->insert_id();
		}
		if($this->iBegin>0)
		{
			array_push($this->arBackUpData[$this->iBegin],$arRow);
		}
		
		/*Запись запроса в лог, запись времени его исполнения в лог*/
		if($this->iDebug==1)
		{
			$arRow=array(
			'TIME'=>$this->get_real_time() - $time_before,
			'QUERY'=>$query,
			);
			$this->arRequests[]=$arRow;
		}
		
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
					if(!($this->query_id = new sqlite3row($this->db_id->query($query)) )) 
					{
						$this->lastError = $this->db_id->lastErrorMsg();
						if($show_error) {
							$this->display_error($this->lastError, $this->db_id->lastErrorCode(), $query);
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
							$sSet.="$key='$value',";
						}
						$sSet=trim($sSet,",");
						$query="UPDATE ".$arRow['TABLE']." SET ".$sSet;
						if(!($this->query_id = new sqlite3row($this->db_id->query($query)) )) 
						{
							$this->lastError = $this->db_id->lastErrorMsg();
							if($show_error) {
								$this->display_error($this->lastError, $this->db_id->lastErrorCode(), $query);
							}
						}
					}
				}			
			}
			$this->iBegin--;
			return true;
		}
	}

	/**Возвращает одну строку в виде ассоциативного массива из запроса. В качестве параметра можно передать код запроса. По умолчанию используется
	код последненго запроса.
	@param $query_id -- целое число, дескриптор результата.*/
	function get_row($query_id = '') 
	{
		if ($query_id == '') $query_id = $this->query_id;
		return $query_id->fetch();
	}

	/**Возвращает одну строку в виде индексированного массива из запроса. В качестве параметра можно передать код запроса. По умолчанию используется
	код последненго запроса.
	@param $query_id -- целое число, дескриптор результата.*/
	function get_array($query_id = '') {
		if ($query_id == '') $query_id = $this->query_id;

		return $query_id->fetch();
	}

	/**Пока без документации*/
	function super_query($query, $multi = false) {

		if(!$multi) {

			$this->query($query);
			$data = $this->get_row();
			$this->free();
			return $data;

		} else {
			$this->query($query);

			$rows = array();
			while($row = $this->get_row()) {
				$rows[] = $row;
			}

			$this->free();

			return $rows;
		}
	}

	/**Всегда возвращает 1, т.к. база данных не поддерживает подсчет результатов запроса
	@param $query_id -- целое число, дескриптор результата.*/
	function num_rows($query_id = '') 
	{
		if ($query_id == '') $query_id = $this->query_id;
		return $query_id->rowCount();
	}

	/**Возвращает номер последней вставленной записи. В качестве параметра можно передать код запроса. По умолчанию используется
	код последненго запроса.*/
	function insert_id() {
		return $this->db_id->lastInsertID();
	}
	
	/**
	 * Метод возвращает количество строк затронутых при выполнении последней операции.
	 * \since версия класса 1.1, 25.11.2008
	 * \author blade39 <blade39@kolosstudio.ru>
	 * \return количество затронутых строк или -1 в случае ошибки.
	 */
	function AffectedRows()
	{
		return $this->db_id->changes();
	}

	/**Возвращает список полей результата запроса. В качестве параметра можно передать код запроса. По умолчанию используется
	код последненго запроса.
	@param $query_id -- целое число, дескриптор результата.*/
	function get_result_fields($query_id = '') 
	{

		if ($query_id == '') $query_id = $this->query_id;
		$count=$query_id->numColumns();
		for($i=0;$i<$count;$i++)
		{
			$fields[]=$query_id->columnName($i);
		}
		return $fields;
   	}

	/**Преобразует переданную строку в sql безопасный вид. Производит предварительное
	 * отсечение слэшей, если включен magic_quotes.
	@param $source -- строка, данные требующие обработки.*/
	function safesql( $source ) {
//		if ($this->db_id) return mysql_real_escape_string ($source, $this->db_id);
//		else return mysql_escape_string($source);
		if(ini_get('magic_quotes_gpc')==1)
		{
			$source=stripslashes($source);
		}
		//return $this->db_id->escapeString($source);
		return $source;
	}

	/**Ничего не делает
	@param $query_id -- целое число, дескриптор результата.*/
	function free( $query_id = '' ) {
		if ($query_id == '') $query_id = $this->query_id;
		$query_id->finalize();
	}

	/**Закрывает соединение с базой данных.*/
	function close() {
		$this->db_id->close();
	}

	/**Выводит ошибку исполнения SQL. В качестве параметров можно передать текст ошибки, код ошибки, запрос при 
	котором произошла ошибка.
	@param $error -- текст ошибки;
	@param $error_num -- код ошибки;
	@param $query -- sql запрос.*/
	function display_error($error, $error_num, $query = '') {
		if($query) {
			// Safify query
			$query = preg_replace("/([0-9a-f]){32}/", "********************************", $query); // Hides all hashes
			$query_str = "$query";
		}

		echo '<?xml version="1.0" encoding="iso-8859-1"?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<title>SQLite Fatal Error</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<style type="text/css">
		<!--
		body {
			font-family: Verdana, Arial, Helvetica, sans-serif;
			font-size: 10px;
			font-style: normal;
			color: #000000;
		}
		-->
		</style>
		</head>
		<body>
			<font size="4">SQLite Error!</font>
			<br />------------------------<br />
			<br />

			<u>The Error returned was:</u>
			<br />
				<strong>'.$error.'</strong>

			<br /><br />
			</strong><u>Error Number:</u>
			<br />
				<strong>'.$error_num.'</strong>
			<br />
				<br />

			<textarea name="" rows="10" cols="52" wrap="virtual">'.$query_str.'</textarea><br />

		</body>
		</html>';

		exit();
	}
}

class sqlite3row
{
	protected $arRows;
	protected $res;
	protected $items;
	protected $current;
	protected $bComplete;
	
	function __construct($res)
	{
		$this->res=$res;
		$this->items=0;
		$this->arRows=array();
		$this->current=0;
		$bComplete=false;
	}
	
	function fetch()
	{
		if(!$this->bComplete)
		{
			if(!$this->res) return false;
			if($data=$this->res->fetch())
			{
				$this->items++;
				$this->current++;
				$this->arRows[$this->current]=$data;
				return $data;
			}
			else
			{
				$this->bComplete=true;
				return false;
			}
		}
		else
		{
			$this->current++;
			return $this->arRows[$this->current-1];
		}
	}
	
	function rowCount()
	{
		if($this->bComplete) return $this->items;
		while($arRow=$this->fetch())
		{
			//print_r($arRow);
		}
		$this->current=1;
		$bComplete=true;
		return $this->items;
	}
}
?>
