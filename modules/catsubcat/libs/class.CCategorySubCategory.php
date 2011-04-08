<?php
/**
 * \file class.CCategorySubCategory.php
 * Контейнер для класса CCategorySubCategory
 * Файл проекта kolos-cms.
 *
 * Создан 25.02.2010
 *
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CRestorable.php';
/**Родительский класс для модуля категорий, подкатегорий. В общем обычно абстрактен и сам по себе нигде не используется.
 * Рекомендуется не применять этот класс а пользоваться его потомками CElement, CCategory. Начиная с версии 2.0
 * присутствует поддержка пользовательских полей.
 * @version 2.1
 * @author blade39 <blade39@kolosstudio.ru>
 */
class CCategorySubCategory extends CRestorable
{
	var $items;
	var $pages;
	var $visible;
	var $sElTable;				/**<Таблица элементов*/
	var $arFields;
	var $check_fields;
	var $auto_fields;
	var $fType;

	/**
	 * Коструктор, устанавливает внутренние переменные, производит вызов родительского конструктора,
	 * проверяет наличие пользовательских полей, и их инициализацию.
	 */

	function __construct($sCategoryTable = '',$sElementsTable='')
	{
		$this->sTable = ($sCategoryTable!='') ? $sCategoryTable : 'catsubcat_catsubcat';
		$this->sElTable = ($sElementsTable!='') ? $sElementsTable : 'catsubcat_element';
		parent::__construct($this->sTable,'/catsubcat','catsubcat');
	}

	/**
	 * Метод генерирует уникальный ключ для записи
	 * @param array $arRecord массив по которому генерируется ключ записи
	 */
	function GenHash($arRecord)
	{
		return 1;
	}

	/**
	 * @copydoc CObject::_GenFileName()
	 * Возвращает md5-хеш полученного имени файла
	 */

	protected function _GenFileName($filename)
	{
		return md5($filename.time()).'.'.substr($filename,strrpos($filename,'.')+1);
	}

	/**
	 * Смотрите @link CObject::_GenWhere(); для подробного описания
	 * Метод проверяет наличие поля deleted в запросе и в случае его отсутствия
	 * добавляет туда значение отображения только существующих элементов
	 */
	protected function _GenWhere($arFilter,$method='AND',$step=0)
	{
		if($step==0)
		{
			$bAddField=true;
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
						$bAddField=false;
						break;
					}
				}
			}
			if($bAddField) $arFilter[$this->sTable.'.deleted']='0';
		}
		return parent::_GenWhere($arFilter,$method,$step);
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
				if($item['deleted']>0)
				{
					if($item['img']!='')
					{
						@unlink(ROOT_DIR.'/uploads/'.$item['img']);
					}
				}
			}
			return parent::DeleteItems($arFilter);
		}
		return false;
	}

	function SetLimits($num)
	{
		global $ks_db;
		$this->visible=$num;
		$this->pages['active']=$_REQUEST['page'];
	}

	/**
	 * Метод выполняет сохранение записи
	 */
	function Save($prefix='',$data=false)
	{
		global $KS_MODULES;
		if(!is_array($data))
		{
			$data=$_POST;
		}
		if(array_key_exists($prefix.'text_ident',$data))
		{
			$data[$prefix.'text_ident']=Translit($data[$prefix.'text_ident']);
			$data[$prefix.'text_ident']=preg_replace('#\s#i','_',$data[$prefix.'text_ident']);
			$data[$prefix.'text_ident']=preg_replace('#[^a-z0-9_\-]#i','',$data[$prefix.'text_ident']);
			$length=$KS_MODULES->GetConfigVar('main','text_ident_length',20);
			$data[$prefix.'text_ident']=substr($data[$prefix.'text_ident'],0,$length);
			$arRow=$this->GetRecord(array('text_ident'=>$data[$prefix.'text_ident'],'!id'=>$data[$prefix.'id'],'parent_id'=>$data[$prefix.'parent_id']));
			if(is_array($arRow)&&($arRow['id']>0))
			{
				//Уже есть запись с таким обрезанным идентификатором
				$code=substr($data[$prefix.'text_ident'],0,$length-MAX_TEXT_IDENT_NUMBERS);
				$arFilter=array(
					'~text_ident'=>$code,
					'!id'=>$arRow['id'],
				);
				$arSort=array(
					'text_ident'=>'desc',
				);
				$iNext=1;
				$arList=$this->GetList($arSort,$arFilter,100);
				if(is_array($arList))
				{
					foreach($arList as $arItem)
					{
						if(preg_match('#^'.$code.'([0-9]{'.MAX_TEXT_IDENT_NUMBERS.','.MAX_TEXT_IDENT_NUMBERS.'})$#i',$arItem['text_ident'],$matches))
						{
							$iNext=intval($matches[1])+1;
							break;
						}
					}
				}
				$data[$prefix.'text_ident']=$code.str_repeat(0,MAX_TEXT_IDENT_NUMBERS-strlen($iNext)).$iNext;
			}
			if(strlen($data[$prefix.'text_ident'])<1) $data[$prefix.'text_ident']=time();
		}
		if($data[$prefix.'id']==0) $data[$prefix.'text_ident']='';
		return parent::Save($prefix,$data);
	}

	/**
	 * Метод выполняет обновление записи. Если при обновлении не была указана дата обновления
	 * она будет определена автоматически.
	 * @param $id integer - номер записи для обновления
	 * @param $arFields array - массив со списком полей и их новых значений
	 */
	function Update($id,$arFields,$bFiltered=false)
	{
		if(in_array('date_edit',$this->arFields) && !array_key_exists('date_edit',$arFields))
			$arFields['date_edit']=time();
		return parent::Update($id,$arFields,$bFiltered);
	}
}

