<?php
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/interfaces/libs/class.CFrame.php';

/**
 * Класс выводит рамку для фильтрации списков. Автоматически определяет поля фильтрации по
 * переданным в запросе данным, может возвращать значения массива для фильтрации при использовании
 * функций потомков CObject::GetList().
 * @todo определить видимость $arFields и скрыть по возможности
 */
class CFilterFrame extends CFrame
{
	public $arFields; /*!<Список полей по которым осуществляется фильтрация. Содержит массив записей,
					* структура записи такова:
					* arRow=array(
					* 'FIELD' -- имя поля для фильтрации, совпадает с именем поля в БД;
					* 'TYPE' -- тип поля при выводе, допустимые значения: STRING,LIST,CHECKBOX;
					* 'VALUE' -- установленное значение поля;
					* 'VALUES' -- значения для полей типа список, массив где ключ - значение поля в БД, элемент - его название;
					* 'METHOD' -- метод сравнения поля, по умолчанию - равно, также доступны >,<,~,>=,<= и ~ в качестве текстового
					* поиска в поле;
					* )*/

	/*!Конструктор, выполняет работу по получению данных из строки запроса. Все поля получают тип STRING, чтобы изменить
	 * можно воспользоваться функцией SetType*/
	function __construct()
	{
		global $smarty;
		$this->arFields=array();
		if(array_key_exists('fm',$_REQUEST))
			$method=$_REQUEST['fm'];
		else
			$method='';
		$arFrom=array();
		if(strtolower($method)=='post')
			foreach($_POST as $key=>$value)
				$arFrom[str_replace('^','.',$key)]=$value;
		else
		{
			$arGet=explode('&',$_SERVER['QUERY_STRING']);
			foreach($arGet as $arOneGet)
			{
				$arItem=explode('=',$arOneGet);
				$arItem[0]=urldecode($arItem[0]);
				if(preg_match('#^(.*)\[(.*)\]$#i',$arItem[0],$matches))
					$arFrom[$matches[1]][$matches[2]]=urldecode($arItem[1]);
				else
					if(count($arItem)==2)
						$arFrom[urldecode($arItem[0])]=urldecode($arItem[1]);
			}
		}
		$arMatches=array();

		if(array_key_exists('filter',$arFrom) && ($arFrom['filter']==1)&&(!array_key_exists('fundo',$arFrom)))
			foreach($arFrom as $key=>$value)
				if(preg_match('/^ff([<>\w\._]+)$/',$key,$arMatches))
				{
					$arField=array('FIELD'=>$arMatches[1],'VALUE'=>$value,'TYPE'=>'STRING');
					$this->AddField($arField);
				}
	}

	/**
	 * Выполняет добавление одного поля для фильтрации.
	 * Добавлена поддержка значения по умолчанию, используется если не задано значение пользователем.
	 * */
	function AddField($arField)
	{
		if(is_string($arField))
		{
			$arField=array(
				'FIELD'=>$arField,
				'METHOD'=>'=',
			);
		}
		$arNewField=array(
			'FIELD'=>$arField['FIELD'],
			'VALUE'=>'',
			'VALUES'=>array(),
			'METHOD'=>'=',
			'TYPE'=>'STRING',
			'DEFAULT'=>'',
		);
		if(array_key_exists('VALUE',$arField))
			$arNewField['VALUE']=$arField['VALUE'];
		if(array_key_exists('VALUES',$arField))
			$arNewField['VALUES']=$arField['VALUES'];
		if(array_key_exists('METHOD',$arField))
			$arNewField['METHOD']=$arField['METHOD'];
		if(array_key_exists('TYPE',$arField))
			$arNewField['TYPE']=$arField['TYPE'];
		if(array_key_exists('DEFAULT',$arField))
			$arNewField['DEFAULT']=$arField['DEFAULT'];

		if(array_key_exists($arField['FIELD'],$this->arFields))
		{
			$this->arFields[$arField['FIELD']]['VALUES']=$arNewField['VALUES'];
			$this->arFields[$arField['FIELD']]['TYPE']=$arNewField['TYPE'];
			$this->arFields[$arField['FIELD']]['METHOD']=$arNewField['METHOD'];
			if(($this->arFields[$arField['FIELD']]['VALUE']=='') && ($arNewField['DEFAULT']!=''))
			{
				$this->arFields[$arField['FIELD']]['VALUE']=$arNewField['DEFAULT'];
			}
		}
		else
		{
			if($arNewField['DEFAULT']!='' && $arNewField['VALUE']=='')
				$arNewField['VALUE']=$arNewField['DEFAULT'];
			$this->arFields[$arField['FIELD']]=$arNewField;
		}
		return true;
	}

