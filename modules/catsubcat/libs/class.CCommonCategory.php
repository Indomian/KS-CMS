<?php
/**
 * \file class.CCategory.php
 * контейнер для класса CCategory
 * Файл проекта kolos-cms.
 *
 * Создан 25.02.2010
 *
 * \author blade39
 * \version
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/catsubcat/libs/class.CCategorySubCategory.php';

/**
 * Класс обработки категорий элементов для модуля кэтсубкэт и их потомков
 * @version 2.2
 * Добавлен метод получения списка родителей элемента
 * @todo Устранить зависимости от оригинального класса категорий
 */
class CCommonCategory extends CCategorySubCategory
{
	static $arCategories;
	static $arCatPath;

	function __construct($tables = '',$sElementsTable = '')
	{
		parent::__construct($tables,$sElementsTable);
		$this->AddFileField('img');
	}

	/**
	 * Метод генерирует уникальный ключ для записи
	 * @param $arRecord array - массив описывающий запись
	 */
	function GenHash($arRecord)
	{
		return 'c'.$arRecord['id'];
	}

	/**
	 * Метод строит путь к категории на основании массива родителей
	 * @param $arResult &array - указатель на массив родителей по которому необходимо построить путь.
	 * @return string строка пути
	 * @todo Избавиться от старого кода
	 */
	function BuildPath(&$arResult)
	{
		if(array_key_exists('text_ident',$arResult))
		{
			//Работаем в старом режиме
			$res[]=$arResult['text_ident'];
		    if ($arResult['parents']!=0)
		    {
		    	$subArray=$arResult['parents'][0];
				while($subArray!=0)
				{
					$res[]=$subArray['text_ident'];
					$subArray=$subArray['parents'][0];
				}
			}
			if(count($res)>0) return join('/',array_reverse($res));
		}
		else
		{
			$sUrl='';
			foreach($arResult as $arItem)
			{
				if($arItem['text_ident']!='')
				{
					$sUrl.=$arItem['text_ident'].'/';
				}
			}
			return $sUrl;
		}
		return '';
	}

	/**
	 * Метод выполняет поиск родителей элемента или категории,
	 * возвращает массив где 0 элемент - кореневой раздел, а последний - сам элемент
	 * @param $id integer элемент для которого требуется получить родителей
	 * @return array массив родительских элементов
	 */
	function GetParents($id)
	{
		$arData=$this->GetRecord(array('id'=>$id));
		if($arData)
		{
			$arResult[]=$arData;
			while(is_array($arData)&&($arData['id']>0))
			{
				$arData=$this->GetRecord(array('id'=>$arData['parent_id']));
				if(is_array($arData))
				{
					$arResult[]=$arData;
				}
				else
					break;
			}
			$arResult=array_reverse($arResult);
			$obResult=new CCategoryParentsResult($arResult);
			return $obResult;
		}
		return false;
	}

	/**
	 * Метод возвращает полный путь к элементу с заданным id родителя
	 *
	 * Работает в пользовательской части
	 * Добавлена поддержка работы в административной части
	 * Добавлена защита от возврата дублированных слэшей
	 * @version 1.1
	 * @since 14.05.2009
	 *
	 * @param $parent_id integer Идентификатор родительского раздела
	 * @return string путь к разделу в пользовательской части
	 */
	function GetFullPath($parent_id)
	{
		if(self::$arCatPath[$this->sTable][$parent_id]=='')
		{
			$bParent_id=$parent_id;
			/* Объект для работы с модулями с таким именем должен быть инициализирован в пользовательской части */
			global $KS_MODULES;

			/* Если объект не создан, то значит где-то произошла ошибка */
			if(!is_object($KS_MODULES)) throw new CError('SYSTEM_STRANGE_ERROR');
			/* Устанавливаем максимально допустимое количество уровней дерева
			   (на случай, если структура каталога по каким-то причинам разрушена и невозможно добраться до корневого элемента)*/
			$max_levels_count = 100;
			/* Стартовый номер уровня дерева */
			$current_level = 0;
			/* Массив полного пути */
			$full_path_array = array();
			/* Флаг успешности поиска пути до корня */
			$search_result = $parent_id==0;
			while ($parent_id != 0 && $current_level <= $max_levels_count)
			{
				$arFilter = array("id" => $parent_id, "active" => 1,'>deleted'=>-1);
				$parentItem = $this->GetRecord($arFilter);
				if ($parentItem)
				{
					$full_path_array[] = $parentItem["text_ident"];
					$parent_id = $parentItem["parent_id"];
					$current_level++;
					if ($parent_id == 0)
						$search_result = true;
				}
				else
					break;
			}
			if (!$search_result)
				return false;

			$path=$KS_MODULES->GetSitePath($this->sFieldsModule);
			if($bParent_id>0)
			{
				/*Возвращаем полный url */
				$path.=implode("/", array_reverse($full_path_array)).'/';
			}
			self::$arCatPath[$this->sTable][$bParent_id]=$path;
		}
		return self::$arCatPath[$this->sTable][$bParent_id];
	}

