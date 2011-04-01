<?php

if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

class CNavTypes extends CObject
{
	protected $arMenuType;
	/**
	 * Конструктор класса типов навигации
	 */
	function __construct($sTable="navigation_menu_types")
	{
		parent::__construct($sTable);
		$this->arFields=Array('id','text_ident','name','description','script_name','active');
		//$this->sTable="navigation_menu_types";
		$this->items=0;
		$this->visible=-1;
		$this->pages=Array();
	}

	function GetById($id)
	{
		$id=intval($id);
		return $this->GetRecord(array('id'=>$id));
	}

	function GetByTextIdent($text_ident)
	{
		return $this->GetRecord(array('text_ident' => $text_ident));
	}

	function GetScriptList($arOrder=false,$arFilter=false)
	{
		if(file_exists(MODULES_DIR.'/navigation/menu_scripts/.description.php'))
		{
			include MODULES_DIR.'/navigation/menu_scripts/.description.php';
		}
		$sScriptsPath=MODULES_DIR.'/navigation/menu_scripts/';
		$arNotTemplates=Array('.','..','admin','cache','configs','templates_c','.description.php');
		$arResult=array();
		if (is_dir($sScriptsPath))
		{
			if ($hDir = @opendir($sScriptsPath))
	   		{
	       		while (($file = readdir($hDir)) !== false)
	        	{
	        		if (!in_array($file,$arNotTemplates))
	        		{
	        			$arResult[]=Array('title'=>$arDescription[$file],'value'=>substr($file,0,strlen($file)-4));
	        		}
	        	}
	      	}
		}
		return $arResult;
	}

	/**
	 * Возвращает список элементов меню, заданного текстовым идентификатором $text_ident, который составляется
	 * выбранным для этого типа меню скриптом
	 * @param $text_ident - текстовый идентификатор меню
	 */
	function GetMenu($text_ident)
	{
		global $ks_db;
		$output = '';
		$menu_params = $this->GetRecord(array('text_ident' => $text_ident,'active'=>'1'));
		$this->arMenuType=$menu_params;
		if (!count($menu_params))
			throw new CError('NAVIGATION_MENU_TYPE_NOT_FOUND');
		$script_fullname = MODULES_DIR . '/navigation/menu_scripts/' . $menu_params['script_name'] . '.php';
		if (file_exists($script_fullname))
	  		include($script_fullname);
	 	else
	 		throw new CError('NAVIGATION_MENU_SCRIPT_REQUIRED');
	 	return $output;
	}

	/**
	 * метод возвращает описание последнего полученного типа меню
	 */
	public function GetLastMenuType()
	{
		return $this->arMenuType;
	}

	function Delete($id)
	{
		$this->DeleteByIds(Array($id));
	}

	function DeleteByIds($ids)
	{
		global $ks_db;
		if (is_array($ids))
		{
			foreach($ids as $item)
			{
				$query="DELETE FROM ".PREFIX."navigation_menu_elements WHERE type_id='".intval($item)."'";
				$ks_db->query($query);
			}
			$where=join('\', \'',$ids);
			$query="DELETE FROM ".PREFIX.$this->sTable." WHERE id IN ('".$where."')";
			$ks_db->query($query);
		}
	}
}

class CNavElement extends CFieldsObject
{
	/**
	 * Конструктор элемента навигации
	 */
	function __construct($sTable="navigation_menu_elements",$sUploadPath='/navigation',$sModule='navigation')
	{
		parent::__construct($sTable,$sUploadPath,$sModule);
	}

	function GetById($id)
	{
		$id=intval($id);
		return $this->GetRecord(Array('id'=>$id));
	}

	function Delete($id)
	{
		$this->DeleteByIds(Array($id));
	}

	function DeleteByIds($ids)
	{
		global $ks_db;
		if (is_array($ids))
		{
			$where=join('\', \'',$ids);
			if(count($ids)>0)
			{
				$this->DeleteItems(array('->id'=>'('.$where.')'));
			}
		}
	}

	/**
	 * @copydoc CObject::DeleteItems
	 * Также удаляет все подчиненные элементы
	 */

