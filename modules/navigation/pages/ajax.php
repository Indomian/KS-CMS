<?php
/**
 * @file navigation/pages/ajax.php
 * Файл возвращает список пунктов меню в виде json ответа
 * Файл проекта kolos-cms.
 *
 * Изменен 14.01.2011
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CModuleAdmin.php';
require_once MODULES_DIR.'/navigation/libs/class.CNav.php';

class CnavigationAIajax extends CModuleAdmin
{
	private $iCurSection;
	private $iParentId;
	private $oElement;

	function __construct($module='navigation',&$smarty,&$parent)
	{
		parent::__construct($module,$smarty,$parent);
		$this->oElement=new CNavElement();
		$this->iCurSection=0;
		$this->iParentId=0;
	}

	/**
	 * Метод генерирует список пунктов меню
	 */
	function MenuItems()
	{
		/* Формирование выходного массива для вывода */
		$arRes = array();
		/* Нулевой элемент будет содержать id родителя */
		$arRes[] = array("parent_id" => $this->iParentId);
		/* Определение вложенных пунктов */
		$arFilter = array(
			'type_id' => $this->iCurSection,
			'parent_id' => $this->iParentId
		);
		$arSelect = array('id', 'anchor', 'active');
		$arOrder = array('orderation'=>'asc');
		if($arResult = $this->oElement->GetList($arOrder, $arFilter, false, $arSelect))
		{
			/* Просматриваем результаты выборки */
			foreach ($arResult as $arItem)
			{
				/* Добавление пункта меню в результирующий массив */
				$arRes[] = $arItem;
			}
		}
		return $arRes;
	}

	function Sort()
	{
		try
		{
			/* Определение id пункта меню, который разворачиваем */
			$iParentId = intval($_REQUEST['pid']);
			if($_REQUEST['bid']=='last')
			{
				$iBeforeId='last';
			}
			else
			{
				$iBeforeId = intval($_REQUEST['bid']);
			}
			$iId=intval($_REQUEST['id']);
			$iTypeId=intval($_REQUEST['tid']);

			/* Ищем переносимый элемент*/
			$arElement=$this->oElement->GetRecord(array('id'=>$iId));

			if(!is_array($arElement)) throw new CError('NAVIGATION_RECORD_NOT_FOUND');
			/* Изменяем вложенность элемента если его переместили*/
			if($arElement['parent_id']!=$iParentId)
			{
				$arElement['parent_id']=$iParentId;
				$this->oElement->Update($arElement['id'],array('parent_id'=>$iParentId));
			}

			/* Определение вложенных пунктов */
			$arFilter = array('parent_id' => $iParentId,'type_id'=>$iTypeId);
			$arOrder = array('orderation'=>'asc');
			if($arResult = $this->oElement->GetList($arOrder, $arFilter, false))
			{
				/* Формирование выходного массива для вывода */
				$arRes = array();
				/* Просматриваем результаты выборки */
				$index=0;
				foreach ($arResult as $key=>$arItem)
				{
					$arItem['orderation']=$index;
					$arRes[$arItem['id']]=$arItem;
					$index+=10;
				}
				if($iBeforeId=='last')
				{
					$arRes[$iId]['orderation']=$index;
				}
				else
				{
					$arRes[$iId]['orderation']=$arRes[$iBeforeId]['orderation']-5;
				}
				uasort($arRes,array('CNavElement','_sort'));
				$index=0;
				foreach($arRes as $arItem)
				{
					$this->oElement->Update($arItem['id'],array('orderation'=>$index));
					$index+=10;
				}
			}
			return null;
		}
		catch(CError $e)
		{
			return 1;
		}
	}

	function Run()
	{
		if($this->obUser->GetLevel($this->module)>0) throw new CAccessError('NAVIGATION_ACCESS_DENIED');
		$sAction=$_GET['action'];
		$arResult=array();
		/* Определение типа меню (его id) */
		$this->iCurSection = intval($_REQUEST['CSC_id']);
		/* Определение id пункта меню, который разворачиваем */
		$this->iParentId = intval($_REQUEST['CSC_parid']);

		switch($sAction)
		{
			case 'sort':
				$arResult=$this->Sort();
			break;
			default:
				$arResult=$this->MenuItems();
			break;
		}
		echo json_encode($arResult);
		die();
	}
}