	/**
	 * Метод возвращает в виде массива развёрнутое дерево всех активных разделов,
	 * причём за каждым родительским элементом сразу следуют все дочерние
	 *
	 * @version 1.1
	 * @since 14.05.2009
	 *
	 * @param $root_id integer или array - Идентификатор раздела, относительно которого строится дерево
	 * либо массив фильтра для получения элемента родителя.
	 * @param $tree_level integer Уровень вложенности текущей ветки дерева
	 * @param $only_active boolean Выбирать только активные разделы
	 * @return array
	 */
	function GetExpandedTree($root_id = 0, $tree_level = 0, $only_active = true)
	{
		/* Результирующий массив разделов */
		$tree = array();

		/* Корневой элемент (нужно будет поставить в начало списка) */
		$root_row = false;

		/* Производим выборку всех родителей на данном уровне */
		$arOrder = array("orderation" => "desc");
		if ($only_active)
			$arFilter = array("parent_id" => $root_id, "active" => 1);
		else
			$arFilter = array("parent_id" => $root_id);
		if(is_array($root_id)) $arFilter=$root_id;
		$arSelect = array("id", "text_ident", "title",'access_create','access_edit','access_view');
		$root_items = $this->GetList($arOrder, $arFilter, false, $arSelect);

		/* Теперь погнали перебирать все полученные относительно корня $root_id разделы и добавлять их детей :) */
		if (is_array($root_items))
			if (count($root_items) > 0)
				foreach ($root_items as $root_item)
				{
					/* Устанавливаем уровень ветки */
					$root_item["level"] = $tree_level;

					/* Добавляем родителя */
					if ($root_id == 0 && $root_item["id"] == 0)
						$root_row = $root_item;
					else
						$tree[] = $root_item;

					/* Рекурсивно получаем дерево всех детей */
					$children_tree = array();
					if ($root_item["id"] != 0)
						$children_tree = $this->GetExpandedTree($root_item["id"], $tree_level + 1, $only_active);

					/* Если находим дочерние элементы, добавляем их в дерево */
					if (count($children_tree) > 0)
						foreach ($children_tree as $child)
							$tree[] = $child;
				}

		/* Добавляем корневой элемент в начало */
		if ($root_id == 0 && $root_row)
			$tree = array_merge(array($root_row), $tree);

		/* Возвращаем массив полученного дерева относительно корня $root_id */
		return $tree;
	}

	/**
	 * Метод возвращает массив id вложенных разделов
	 *
	 * @version 1.0
	 * @since 01.06.2009
	 *
	 * @param $root_id integer Идентификатор родительского раздела, для которого ищутся все вложенных активные разделы
	 * @param $only_active boolean Выбирать только активные разделы
	 * @return array Результирующий массив
	 */
	function GetChildrenIds($root_id = 0, $only_active = true)
	{
		if(self::$arCategories[$this->sTable][$root_id]=='')
		{
			$arSelect = array("id", "parent_id");
			if($root_id==0)
			{
				if ($only_active)
					$arFilter = array("active" => 1);
				else
					$arFilter = array();

				$children_items = $this->GetList(false, $arFilter, false, $arSelect);
				if (is_array($children_items))
					if (count($children_items) > 0)
						foreach ($children_items as $child_item)
						{
							/* Добавляем дочерний раздел в массив */
							$children_ids[] = $child_item["id"];
							self::$arCategories[$this->sTable][$root_id]=$children_ids;
						}
			}
			else
			{
				$children_ids = array();
				if ($only_active)
					$arFilter = array("parent_id" => $root_id, "active" => 1);
				else
					$arFilter = array("parent_id" => $root_id);
				$arSelect = array("id", "parent_id");
				$children_items = $this->GetList(false, $arFilter, false, $arSelect);
				if (is_array($children_items))
					if (count($children_items) > 0)
						foreach ($children_items as $child_item)
						{
							/* Добавляем дочерний раздел в массив */
							$children_ids[] = $child_item["id"];

							/* Ищем разделы, вложенные в дочерний раздел */
							if ($child_item["id"] != 0)
								$children_ids = array_merge($children_ids, $this->GetChildrenIds($child_item["id"], $only_active));
							self::$arCategories[$this->sTable][$root_id]=$children_ids;
						}
			}
		}
		else
		{
			$children_ids=self::$arCategories[$this->sTable][$root_id];
		}

		/* Возвращаем результат */
		return $children_ids;
	}