	function DeleteItems($arFilter)
	{
		global $ks_db;
		$sWhere=$this->_GenWhere($arFilter);
		$sWhere=preg_replace('# [a-z_\-]+\.#i',' ',$sWhere);
		if (strlen($sWhere)>0)
		{
			if($arList=$this->GetList(array('id'=>'asc'),$arFilter))
			{
				foreach($arList as $item)
				{
					$this->DeleteItems(array('parent_id'=>$item['id']));
				}
			}
			$query="DELETE FROM ".PREFIX.$this->sTable.$sWhere;
			$ks_db->query($query);
			return true;
		}
		return false;
	}

	/**
	 * Возвращает в виде массива все дочерние элементы меню из имеющегося списка элементов $search_items
	 * для родительского элемента $parent_id, причём за каждым дочерним элементом массива сразу же следуют
	 * его дочерние элементы
	 * @param $type_id - id меню
	 * @param $parent_id - id родительского элемента меню
	 * @param &$search_items - ссылка на массив элементов меню, в котором ищутся дочерние
	 */
	function GetDaughterMenuItems($type_id, $parent_id, &$search_items)
	{
		$daughter_items = array();
		if (count($search_items))
		{
			// выбор всех дочерних элементов для родительского $parent_id
			foreach ($search_items as $key => $search_item)
			{
				if ($search_item['parent_id'] == $parent_id)
				{
					$daughter_items[] = $search_item;
					unset($search_items[$key]);
				}
			}
			// выбор дочерних элементов следующего уровня
			if (count($daughter_items) && count($search_items))
			{
				$tmp_items = $daughter_items;
				$daughter_items = array();
				foreach ($tmp_items as $key => $tmp_item)
				{
					$daughter_items[] = $tmp_item;
					$daughter_items = array_merge($daughter_items, $this->GetDaughterMenuItems($type_id, $tmp_item['id'], $search_items));
				}
			}
		}
		return $daughter_items;
	}

	/**
	 * Возвращает элементы меню в виде массива
	 * @param $type_id - id меню
	 * @param $expand - возвращать меню с вложениями, причём элементы сортируются таким образом,
	 * что за каждым родительским элементом следуют все его дочерние
	 */
	function GetMenuList($type_id, $expand = false)
	{
		$menu_items_all = array();
		$arOrder = array('parent_id' => 'asc', 'orderation' => 'asc');
		$arFilter = array('type_id' => $type_id, 'active'=>1);
		if (!$expand)
			$arFilter['parent_id'] = 0;
		$menu_items_all = $this->GetList($arOrder, $arFilter);
		if (!$expand)
			return $menu_items_all;

		// сортируем пункты меню с вложениями
		$start_parent_id = 0;
		return $this->GetDaughterMenuItems($type_id, $start_parent_id, $menu_items_all);
	}

	/**
	 * Функция возвращает ассоциативный массив, содержащий id элемента меню и id его родительского элемента меню
	 * для страницы с URL $link
	 * @param $link - URL, для которого находим id соответствующего меню
	 * @param $type_id - ID меню
	 */
	function GetCurrentPageMenuIds($link,$type_id='')
	{
		$arFilter = array('link' => $link);
		if($type_id)
			$arFilter['type_id']=$type_id;
		$page_menu=array();
		if($res_array = $this->GetRecord($arFilter))
		{
			$page_menu['id'] = $res_array['id'];
			$page_menu['parent_id'] = $res_array['parent_id'];
		}
		else
		{
			//Если не нашли меню сразу, пробуем усекать линк пока не найдём меню
			$arSourceLink=explode('/',$link);
			while(count($arSourceLink)>0)
			{
				array_pop($arSourceLink);
				$sLink=join('/',$arSourceLink);
				$arFilter = array('link' => $sLink.'/');
				if($type_id)
					$arFilter['type_id']=$type_id;
				if($res_array = $this->GetRecord($arFilter))
				{
					$page_menu['id'] = $res_array['id'];
					$page_menu['parent_id'] = $res_array['parent_id'];
					break;
				}
			}
		}
		if (!is_array($page_menu))
			return false;
		return $page_menu;
	}

	function _sort($a,$b)
	{
		if($a['orderation']>$b['orderation']) return 1;
		elseif($a['orderation']<$b['orderation']) return -1;
		return 0;
	}
}

?>