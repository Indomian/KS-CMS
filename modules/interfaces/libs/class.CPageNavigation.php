<?php
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/interfaces/libs/class.CInterface.php';

/**
 * Класс для работы с постраничной навигацией. Осуществляет обработку переданных данных или
 * взятие их из строки запроса, так же возможна работа с потомками класса CObect для автоматической
 * генерации страниц. При инициализации встраивается функция смарти, осуществляющая вывод постраничной навигации.
 */
class CPageNavigation extends CInterface
{
	var $iPages;
	var $iItems;
	var $iVisible;
	var $iCurrent;
	var $obGeneral;
	var $iPageNavNum;
	static $pageNavNum;

	/**
	 * Конструктор - производит создание объекта постраничной навигации,
	 * при создании учитывает номер созданного объекта.
	 */
	function __construct($General=NULL,$Items=false,$Visible=false,$Current=false)
	{
		$this->obGeneral=NULL;
		$this->iItems=0;
		$this->iVisible=0;
		$this->iPages=0;
		$this->iCurrent=0;
		CPageNavigation::$pageNavNum++;
		$this->iPageNavNum=CPageNavigation::$pageNavNum;
		if($General!=NULL)
			$this->obGeneral=$General;
		if($Items!=false)
			$this->iItems=$Items;
		if($Visible!=false)
			$this->iVisible=$Visible;
		elseif(array_key_exists('n',$_REQUEST))
			$this->iVisible=$_REQUEST['n'];
		if($this->iVisible<1) $this->iVisible=20;
		if($Current!=false)
			$this->iCurrent=$Current-1;
		elseif(array_key_exists('p'.$this->iPageNavNum,$_REQUEST))
			$this->iCurrent=$_REQUEST['p'.$this->iPageNavNum]-1;
		if($this->iVisible>0)
			$this->iPages=ceil($this->iItems/$this->iVisible);
	}

	/**
	 * Метод возвращает массив для ограничения выборки.
	 * @param $items - количество элементов в выборке или false для автоматического определения
	 * @return array - массив ограничения записей в выборке
	 */
	function GetLimits($items=false)
	{
		if(($items==false)&&($this->obGeneral!=NULL))
			$this->iItems=$this->obGeneral->items;
		else
			$this->iItems=$items;
		$this->iPages=ceil($this->iItems/$this->iVisible);
		if($this->iCurrent*$this->iVisible>$this->iItems)
			$this->iCurrent=0;
		$arResult=array($this->iCurrent*$this->iVisible,$this->iVisible);
		return $arResult;
	}

	/**
	 * Возвращает количество видимых элементов.
	 * @return integer - количество видимых элементов
	 */
	function GetVisible()
	{
		return $this->iVisible;
	}

	/*!Возвращает массив страниц для последующей обработки в смарти.*/
	function GetPages($items=false)
	{
		if(($items==false)&&($this->obGeneral!=NULL))
			$this->iItems=$this->obGeneral->items;
		elseif($items!=false)
			$this->iItems=$items;
		$this->iPages=ceil($this->iItems/$this->iVisible);
		$pages=array();
		$pages['num']=$this->iPages;
		$pages['active']=$this->iCurrent+1;
		$pages['visible']=$this->iVisible;
		$pages['TOTAL']=$this->iItems;
		$pages['index']=$this->iPageNavNum;
		if ($pages['active']==0) $pages['active']=1;
		for($i=1;$i<=$pages['num'];$i++)
			$pages['pages'][$i]=$i;
		return $pages;
	}

	/**
	 * Метод производит поиск номера страницы на которой будет выведен элемент id
	 * из списка элементов $arIds
	 */
	function SearchPage($id,$arIds)
	{
		$iPages=ceil(count($arIds)/$this->iVisible);
		$pages=array();
		$pages['num']=$iPages;
		$pages['active']=1;
		$pages['visible']=$this->iVisible;
		$pages['TOTAL']=count($arIds);
		$pages['index']=$this->iPageNavNum;
		$iCount=1;
		foreach($arIds as $arItem)
		{
			$iCount++;
			if($arItem==$id) break;
			if($iCount>$this->iVisible)
			{
				$pages['active']++;
				$iCount=1;
			}
		}
		for($i=1;$i<=$pages['num'];$i++)
			$pages['pages'][$i]=$i;
		return $pages;
	}
}

