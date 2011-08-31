<?php

if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

/**
 * Класс выполняет генерацию координат для ресайза картинки
 */
class CRectGenerator
{
	protected $arSource;
	protected $arResult;

	function __construct($arSource,$arResult,$rWidth=false,$rHeight=false)
	{
		if(is_array($arSource) && is_array($arResult))
		{
			$this->arSource=$arSource;
			$this->arResult=$arResult;
		}
		elseif($rWidth===false && $rHeight===false)
		{
			$this->arResult=array('width'=>$arSource,'height'=>$arResult);
			$this->arSource=false;
		}
		else
		{
			$this->arSource=array('width'=>$arSource,'height'=>$arResult);
			$this->arResult=array('width'=>$rWidth,'height'=>$rHeight);
		}
	}

	function SetSourceSize($w,$h)
	{
		$this->arSource=array('width'=>$w,'height'=>$h);
	}

	/**
	 * Метод возвращает координаты по простому способу
	 */
	function GetCoord()
	{
		if(!$this->arSource) return false;
		$arResult=array(
			'x'=>0,
			'y'=>0,
			'w'=>$this->arSource['width'],
			'h'=>$this->arSource['height'],
			'x1'=>0,
			'y1'=>0,
			'w1'=>$this->arResult['width'],
			'h1'=>$this->arResult['height']
		);
		return $arResult;
	}
}

class CScale extends CRectGenerator
{
	/**
	 * Метод возвращает координаты для усечения по центру
	 */
	function GetCoord()
	{
		$arResult=parent::GetCoord();
		if($arResult['w1']==0 && $arResult['h1']==0) return false;
		$fProp=$this->arSource['width']/$this->arSource['height'];
		if($arResult['h1']==0)
		{
			$arResult['h1']=$arResult['w1']/$fProp;
		}
		elseif($arResult['w1']==0)
		{
			$arResult['w1']=$arResult['h1']*$fProp;
		}
		if($arResult['w1']>$this->arSource['width'] || $arResult['h1']>$this->arSource['height'])
		{
			$arResult['w1']=$this->arSource['width'];
			$arResult['h1']=$this->arSource['height'];
		}
		return $arResult;
	}
}

class CCropToCenter extends CRectGenerator
{
	/**
	 * Метод возвращает координаты для усечения по центру
	 */
	function GetCoord()
	{
		$arResult=parent::GetCoord();
		//Считаем пропорции оригинала и результата
		$fProp=$this->arSource['width']/$this->arSource['height'];
		$fRProp=$this->arResult['width']/$this->arResult['height'];
		if($fRProp>$fProp)
		{
			//Если пропорции результата больше (т.е. ширина важнее)
			$scale=$this->arResult['width']/$this->arSource['width'];
			$iScaledHeight = round($this->arSource['height']*$scale);
			if($iScaledHeight>$this->arResult['height'])
			{
				//Высота ресайза больше высоты результата
				$arResult['y']=round(($iScaledHeight-$this->arResult['height'])/2/$scale);
				$arResult['h']=round($this->arResult['height']/$scale);
			}
		}
		else
		{
			//Пропорции исходного больше, значит важнее высота
			$scale=$this->arResult['height']/$this->arSource['height'];
			$iScaledWidth = round($this->arSource['width']*$scale);
			if($iScaledWidth>$this->arResult['width'])
			{
				//Если ширина картинки оказалась больше чем допустимая ширина
				//То надо посчитать смещение и изменить выводимую ширину
				$arResult['x']=round(($iScaledWidth-$this->arResult['width'])/2/$scale);
				$arResult['w']=round($this->arResult['width']/$scale);
			}
		}
		return $arResult;
	}
}

class CCropToTop extends CRectGenerator
{
	/**
	 * Метод возвращает координаты для усечения по центру или по верхнему краю
	 */
	function GetCoord()
	{
		$arResult=parent::GetCoord();
		//Считаем пропорции оригинала и результата
		$fProp=$this->arSource['width']/$this->arSource['height'];
		$fRProp=$this->arResult['width']/$this->arResult['height'];
		if($fRProp>$fProp)
		{
			//Если пропорции результата больше (т.е. ширина важнее)
			$scale=$this->arResult['width']/$this->arSource['width'];
			$iScaledHeight = round($this->arSource['height']*$scale);
			if($iScaledHeight>$this->arResult['height'])
			{
				//Высота ресайза больше высоты результата
				$arResult['y']=0;
				$arResult['h']=round($this->arResult['height']/$scale);
			}
		}
		else
		{
			//Пропорции исходного больше, значит важнее высота
			$scale=$this->arResult['height']/$this->arSource['height'];
			$iScaledWidth = round($this->arSource['width']*$scale);
			if($iScaledWidth>$this->arResult['width'])
			{
				//Если ширина картинки оказалась больше чем допустимая ширина
				//То надо посчитать смещение и изменить выводимую ширину
				$arResult['x']=round(($iScaledWidth-$this->arResult['width'])/2/$scale);
				$arResult['w']=round($this->arResult['width']/$scale);
			}
		}
		return $arResult;
	}
}