	/**
	 * Метод считает количество вложенных в раздел элементов
	 */
	function GetChildrenCount($id,$arOrder,$onlycount=false)
	{
		global $ks_db;
		$id=intval($id);
		$sOrder=$this->_GenOrder($arOrder);
		$query="SELECT id,parent_id,title,text_ident,description FROM ".PREFIX.$this->sTable." WHERE parent_id='$id' AND parent_id!=id AND active='1'".$sOrder;
		$res=$ks_db->query($query);
		$arResult=array();
		if($ks_db->num_rows($res)>0)
		{
			while($arRow=$ks_db->get_row($res))
			{
				try
				{
					$arRow['count']+=$this->GetChildrenCount($arRow['id'],$arOrder,true);
					$arResult[]=$arRow;
				}
				catch (CError $e)
				{
					continue;
				}
			}
		}
		$query="SELECT COUNT(id) as cnt FROM ".PREFIX.$this->sElTable." WHERE parent_id='$id' AND active='1'";
		$res=$ks_db->query($query);
		if($ks_db->num_rows($res)>0)
		{
			$arRow=$ks_db->get_row($res);
			$count=$arRow['cnt'];
		}
		if($onlycount) return $count;
		return $arResult;
	}

	/**
	 * Метод получает запись о разделе по переданному внутреннему коду.
	 * Также в методе определяется полный путь к указанному разделу.
	 * @param $id - внутренний идентификатор раздела
	 * @return array - массив полей
	 */
	function GetById($id)
	{
		$arResult=$this->GetRecord(array('id'=>$id));
		if(is_array($arResult))
		{
			if ($id!=0)
			{
				$arResult['URL']=$this->GetFullPath($arResult['parent_id']).$arResult['text_ident'].'/';
			}
			else
			{
				$arResult['URL']=$this->GetFullPath($arResult['parent_id']);
			}
		}
		return $arResult;
	}

	/**
	 * @todo Добавить документацию
	 */
	function FilterBeforeDelete($item)
	{
		if($item==0) return 0;
		return 1;
	}

	/**
	 * Метод выполняет удаление записи о разделе, также удаляются все записи
	 * о вложенных разделах и элементах.
	 * Метод перекрывает родительский DeleteItems
	 * @param $arFilter array - массив фильтрующий элементы подлежащие удалению
	 * @return boolean - в зависимости от результата выполнения операции
	 */
	function DeleteItems($arFilter)
	{
		if(!$this->ParseFilter($arFilter))
			$arFilter['>deleted']='-1';
		$obElement=new CCommonElement($this->sTable,$this->sElTable);
		$arItems=$this->GetList(array('id'=>'asc'),$arFilter);
		if(is_array($arItems)&&count($arItems)>0)
		{
			foreach($arItems as $key=>$item)
			{
				if($item['deleted']>0) $arMyFilter=array('parent_id'=>$item['id'],'>deleted'=>-1);
				else $arMyFilter=array('parent_id'=>$item['id'],'deleted'=>'0');
				$arList=$this->GetExpandedTree($arMyFilter);
				if (is_array($arList)&&($arList!=0)&&(count($arList)>0))
				{
					foreach ($arList as $id)
					{
						if($item['deleted']>0)
						{
							$arSubFilter=array('parent_id'=>$id['id'],'>deleted'=>-1);
							$arDelFilter=array('id'=>$id['id'],'>deleted'=>-1);
						}
						else
						{
							$arSubFilter=array('parent_id'=>$id['id'],'deleted'=>'0');
							$arDelFilter=array('id'=>$id['id'],'deleted'=>'0');
						}
						parent::DeleteItems($arDelFilter);
						$obElement->DeleteItems($arSubFilter);
					}
				}
				$obElement->DeleteItems($arMyFilter);
			}
			return parent::DeleteItems($arFilter);
		}
	}

	/**
	 * Метод производит восстановление раздела и всех вложенных в него записей
	 */
	function RestoreItems($arFilter)
	{
		if(!$this->ParseFilter($arFilter))
			$arFilter['>deleted']='0';
		$obElement=new CElement($this->sTable,$this->sElTable);
		$bChilds=$arFilter['childs']=='N';
		unset($arFilter['childs']);
		$arItems=$this->GetList(array('id'=>'asc'),$arFilter);
		if(is_array($arItems)&&count($arItems)>0)
		{
			parent::RestoreItems($arFilter);
			foreach($arItems as $key=>$item)
			{
				$arParent=$this->GetRecord(array('id'=>$item['parent_id'],'>deleted'=>'-1'));
				if(is_array($arParent))
				{
					if($arParent['deleted']>0)
					{
						$this->RestoreItems(array('id'=>$item['parent_id'],'childs'=>'N'));
					}
				}
				if(!$bChilds)
				{
					$arList=$this->GetExpandedTree(array('parent_id'=>$item['id'],'>deleted'=>'0'));
					if (is_array($arList)&&($arList!=0)&&(count($arList)>0))
					{
						foreach ($arList as $id)
						{
							$this->RestoreItems(array('id'=>$id['id']));
							$obElement->RestoreItems(array('parent_id'=>$id['id'],'>deleted'=>'0'));
						}
					}
					$obElement->RestoreItems(array('parent_id'=>$item['id'],'>deleted'=>'0'));
				}
			}
		}
	}
}
?>
