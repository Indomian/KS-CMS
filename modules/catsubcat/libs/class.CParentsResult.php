<?php
/**
 * \file class.CParentsResult.php
 * В файле находится класс работающий с древовидными результатами
 * Файл проекта kolos-cms.
 * 
 * Создан 16.06.2009
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 1.0
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

class CParentsResult extends CBaseObject
{
	protected $arParents;
	/**
	 * Конструктор класса получает на входе массив родительских записей
	 */
	function __construct($arData)
	{
		$this->arParents=$arData;
	}
	
	/**
	 * Метод возращает запись с указанным номером из массива
	 * или все записи если номер не указан
	 */
	function Get($i=false)
	{
		if($i!==false)
		{
			if($i>=0)
			{
				return $this->arParents[$i];
			}
			else
			{
				return $this->arParents[count($this->arParents)+$i];
			}
		}
		return $this->arParents;
	}
}

/**
 * Класс для работы с деревьями категорий
 */
class CCategoryParentsResult extends CParentsResult
{
	function GetFullPath($root='')
	{
		$path='';
		foreach($this->arParents as $arItem)
		{
			if($arItem['text_ident']!='')
				$path.=$arItem['text_ident'].'/';
		}
		return $root.$path;
	}
	
	function GetNavChain($root)
	{
		$arResult=array();
		$path='';
		foreach($this->arParents as $arItem)
		{
			if($arItem['text_ident']!='')
			{
				$path.=$arItem['text_ident'].'/';
				$arResult[]=array(
					'title'=>$arItem['title'],
					'text_ident'=>$arItem['text_ident'],
					'parent_id'=>$arItem['parent_id'],
					'id'=>$arItem['id'],
					'path'=>$root.$path,
				);
			}
		}
		return $arResult;
	}
	
	/**
	 * Метод возвращает массив с id родительских элементов
	 */
	function GetParentsIds()
	{
		if (count($this->arParents) >= 2)
		{
			$temp_parents = $this->arParents;
			array_pop($temp_parents);
			$parents_ids = array();
			foreach ($temp_parents as $temp_parent)
				$parents_ids[] = $temp_parent['id'];
			return ($parents_ids);
		}
		return false;
	}
}
?>
