<?php
/**
 * @file class.CBaseAPI.php
 * Файл содержит класс для работы с апи различных модулей
 * Файл проекта kolos-cms.
 *
 * Создан 16.03.2010
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.5.5
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

/**
 * Абстрактный класс реализующий различные функции АПИ
 */
abstract class CBaseAPI extends CBaseObject
{
	protected $obDB;
	
	function __construct()
	{
		global $ks_db;
		$this->obDB=$ks_db;
	}
	
	/**
	 * Метод вычисляет значения каких ключей двух массивов отличаются друг от друга
	 */
	function GetDifference($ar1,$ar2)
	{
		$arDifferent=array();
		foreach($ar1 as $key=>$value)
			if(array_key_exists($key,$ar2))
			{
				if($value!=$ar2[$key]) $arDifferent['both'][]=$key;
				unset($ar2[$key]);
			}
			else
				$arDifferent['infirst'][]=$key;
		if(count($ar2)>0)
			$arDifferent['insecond']=array_keys($ar2);
		return $arDifferent;
	}
}

