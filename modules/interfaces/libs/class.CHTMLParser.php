<?php
/**
 * @file class.CHTMLParser.php
 * Файл выполняющий парсинг html таким образом, чтобы обезопасить содержимое
 * Файл проекта kolos-cms.
 *
 * Создан 06.05.2011
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/interfaces/libs/class.CTextParser.php';

class CHTMLParser extends CTextParser
{
	private $sParseError;
	private $iParseError;
	protected $arAvailableTags;

	function __construct()
	{
		$sParseError='';
		$iParseError=0;
		//Список допустимых тэгов
		$this->arAvailableTags=array('p','em','strong','i','b','span','img');
	}

	private function ParseDoc($obElement)
	{
		$name=$obElement->nodeName;
		$sResult='';
		switch($name)
		{
			case 'p':
			case 'em':
			case 'strong':
			case 'i':
			case 'b':
			case 'span':
				if(!in_array($name,$this->arAvailableTags))
				{
					$sBegin='';
					$sEnd='';
				}
				else
				{
					$sBegin='<'.$name;
					$arAvailableAttrs=array('style','title','align');
					foreach($arAvailableAttrs as $sAttr)
					{
						if($obElement->hasAttribute($sAttr))
						{
							$value=$obElement->getAttribute($sAttr);
							$value=str_replace('"',"&quot;",$value);
							$sBegin.=' '.$sAttr.'="'.$value.'" ';
						}
					}
					$sBegin.='>';
					$sEnd='</'.$name.'>';
				}
				$sContent='';
				if($obElement->hasChildNodes())
				{
					foreach($obElement->childNodes as $obChild)
					{
						$sContent.=$this->ParseDoc($obChild);
					}
				}
				if(strlen($sContent)>0)
				{
					$sResult.=$sBegin.$sContent.$sEnd;
				}
			break;
			case 'a':
				if(!in_array($name,$this->arAvailableTags))
				{
					$sBegin='';
					$sEnd='';
				}
				else
				{
					$sBegin='<'.$name;
					$arAvailableAttrs=array('style','title','align','href','name');
					foreach($arAvailableAttrs as $sAttr)
					{
						if($obElement->hasAttribute($sAttr))
						{
							$value=$obElement->getAttribute($sAttr);
							$value=str_replace('"',"&quot;",$value);
							if($sAttr=='href')
							{
								if(preg_match('#^java#i',$value)) continue;
							}
							$sBegin.=' '.$sAttr.'="'.$value.'" ';
						}
					}
					$sBegin.='>';
					$sEnd='</'.$name.'>';
				}
				$sContent='';
				if($obElement->hasChildNodes())
				{
					foreach($obElement->childNodes as $obChild)
					{
						$sContent.=$this->ParseDoc($obChild);
					}
				}
				if(strlen($sContent)>0)
				{
					$sResult.=$sBegin.$sContent.$sEnd;
				}
			break;
			case 'img':
				$sResult.='<'.$name;
				$arAvailableAttrs=array('style','title','align','src','alt','width','height');
				foreach($arAvailableAttrs as $sAttr)
				{
					if($obElement->hasAttribute($sAttr))
					{
						$value=$obElement->getAttribute($sAttr);
						$value=str_replace('"',"&quot;",$value);
						if($sAttr=='src')
						{
							if(preg_match('#^java#i',$value)) continue;
						}
						$sResult.=' '.$sAttr.'="'.$value.'" ';
					}
				}
				$sResult.='/>';
			break;
			case '#text':
				$sResult.=$obElement->nodeValue;
			break;
			case '#document':
			default:
				if($obElement->hasChildNodes())
				{
					foreach($obElement->childNodes as $obChild)
					{
						$sResult.=$this->ParseDoc($obChild);
					}
				}
				//$sResult.=$obElement->nodeValue;
		}
		return $sResult;
	}

	/**
	 * Метод выполняет обработку текста в html, на выходе возвращает безопасный текст
	 * в случае если текст не удалось обработать - возвращает значение с обрезанными тэгами
	 * @param string $text текст который необходимо преобразовать
	 */
	function Parse($text)
	{
		//$text = str_replace('&nbsp;',' ',$text);
		//echo htmlspecialchars($text)."<br/>";
		/*$data='<?xml version="1.0" encoding="UTF-8"?><data>'.$text.'</data>';*/
		$data='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> <html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head><body>'.$text.'</body></html>';
		try
		{
			$doc = new DOMDocument('1.0','UTF-8');
			if(@$doc->loadHTML($data))
			{
				$sNewText=$this->ParseDoc($doc);
			}
			else
			{
				throw new Exception('INTERFACES_HTML_PARSER_ERROR');
			}
		}
		catch(Exception $e)
		{
			$this->sParseError=$e->getMessage();
			$this->iParseError=$e->getCode();
			$sNewText=strip_tags($text);
		}
		return $sNewText;
	}

	function Convert($text)
	{

	}
}