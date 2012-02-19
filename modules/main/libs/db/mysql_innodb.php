<?php
if( !defined('KS_ENGINE') )  die("Hacking attempt!");

require_once MODULES_DIR.'/main/libs/db/mysql.php';
/**
 * Класс выполняет роль интерфейса к базе данных типа mysql при использовании движка таблиц InnoDB. Логгирует все SQL запросы
 * при работе в режиме отладки, также логирует время исполнения этих запросов. Добавлена функция вставки информационных
 * строк в лог запросов, что позволяет отделять из каких методов произведен вызов того или иного запроса.
 * @version 2.6
 * @author BlaDe39 <blade39@kolosstudio.ru>, DNKonev <dnkonev@yandex.ru>, DoTJ <dotj@kolosstudio.ru>
 */
class mysql_innodb extends mysql
{
	function __construct($debug=0)
	{
		parent::__construct($debug);
		$this->sTableType='InnoDB';
	}

	/**
	 * Метод выполняет SQL запрос к базе данных.
	 * Возращает id результата. В случае работы в режиме отката,
	 * записывает последние изменения в базе данных в кэш.
	 * @param $query -- sql запрос;
	 * @param $show_error -- флаг вывода ошибок.
	 */
	function query($query, $show_error=true)
	{
		if(KS_DEBUG==1)
			$time_before = $this->get_real_time();
		if(!$this->connected) $this->connect(DBUSER, DBPASS, DBNAME, DBHOST);

		if(!($this->query_id = @mysql_query($query, $this->ks_db_id) ))
		{
			$this->mysql_error = mysql_error();
			$this->mysql_error_num = mysql_errno();
			throw new CDBError($this->mysql_error, $this->mysql_error_num, $query);
		}

		if(KS_DEBUG==1)
		{
			/*Запись запроса в лог, запись времени его исполнения в лог*/
			$this->add2log($query,$this->get_real_time() - $time_before);
			$this->MySQL_time_taken += $this->get_real_time() - $time_before;
			$this->query_num ++;
		}
		return $this->query_id;
	}

	function begin()
	{
		@mysql_query('BEGIN TRANSACTION');
		$this->iBegin ++;
	}

	function commit()
	{
		@mysql_query('COMMIT');
		$this->iBegin --;
	}

	function rollback($show_error=false)
	{
		@mysql_query('ROLLBACK');
	}
}

