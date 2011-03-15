<?php

if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/interfaces/libs/class.CFrame.php';

/**
 * Класс выводит рамку для фильтрации списков. Автоматически определяет поля фильтрации по
 * переданным в запросе данным, может возвращать значения массива для фильтрации при использовании
 * функций потомков CObject::GetList().
 */

class CFilterFrame extends CFrame
{
	var $arFields; /*!<Список полей по которым осуществляется фильтрация. Содержит массив записей,
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
		$method=$_REQUEST['fm'];
		if(strtolower($method)=='post')
		{
			//Ацкий чит из-за точки в пхп
			foreach($_POST as $key=>$value)
			{
				$arFrom[str_replace('^','.',$key)]=$value;
			}
		}
		else
		{
			//pre_print($_SERVER);
			//Чит чтобы точки тоже бегали
			//Исправление ошибки с сортировками дат
			/** @todo: Переместить потом в CUrlParser */
			$arGet=explode('&',$_SERVER['QUERY_STRING']);
			foreach($arGet as $arOneGet)
			{
				$arItem=explode('=',$arOneGet);
				$arItem[0]=urldecode($arItem[0]);
				if(preg_match('#^(.*)\[(.*)\]$#i',$arItem[0],$matches))
				{
					$arFrom[$matches[1]][$matches[2]]=urldecode($arItem[1]);
				}
				else
				{
					$arFrom[urldecode($arItem[0])]=urldecode($arItem[1]);
				}
			}
		}
		$arMatches=array();
		//pre_print($arFrom);
		if(($arFrom['filter']==1)&&(!array_key_exists('fundo',$arFrom)))
		{
			foreach($arFrom as $key=>$value)
			{
				if(preg_match('/^ff([\w\._]+)$/',$key,$arMatches))
				{
					$arField=array('FIELD'=>$arMatches[1],'VALUE'=>$value,'TYPE'=>'STRING');
					$this->AddField($arField);
				}
			}
		}
	}

	/**
	 * Выполняет добавление одного поля для фильтрации.
	 * Добавлена поддержка значения по умолчанию, используется если не задано значение пользователем.
	 * */
	function AddField($arField)
	{
		$arNewField=array(
			'FIELD'=>$arField['FIELD'],
			'VALUE'=>$arField['VALUE'],
			'VALUES'=>$arField['VALUES'],
			'METHOD'=>$arField['METHOD'],
			'TYPE'=>$arField['TYPE'],
			'DEFAULT'=>$arField['DEFAULT'],
		);
		if($arNewField['METHOD']=='') $arNewField['METHOD']='='; else $arNewField['METHOD']=$arField['METHOD'];
		if($arNewField['TYPE']=='') $arNewField['TYPE']='STRING'; else $arNewField['TYPE']=$arField['TYPE'];
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
		$smarty->assign('hideFilter',intval($_COOKIE['showFilter']));
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
	 * Возращает массив для фильтрации списка.
	 * @return array массив где ключи - ключи фильтра, а значения - значения фильтрации
	 */
	function GetFilter()
	{
		global $KS_URL;
		$arResult=array();
		foreach($this->arFields as $key=>$value)
		{
			if(strlen($value['VALUE'])>0)
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
					{
						$KS_URL->Set('ff'.$key.'['.$key.']',$val);
					}
				}
				elseif($value['METHOD']=='<>')
				{
					if(strlen($value['VALUE'][0])>0)
					{
						$arResult['>'.$key]=$value['VALUE'][0];
						$KS_URL->Set('ff'.$key.'[0]',$value['VALUE'][0]);
					}
					if(strlen($value['VALUE'][1])>0)
					{
						$arResult['<'.$key]=$value['VALUE'][1];
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
