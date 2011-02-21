<?php
/**
 * @file class.CErrorsParser.php
 * Файл для парсера файлов текстовых констант
 * Файл проекта kolos-cms.
 * 
 * Создан 26.10.2009
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.5.2
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/main/libs/class.CWhere.php';

class CErrorsParser extends CBaseList
{
	protected $arList;
	protected $bLoad;
	protected $sFilename;
	public $arFields;
	private $arOrder;
	private $arSort;
	
	function __construct($filename)
	{
		if(!preg_match('#^[a-z_\-\.]+$#',$filename)) throw new CError('SYSTEM_FILE_NOT_FOUND');
		$this->arList=array();
		$this->bLoad=false;
		$this->sFilename=$filename;
		$this->LoadFromFile($filename);
		$this->arFields=array('text_ident','ru');
	}
	
	protected function _ParseItem(&$item)
	{return true;}
	
	/**
	 * Метод выполняет загрузку данных из файла
	 */
	function LoadFromFile($filename=false)
	{
		if(!$filename) $filename=$this->sFilename;
		$this->sFilename=$filename;
		if(!preg_match('#^[a-z_\-\.]+$#',$filename)) throw new CError('SYSTEM_FILE_NOT_FOUND');
		if($sList=@file_get_contents(SYS_TEMPLATES_DIR.'/configs/'.$filename))
		{
			$arStrings=explode("\n",$sList);
			foreach($arStrings as $sLine)
			{
				$arRow=explode('=',$sLine);
				if(is_array($arRow)&&trim($arRow[0])!='')
				{
					$this->arList[trim(array_shift($arRow))]=array('ru'=>htmlspecialchars(trim(join('=',$arRow))));
				}
			}
			$this->bLoad=true;
		}
	}
	
	/**
	 * Метод выполняет подгрузку данных для локали
	 */
	public function LoadLocale($locale)
	{
		if(!$this->bLoad) return false;
		if(!preg_match('#^[a-z0-9]+$#',$locale)) return false;
		$filename=str_replace('.conf','_'.$locale.'.conf',$this->sFilename);
		if($sList=@file_get_contents(SYS_TEMPLATES_DIR.'/configs/'.$filename))
		{
			$arStrings=explode("\n",$sList);
			foreach($arStrings as $sLine)
			{
				$arRow=explode('=',$sLine);
				if(is_array($arRow)&&trim($arRow[0])!='')
				{
					if(array_key_exists(trim($arRow[0]),$this->arList))
						$this->arList[trim(array_shift($arRow))]['user']=htmlspecialchars(trim(join('=',$arRow)));
				}
			}
			$this->arFields[]=$locale;
			return true;
		}
		return false;
	}
		
	/**
	 * Внутренний метод для сортировки массива
	 */
	private function _Sort($a,$b)
	{
		if(is_array($this->_arSort))
		{
			if($a[key($this->_arSort)]>$b[key($this->_arSort)])
				return current($this->_arSort)=='desc'?-1:1;
			elseif($a[key($this->_arSort)]<$b[key($this->_arSort)])
				return current($this->_arSort)=='desc'?1:-1;
			else return 0;
		}
		return 0;
	}
	
	/**
	 * Метод выполняет сохранение констант
	 */
	public function Save($locale,$data=false)
	{
		if(!$this->bLoad) return false;
		if(!preg_match('#^[a-z0-9]+$#',$locale)) return false;
		$filename=preg_replace('#\.conf$#i','_'.$locale.'.conf',$this->sFilename);
		if(!$data) $data=$_POST;
		$sResult='';
		foreach($this->arList as $key=>$value)
		{
			if(array_key_exists($key,$data['user']))
			{
				$sResult.=$key.'='.htmlspecialchars_decode($data['user'][$key])."\n";
			}
			elseif($value['user']!='')
			{
				$sResult.=$key.'='.htmlspecialchars_decode($value['user'])."\n";
			}	
			else
			{
				$sResult.=$key.'='.htmlspecialchars_decode($value['ru'])."\n";
			}
		}
		return @file_put_contents(SYS_TEMPLATES_DIR.'/configs/'.$filename,$sResult);
	}
	
	/**
	 * Метод выполняет подсчет количества записей
	 */
	public function Count($arFilter = false, $fGroup = false)
	{
		if(!$this->bLoad) $this->LoadFromFile();
		if(!is_array($this->arList)||count($this->arList)==0) return 0;
		$obWhere=new CWhere($this->arFields);
		$arResult=array();
		$iCount=0;
		foreach($this->arList as $key=>$arRow)
		{
			$bAdd=1;
			$arNewRow=array_merge(array('text_ident'=>$key),$arRow);
			if($arFilter!=false)
			{
				foreach($arFilter as $sOperation=>$sValue)
				{
					$arAction=$obWhere->GetOperation($sOperation,$sValue);
					$bAdd*=$obWhere->doIf($arAction,$arNewRow);
					if(!$bAdd) break;
				}								
			}
			if(!$bAdd) continue;
			$iCount++;
		}
		$this->iCount=$iCount;
		return $iCount;
	}
	/**
	 * Метод выполняет получение списка записей по фильтру
	 */
	function GetList($arOrder=false,$arFilter=false,$limit=false,$arSelect=false,$arGroupBy=false)
	{
		if(!$this->bLoad) $this->LoadFromFile();
		if(!is_array($this->arList)||count($this->arList)==0) return false;
		$obWhere=new CWhere($this->arFields);
		$arResult=array();
		$iCount=0;
		$this->_arSort=$arOrder;
		foreach($this->arList as $key=>$arRow)
		{
			$bAdd=1;
			$arNewRow=array_merge(array('text_ident'=>$key),$arRow);
			if($arFilter!=false)
			{
				foreach($arFilter as $sOperation=>$sValue)
				{
					$arAction=$obWhere->GetOperation($sOperation,$sValue);
					$bAdd*=$obWhere->doIf($arAction,$arNewRow);
					if(!$bAdd) break;
				}								
			}
			if(!$bAdd) continue;
			$arResult[$iCount]=$arNewRow;
			$iCount++;
		}
		//Если ничего не выбрали в фильтре, вернем false
		if(count($arResult)==0) return false;
		//Упорядычиваем
		if(is_array($arOrder))
		{
			uasort($arResult,array($this,'_Sort'));
		}
		//Обрезаем количество по заданному параметру
		if($limit)
		{
			if(is_array($limit))
			{
				$arResult=array_slice($arResult,$limit[0],$limit[1]);
				$iCount=$limit[1];
			}
			elseif($limit>0)
			{
				$arResult=array_slice($arResult,0,$limit);
				$iCount=$limit;
			}
		}
		return $arResult;
	}
}
?>
