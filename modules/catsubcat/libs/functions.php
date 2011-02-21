<?php
/**
 * KS ENGINE
 * File: /catsubcat/CCategoryEdit.php
 * Original Code by BlaDe39
 * http://www.kolosstudio.ru/
 * (c) 2008
 * Назначение: Управление категориями/подкатегориями (создание редактрирование удаление)
 */

if( !defined('KS_ENGINE') ){ die("Hacking attempt!");}

/**
 * Количество цифр при замене текстового идентификатора
 */
define('MAX_TEXT_IDENT_NUMBERS',7);

/**
 * Подключаем требуемые модули
 */
require_once MODULES_DIR.'/catsubcat/libs/class.CParentsResult.php';
require_once MODULES_DIR.'/catsubcat/libs/class.CCategory.php';
require_once MODULES_DIR.'/catsubcat/libs/class.CElement.php';

/**
 * Функция выполняет выборку всех записей одного уровня (и элементов и разделов).
 * @param $arOrder - массив сортировки
 * @param $arFilter - массив фильтрации
 * @param $arLimit - ограничение количества записей
 * @param $arTables - список таблиц из которых делается выборка, начиная с версии 2.5.5 можно передать объект для
 * категорий и элементов, сохранив их в элементы с ключами category и element соответсвенно.
 * @param $arFilds - неиспользуемый параметр (?)
 * @return array - массив со списком записей
 */
function GetAllList($arOrder,$arFilter,$arLimit,$arTables,$arFilds=false)
{
	global $ks_db,$USER;
	if(is_array($arTables))
	{
		if($arTables['category'] instanceof CCommonCategory) $obCategory=$arTables['category'];
		if($arTables['element'] instanceof CCommonElement) $obElement=$arTables['element'];
	}
	else
	{
		$obCategory=new CCategory($arTables);
		$obElement=new CElement($arTables);
	}
	if(!$obElement instanceof CCommonElement || !$obCategory instanceof CCommonCategory) throw new CError('SYSTEM_STRANGE_ERROR','',__FILE__.':'.__LINE__);

	if (is_array($arLimit))
	{
		$from=$arLimit[0];
		$count=$arLimit[1];
	}
	else
	{
		$from=0;
		$count=20;
	}
	$bCat=true;
	$bElm=true;
	if(array_key_exists('TYPE',$arFilter))
	{
		if($arFilter['TYPE']=='cat')
		{
			$bElm=false;
		}
		if($arFilter['TYPE']=='elm')
		{
			$bCat=false;
		}
		unset($arFilter['TYPE']);
	}
	$arSelect=Array('id','title','description','content','orderation','text_ident','parent_id','date_add','date_edit','active','deleted');
	$arCSelect=array_merge($arSelect,array('access_edit','access_view','access_create'));
	if($bCat) $arResult['CATEGORIES']=$obCategory->Count($arFilter);
	if($bElm) $arResult['ELEMENTS']=$obElement->Count($arFilter);
	$arResult['TOTAL']=$arResult['CATEGORIES']+$arResult['ELEMENTS'];
	$arResult['IN_PAGE']=$count;
	if ($from<$arResult['CATEGORIES']&&$bCat)
	{
		$arLimit=array($from,$count);
		$arCategories=$obCategory->GetList($arOrder,$arFilter,$arLimit,$arCSelect);
		if(is_array($arCategories))
		{
			foreach($arCategories as $arRow)
			{
				$arRow['TYPE']='cat';
				//Устанавливаем какими правами обладает текущий пользователь
				if(in_array($arRow['access_view'],$USER->GetGroups())) $arRow['ACCESS']['view']=1;
				if(in_array($arRow['access_edit'],$USER->GetGroups())) $arRow['ACCESS']['edit']=1;
				if(in_array($arRow['access_create'],$USER->GetGroups())) $arRow['ACCESS']['create']=1;
				$arResult['ITEMS'][]=$arRow;
			}
			$arResult['SELECTED']=count($arCategories);
		}
	}
	$arResult['CURRENT_PAGE']=ceil($from/$count)+1;
	if (($arResult['CATEGORIES']-$from)<$count&&$bElm)
	{
		if ($arResult['SELECTED']>0)
		{
			$from=0;
			$count=$count-$arResult['SELECTED'];
		}
		else
		{
			$from=$arLimit[0]-$arResult['CATEGORIES'];
			$count=$arLimit[1];
		}
		$arLimit=array($from,$count);
		$arElements=$obElement->GetList($arOrder,$arFilter,$arLimit,$arCSelect);
		if(is_array($arElements))
		{
			foreach($arElements as $arRow)
			{
				$arRow['TYPE']='elm';
				//Устанавливаем какими правами обладает текущий пользователь
				if(in_array($arRow['access_view'],$USER->GetGroups())) $arRow['ACCESS']['view']=1;
				if(in_array($arRow['access_edit'],$USER->GetGroups())) $arRow['ACCESS']['edit']=1;
				$arResult['ITEMS'][]=$arRow;
			}
			$arResult['SELECTED']+=count($arElements);
		}
	}
	$arResult['PAGES']=ceil($arResult['TOTAL']/$arResult['IN_PAGE']);
 	return $arResult;
}