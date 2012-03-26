<?php
/**
 * Базовый класс для различных объектов списков
 *
 * @filesource main/libs/class.CBaseList.php
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/main/libs/class.CBaseObject.php';

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
	 * @return array
	 */
	function GetFields()
	{
		return $this->arFields;
	}

	/**
	 * Метод создает массив данных из данных переданных в пост с префиксом.
	 * @param $prefix - префикс к данным
	 * @param $data - ассоциативный массив данных обычно $_POST
	 * @return ассоциативный массив с заполнеными полями данных
	 */
	public function GetFromPost($prefix,$data)
	{
		$arResult=array();
		foreach($this->arFields as $field)
			if(array_key_exists($prefix.$field,$data))
				$arResult[$field]=$data[$prefix.$field];
		return $arResult;
	}
}
