<?php
/**
 * @filesource catsubcat/libs/class.CCatsubcatAPI.php
 * Файл содержит в себе класс АПИ модуля catsubcat
 * Файл проекта kolos-cms.
 *
 * @since 22.02.2011
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/main/libs/class.CBaseAPI.php';
require_once MODULES_DIR.'/catsubcat/libs/class.CCategory.php';
require_once MODULES_DIR.'/catsubcat/libs/class.CElement.php';

/**
 * Класс обеспечивает высокоуровневые функции для модуля wave
 */
class CCatsubcatAPI extends CBaseAPI
{
	static private $obInstance;
	private $obElement;
	private $obCategory;
	private $obModules;

	/**
	 * Метод заменяющий конструктор. Используется для инициализации.
	 */
	private function init()
	{
		global $KS_MODULES;
		$this->obElement=false;
		$this->obCategory=false;
		$this->obModules=$KS_MODULES;
	}

	/**
	 * This implements the 'singleton' design pattern
   	 *
     * @return object CCatsubcatAPI The one and only instance
     */
  	static function get_instance()
  	{
	    if (!self::$obInstance)
	    {
    		self::$obInstance = new CCatsubcatAPI();
      		self::$obInstance->init();  // init AFTER object was linked with self::$instance
    	}
	    return self::$obInstance;
  	}

	/**
	 * Метод возвращает объект записей
	 * @return CElement - объект обеспечивающий управление записями
	 */
	function Element()
	{
		if(!$this->obElement)
			$this->obElement=new CElement();
		return $this->obElement;
	}

	/**
	 * Метод возвращает объект категорий
	 * @return CCategory - объект обеспечивающий управление категориями
	 */
	function Category()
	{
		if(!$this->obCategory)
			$this->obCategory=new CCategory();
		return $this->obCategory;
	}

	/**
	 * Метод выполняет расчёт количества записей одного уровня
	 * @param $iParentId - номер родительского раздела
	 * @param $arFilter - массив фильтрации, специальный ключ TYPE позволяет указать какие записи выбирать, если cat - только категории, elm - только элементы
	 * @param $bOnlyTotal - режим возврата результата
	 * @return mixed - если $bOnlyTotal==true - вернёт количество элементов, иначе - ассоциативный массив с ключами CATEGORIES, ELEMENTS и TOTAL
	 */
	function CountAllList($iParentId,$arFilter=false,$bOnlyTotal=false)
	{
		$iCat=0;
		$iElm=0;
		$bCat=true;
		$bElm=true;
		if(isset($arFilter['TYPE']))
			if($arFilter['TYPE']=='cat')
				$bElm=false;
			elseif($arFilter['TYPE']=='elm')
				$bCat=false;
		$arFilter['parent_id']=$iParentId;
		if($bCat) $iCat=$this->Category()->Count($arFilter);
		if($bElm) $iElm=$this->Element()->Count($arFilter);
		if($bOnlyTotal)
			return $iCat+$iElm;
		else
			return array(
				'CATEGORIES'=>$iCat,
				'ELEMENTS'=>$iElm,
				'TOTAL'=>$iCat+$iElm
			);
	}

	/**
	 * Метод выполняет выборку всех записей одного уровня (и элементов и разделов).
	 * @param $iParentId - номер родительского раздела
	 * @param $arOrder - массив сортировки
	 * @param $arFilter - массив фильтрации
	 * @param $arLimit - ограничение количества записей
	 * @return array - массив со списком записей или false если записей не найдено
	 */
	function GetAllList($iParentId,$arOrder=false,$arFilter=false,$arLimit=false)
	{
		if (is_array($arLimit))
		{
			$from=$arLimit[0];
			$count=$arLimit[1];
		}
		else
		{
			$from=0;
			$count=$this->obModules->GetConfigVar('main','admin_items_count',20);
		}
		$arResult=$this->CountAllList($iParentId,$arFilter);
		$bCat=true;
		$bElm=true;
		if(isset($arFilter['TYPE']))
			if($arFilter['TYPE']=='cat')
				$bElm=false;
			elseif($arFilter['TYPE']=='elm')
				$bCat=false;
		$arSelect=array('id','title','description','content','orderation','text_ident','parent_id','date_add','date_edit','active','deleted');
		$arResult['IN_PAGE']=$count;
		$arResult['SELECTED']=0;
		$arFilter['parent_id']=$iParentId;
		if ($from<$arResult['CATEGORIES']&&$bCat)
		{
			$arLimit=array($from,$count);
			if($arCategories=$this->Category()->GetList($arOrder,$arFilter,$arLimit,$arSelect))
			{
				foreach($arCategories as $arRow)
				{
					$arRow['TYPE']='cat';
					$arResult['ITEMS'][]=$arRow;
				}
				$arResult['SELECTED']=count($arCategories);
			}
		}
		$arResult['CURRENT_PAGE']=ceil($from/$count)+1;
		if (($arResult['CATEGORIES']-$from)<$count&&$bElm)
		{
			if($arResult['SELECTED']>0)
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
			if($arElements=$this->Element()->GetList($arOrder,$arFilter,$arLimit,$arSelect))
			{
				foreach($arElements as $arRow)
				{
					$arRow['TYPE']='elm';
					$arResult['ITEMS'][]=$arRow;
				}
				$arResult['SELECTED']+=count($arElements);
			}
		}
		if($arResult['SELECTED']==0) return false;
		$arResult['PAGES']=ceil($arResult['TOTAL']/$arResult['IN_PAGE']);
	 	return $arResult;
	}
}