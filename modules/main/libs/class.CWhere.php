<?php
/**
 * @file class.CWhere.php
 * Файл контейнер для класса CWhere
 * Файл проекта kolos-cms.
 * 
 * Создан 08.09.2009
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 1.1
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

/**
 * Класс CWhere выполняет операции связанные с генерацией параметров фильтра
 * и анализом поступивших значений
 */
class CWhere extends CBaseObject
{
	/**
	 * Список полей доступных для поиска значений
	 */
	private $arFields;
	
	/**
	 * Конструктор класса, в качестве параметров необходимо передать список полей
	 * по которым сможет работать фильтр.
	 * @param $arFields array массив полей по которым может работать фильтр
	 */
	function __construct($arFields)
	{
		$this->arFields=$arFields;
	}
	
	/**
	 * Метод выполняет получение операции по переданному полю и значению. В качестве
	 * результата работы возвращает массив указывающий что делать с тим полем
	 * @param $field имя поля в фильтре, может принимать специальные знаки, так же смотрите {@link CObject::_GenWhere() CObject::_GenWhere()}
	 * @param $value значение поля
	 * @return array массив для выполнения операции сравнения
	 */
	function GetOperation($field,$value)
	{
		global $ks_db;
		$noSafe=true;
		if(substr($field,0,1)=='?')
		{
			$noSafe=true;
			$field=substr($field,1);
		}
		if(preg_match('#^(!~|[><!~=]|>=|<=|->|is)?([\w_\.\-]+)#i',$field,$matches))
		{
			$operation=$matches[1];
			if($operation=='') $operation="=";
			$myfield=$matches[2];
			if($operation=='!')	$arResult=array($myfield,'!=',$value);
			elseif($operation=='~') $arResult=array($myfield, "strpos()!==false", $value);
			elseif($operation=='!~') $arResult=array($myfield, "strpos()===false", $value);
			elseif($operation=='->') $arResult=array($myfield, "in_array()",$value);
			else $arResult=array($myfield, $operation, $value);
		}
		return $arResult;
	}
	
	/**
	 * Метод выполняет операцию сравнения, в качестве параметров передаются, массив для
	 * обработки операции и данные которые необходимо обработать. Возвращает 0 или 1
	 * в зависимости от того, прошли данные проверку или нет
	 * @param $arIf array - массив операции, обычно используется результат вызова метода CWhere::GetOperation()
	 * @param $data array - массив данных в котором необходимо выполнить проверку
	 * @return int 1 или 0, если 1 - данные прошли через фильтр, 0 - не прошли.
	 */
	function doIf($arIf,$data)
	{
		if(!is_array($arIf)) return 1;
		if($arIf[1]=='!=') return $data[$arIf[0]]!=$arIf[2];
		if($arIf[1]=='=') return $data[$arIf[0]]==$arIf[2];
		if($arIf[1]=='>') return $data[$arIf[0]]>$arIf[2];
		if($arIf[1]=='<') return $data[$arIf[0]]<$arIf[2];
		if($arIf[1]=='>=') return $data[$arIf[0]]>=$arIf[2];
		if($arIf[1]=='<=') return $data[$arIf[0]]<=$arIf[2];
		if($arIf[1]=='strpos()===false') return strpos($data[$arIf[0]],$arIf[2])===false;
		if($arIf[1]=='strpos()!==false') return strpos($data[$arIf[0]],$arIf[2])!==false;
		if($arIf[1]=='in_array()') return in_array($arIf[2],$data[$arIf[0]]);
		return 1;
	}
}
?>
