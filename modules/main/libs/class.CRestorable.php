<?php
/**
 * \file class.CRestorable.php
 * Класс который обладает функционалом работы с восстановлением данных
 * Файл проекта kolos-cms.
 * 
 * Создан 09.06.2009
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 1.0
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

/**
 * Данный класс обладает измененными методами восстановления и удаления данных
 */
require_once MODULES_DIR.'/main/libs/class.CFieldsObject.php';
 
class CRestorable extends CFieldsObject
{
	/**
	 * Метод выполняет обработку фильтра и поиск в нем поля deleted
	 * если поле найдено, возвращает true иначе false
	 * @param $arFilter array - массив фильтрации
	 * @return boolean - результат поиска поля deleted
	 */
	protected function ParseFilter($arFilter)
	{
		$bAddField=false;
		foreach($arFilter as $key=>$item)
		{
			if(preg_match('#^([><!~=]|>=|<=|->|)?([\w_\.\-]+)#i',$key,$matches))
			{
				if(strpos($key,'\.')>0)
				{
					$field=substr($key,strpos($matches[2],'\.'));
				}
				else
				{
					$field=$matches[2];
				}
				if($field=='deleted') 
				{
					$bAddField=true;
					break;
				}
			}
		}
		return $bAddField;
	}
	
	/**
	 * Метод выполняет сохранение записи, используется родительский метод сохранения,
	 * при этом если происходит ошибка существования такой записи, то выполняется 
	 * поиск похожей записи в корзине.
	 * @param $prefix string - префикс полей в массиве данных
	 * @param $data array - данные для сохранения, если пустой использует массив $_POST;
	 * @return integer - номер добавленной записи
	 */
	function Save($prefix='',$data=false)
	{
		if($data===false) $data=$_POST;
		try
		{
			return parent::Save($prefix,$data);
		}	
		catch(CError $e)
		{
			if($e->getCode()==KS_ERROR_MAIN_ALREADY_EXISTS)
			{
				$arFields=$this->GetRecordFromPost($prefix,$data);
				foreach($this->check_fields as $field)
				{
					$arFilter[$field]=$arFields[$field];
				}
				unset($arFilter['id']);
				$arFilter['>deleted']=0;
				$arRecord=$this->GetRecord($arFilter);
				if(is_array($arRecord)&&$arRecord['id']>0)
				{
					throw new CError('CATSUBCAT_COMMON_IN_BASKET',KS_ERROR_MAIN_ALREADY_EXISTS);
				}
			}
			throw $e;
		}
	}
	/**
	 * Метод восстанавливает указанные записи
	 */
	function RestoreItems($arFilter)
	{
		if(!$this->ParseFilter($arFilter))
			$arFilter['>deleted']='0';
		$arItems=$this->GetList(array('id'=>'asc'),$arFilter);
		if(is_array($arItems)&&count($arItems)>0)
		{
			$arIds=array();
			foreach($arItems as $key=>$item)
			{
				$arIds[]=$item['id'];
			}
			if(count($arIds)>0)
				return $this->Update($arIds,array('deleted'=>0));
		}
		return false;
	}
	/**
	 * Метод удаляет элементы, при этом проверяется ключ удаления и если
	 * ключ не был выставлен, элемент удаляется в "корзину". Переопределяет
	 * метод {@link CObject::DeleteItems() CObject::DeleteItems()}.
	 */
	function DeleteItems($arFilter)
	{
		if(!$this->ParseFilter($arFilter))
			$arFilter['>deleted']="-1";
		$arItems=$this->GetList(array('id'=>'asc'),$arFilter);
		if(is_array($arItems)&&count($arItems)>0)
		{
			foreach($arItems as $key=>$item)
			{
				if($item['deleted']==0)
				{
					$this->Update($item['id'],array('deleted'=>time()));
				}
				if($item['deleted']>0)
				{
					parent::DeleteItems(array('id'=>$item['id'],'>deleted'=>'0'));
				}
			}
			return true;
		}
		return false;
	}
	
	/**
	 * Метод производит удаление записей в корзину
	 */
	function DeleteToBasket($arFilter)
	{
		$arItems=$this->GetList(array('id'=>'asc'),$arFilter);
		if(is_array($arItems)&&count($arItems)>0)
		{
			foreach($arItems as $key=>$item)
			{
				$this->Update($item['id'],array('deleted'=>time()));
			}
			return true;
		}
		return false;
	}
}
?>