	/*!Выполняет экспорт внутреннего массива фильтрации в переменную смарти.
	 * \param $sName -- имя переменной в шаблоне куда экспортировать значение.*/
	function SetSmartyFilter($sName)
	{
		global $smarty;
		foreach($this->arFields as $arItem)
		{
			if ($arItem['TYPE']=='DATE') $smarty->assign('addCalendar',1);
		}
		if(array_key_exists('showFilter',$_COOKIE))
		{
			$smarty->assign('hideFilter',intval($_COOKIE['showFilter']));
		}
		else
		{
			$smarty->assign('hideFilter',1);
		}
		$smarty->assign($sName,$this->arFields);
	}

	/*!Выполняет удаление одного поля из результата фильтрации.*/
	function RemoveField($sField)
	{
		if(array_key_exists($sField,$this->arFields))
		{
			unset($this->arFields[$sField]);
			return true;
		}
		return false;
	}

	/*!Устанавиливает новый тип поля фильтрации. В качестве параметров передаются, имя поля, тип и значения (необязательно). */
	function SetType($sField,$sType,$arValues=false)
	{
		if(in_array($sField,$this->arFields))
		{
			$this->arFields[$sField]['TYPE']=$sType;
			if($sType=='list') $this->arFields[$sField]['VALUES']=$arValues;
			return true;
		}
		return false;
	}

	/**
	 * Метод проверяет пустое ли значение
	 */
	function IsEmpty($value)
	{
		if(is_string($value)) return $value=='';
		elseif(is_array($value)) return count($value)==0;
		elseif(is_numeric($value)) return false;
		return empty($value);
	}

	/**
	 * Возращает массив для фильтрации списка.
	 * @return array массив где ключи - ключи фильтра, а значения - значения фильтрации
	 */
	function GetFilter()
	{
		global $KS_URL;
		$arResult=array();
		foreach($this->arFields as $key=>$value)
		{
			if(!$this->IsEmpty($value['VALUE']))
			{
				if($value['TYPE']=='DATE')
				{
					if((strlen($value['VALUE'][0])>0)&&
						(strlen($value['VALUE'][1])>0))
					{
						if(preg_match('#([0-9]{2,2})\.([0-9]{2,2})\.([0-9]{4,4}) ([0-9]{2,2}):([0-9]{2,2})#',$value['VALUE'][0],$time))
							$arResult['>'.$key]=mktime(intval($time[4]),intval($time[5]),0,intval($time[2]),intval($time[1]),intval($time[3]));
						if(preg_match('#([0-9]{2,2})\.([0-9]{2,2})\.([0-9]{4,4}) ([0-9]{2,2}):([0-9]{2,2})#',$value['VALUE'][1],$time))
							$arResult['<'.$key]=mktime(intval($time[4]),intval($time[5]),0,intval($time[2]),intval($time[1]),intval($time[3]));
						$KS_URL->Set('ff'.$key.'[0]',$value['VALUE'][0]);
						$KS_URL->Set('ff'.$key.'[1]',$value['VALUE'][1]);
					}
				}
				elseif($value['METHOD']=='->')
				{
					$arResult[$value['METHOD'].$key]="('".join("','",$value['VALUE'])."')";
					foreach($value['VALUE'] as $key=>$val)
						$KS_URL->Set('ff'.$key.'['.$key.']',$val);
				}
				elseif($value['METHOD']=='<>')
				{
					if(isset($value['VALUE'][0]) && strlen($value['VALUE'][0])>0)
					{
						$arResult['>='.$key]=$value['VALUE'][0];
						$KS_URL->Set('ff'.$key.'[0]',$value['VALUE'][0]);
					}
					if(isset($value['VALUE'][1]) && strlen($value['VALUE'][1])>0)
					{
						$arResult['<='.$key]=$value['VALUE'][1];
						$KS_URL->Set('ff'.$key.'[1]',$value['VALUE'][1]);
					}
				}
				elseif($value['METHOD']!='=')
				{
					$arResult[$value['METHOD'].$key]=$value['VALUE'];
					$KS_URL->Set('ff'.$key,$value['VALUE']);
				}
				else
				{
					$arResult[$key]=$value['VALUE'];
					$KS_URL->Set('ff'.$key,$value['VALUE']);
				}
			}
		}
		if(count($arResult)>0)
		{
			$KS_URL->Set('filter',1);
			$KS_URL->Set('fm','get');
		}
		return $arResult;
	}
}
