<?php

/**
 * Виджет отображения анонсов текстовых страниц
 * 
 * Выводит анонсы нескольких страниц в зависимости от выбранных страниц
 * 
 * @filesource function.CatAnnounce.php
 * @author North-E <pushkov@kolosstudio.ru>, BlaDe39 <blade39@kolosstudio.ru>
 * @version 1.1
 * @since 01.06.2009
 * 
 * 1. Добавлена выборка из вложенных разделов.
 * 2. Добавлена постраничная навигация
 * 3. BlaDe39: добавлена поддержка работы в режиме Аякс
 */

require_once MODULES_DIR.'/interfaces/libs/class.CAjax.php';

/**
 * Функция, возвращающая шаблон анонсов
 * 
 * @param array $params Массив входных параметров для виджета
 * @param object $subsmarty Ссылка на объект Смарти
 * @return string
 */
function smarty_function_CatAnnounce($params, &$subsmarty)
{
	/* Необходимые глобальные объекты и переменные */
	global $KS_IND_matches, $KS_URL, $USER, $KS_MODULES, $global_template,$ks_db;
	try
	{
		//Проверка и инициализация аякса
		if($params['isAjax']=='Y') 
		{
			/*Ключ о том это аякс запрос или нет*/
			$oldAjax=false;
			$obAjax=new CAjax('CatAnnounce',$params);
			if(array_key_exists('ajaxMode',$_GET))
			{
				if($obAjax->CheckHash($_GET['ajaxMode']))
				{
					$oldAjax=true;
					ob_clean();
				}
				else
					return '';
			}
		}
		/* Проверка прав доступа пользователя к анонсам */
		$access_level=$USER->GetLevel('catsubcat');
		$arUserGroups=$USER->GetGroups();
		//Проверяем права на доступ к самому модулю
		if($access_level>8) throw new CAccessError("CATSUBCAT_NOT_VIEW_ANNOUNCE");
		/* Подключаем необходимые библиотеки */	
		$module_directory = MODULES_DIR . "/catsubcat/";
		include_once($module_directory . "libs/class.CCategoryEdit.php");
		/* Подключаем необходимые библиотеки */	
		$arFilterOperations=array(
			'eq'=>'=',
			'ne'=>'!=',
			'gt'=>'>',
			'lt'=>'<',
			'ge'=>'>=',
			'le'=>'<=',
			//'in'=>'->',
		);
		/* Создаём объект для работы с разделами */
		$obCategory = new CCategory();
				
		/* Создаём объект для работы с элементами*/
		$obElement = new CElement();
		foreach($obElement->arFields as $item)
			if($item!='id')	$arFilterFields[$item]='[a-z0-9_\-\.]+';
			
		/* Берём параметры, которые нам необходимы для работы виджета */
		$select_all = false;
		if ($params['parent_id'] == "all")
		{
			$select_all = true;
			$parent_id = 0;
		}
		else
			$parent_id = intval($params['parent_id']);
		$params['shift']=intval($params['shift']);
			
		/* Количество анонсируемых страниц или количество анонсируемых текстовых страниц на страницу (для постраничной навигации)*/
		$announces_count = $params['announces_count'];
		
		/* Принцип сортировки */
		$orderby = $params['sort_by'];
		$orderdir= $params['sort_order']=='asc'?'asc':'desc';
	
		/* Смотрим, найден ли активный родительский элемент - если нет, то нужно отдать в Смарти ошибку */
		if ($parent_id >= 0)
		{		
			/* Определяем принцип сортировки элементов */
			if ($orderby != "random")
				$arElOrder = array($orderby => $orderdir);
			else
				$arElOrder = array("RAND()" => $orderdir);
			
			/* Определяем принцип выборки */
			if ($select_all)
				$arElFilter = array("active" => 1);
			else
			{
				if ($params["select_from_children"] == "Y")
				{
					if($parent_id!=0)
					{
						/* Анонсируемые страницы выбираются не только из указанного раздела, но и из всех вложенных */
						$children_ids = array_merge(array($parent_id), $obCategory->GetChildrenIds($parent_id));
						$arElFilter = array("->parent_id" => "(" . implode(", ", $children_ids) . ")", "active" => 1);
					}
					else
					{
						$arElFilter = array("active" => 1);
					}
				} 
				else
				{
					/* Анонсируемые страницы выбираются только из указанного раздела */
					$arElFilter = array("parent_id" => $parent_id, "active" => 1);
				}
			}
			
		//Если надо фильтруем записи по времени
		if($_GET['year']!=0)
		{
			$arMonthesLength=array(
				31,28,31,30,31,30,31,31,30,31,30,31
			);
			$iDate=mktime(0,0,0,$_GET['month'],1,$_GET['year']);
			$isLeap=idate('I',$iDate);
			if($isLeap==1) $arMonthesLength[1]++;
			if($_GET['month']!=0)
			{
				if($_GET['day']!=0)
				{
					$arElFilter['>date_add']=mktime(0,0,0,$_GET['month'],$_GET['day'],$_GET['year']);
					$arElFilter['<date_add']=mktime(23,59,59,$_GET['month'],$_GET['day'],$_GET['year']);
				}
				else
				{
					$arElFilter['>date_add']=mktime(0,0,0,$_GET['month'],1,$_GET['year']);
					$arElFilter['<date_add']=mktime(23,59,59,$_GET['month'],$arMonthesLength[$_GET['month']-1],$_GET['year']);
				}
			}
			else
			{
				$arElFilter['>date_add']=mktime(0,0,0,1,1,$_GET['year']);
				$arElFilter['<date_add']=mktime(23,59,59,12,31,$_GET['year']);
			}
		}
			
			/**
			 * Ищем пользовательские требования к фильтру и используем их если надо
			 */
			//Делаем проверку на наличие фильтра в параметрах
			$fParams=array();
			foreach($params as $key=>$value)
			{
				if(preg_match('#^(fo|ff)_([a-z0-9_]+)$#i',$key,$matches))
				{
					$fParams[$key]=$value;
				}
			}
			if((count($fParams)<1)&&($_GET['ff']=='Y')) $fParams=$_GET;
			foreach($fParams as $key=>$value)
			{
				if(preg_match('#^fo_([a-z0-9_]+)$#i',$key,$matches))
				{
					$field=$matches[1];
					if(array_key_exists($field,$arFilterFields)&&array_key_exists($value,$arFilterOperations)&&array_key_exists('ff_'.$field,$fParams))
					{
						if(preg_match('#'.$arFilterFields[$field].'#i',$fParams['ff_'.$field]))
						{
							if($value=='eq') $arElFilter[$field]=$fParams['ff_'.$field];
							else
							$arElFilter[$arFilterOperations[$value].$field]=$fParams['ff_'.$field];
						}
					}
				}
			}
			/* Устанавливаем количество выбираемых элементов */
			$arLimit = intval($announces_count);
			
			/* Массив выбираемых полей */
			//$arSelect = array("id", "parent_id", "text_ident", "title", "description", "img", "date_add", "views_count");
			$arSelect=$obElement->arFields;
			
			/* Использование постраничной навигации */
			if ($params["use_page_navigation"] == "Y")
			{
				/* Подключаем библиотеку для работы с постраничной навигацией */
				include_once MODULES_DIR . '/interfaces/libs/CInterface.php';
				
				/* Определяем общее количество выбираемых элементов */
				$elements_count = $obElement->Count($arElFilter);
				
				/* Создаём объект для работы с постраничной навигацией */
				$obPages = new CPageNavigation($obElement, $elements_count, $announces_count);
				
				/* Получаем массив для постраничной навигации (будем использовать в Смарти) */
				$pages = $obPages->GetPages();
				
				/* Устанавливаем новые переделы для выборки элементов */
				$arLimit = $obPages->GetLimits();
			}
		
			//Проверяем надо ли сдвигать результат или нет
			if($params['shift']>0)
			{
				if(is_array($arLimit))
				{
					$arLimit[0]+=$params['shift'];
//					$arLimit[1]+=$params['shift'];
				}
				else
				{
					$arLimit=array($params['shift'],$arLimit);
				}
			}
		
			
			/* Получаем массив анонсируемых страниц */
			$announces = $obElement->GetList($arElOrder, $arElFilter, $arLimit,$arSelect);
			$data=array();
			$arFullPath=array();
			if (is_array($announces))
				if (count($announces))
					foreach ($announces as $announce_key => $announce)
					{
						/* Кидаем в массив дату добавления в отформатированном виде */
						$announces[$announce_key]['date'] = date("d.m.Y", $announce['date_add']);
						
						/* Определяем полный путь к разделу, содержащему элемент */
						/* Определяем полный путь к разделу, содержащему элемент */
						if(!array_key_exists($announce['parent_id'],$arFullPath))
						{
							$arFullPath[$announce['parent_id']]=$obCategory->GetFullPath($announce['parent_id']);
						}
						$announces[$announce_key]['full_path'] = $arFullPath[$announce['parent_id']];
						$announces[$announce_key]['date_rfc2822']=date('r',$announces[$announce_key]['date_add']);
						if($data['pubDate']<$announce['date_add'])
							$data['pubDate'] = $announce['date_add'];
						if($data['lastBuildDate']<$announce['date_edit'])
							$data['lastBuildDate'] = $announce['date_edit'];
					}
			
			/* Отправляем данные для Смарти */
			$subsmarty->assign("announces", $announces);
			$subsmarty->assign("announces_count", count($announces));
			$subsmarty->assign('module_title',$KS_MODULES->GetConfigVar('catsubcat','title_default','Текстовые страницы'));
			$subsmarty->assign("orderby", $orderby);
			if (isset($pages))
			{
				$data = array();
				$data["pages"] = $pages;
				$subsmarty->assign("data", $data);
			}
		}
		else
			$subsmarty->assign("announce_error", "CATSUBCAT_SECTION_NOT_ACTIVE");
			
		$sResult=$KS_MODULES->RenderTemplate($subsmarty,'/catsubcat/CatAnnounce',$params['global_template'],$params['tpl']);
		//Заканчиваем работу в режиме аякса	
		if($params['isAjax']=='Y') $sResult=$obAjax->GetCode($sResult,$oldAjax);
		if($oldAjax)
		{
			echo $sResult;
			die();
		}			
		return $sResult;
	}
	catch (CAccessError $e)
	{
		return $e;
	}
	catch (CError $e)
	{
		return $e;
	}
}

/**
 * Функция, определяющая возможные настраиваемые параметры виджета анонсов
 */
function widget_params_CatAnnounce($params)
{
	/* Подключение необходимой библиотеки */
	include_once(MODULES_DIR . "/catsubcat/libs/class.CCategoryEdit.php");
	
	/* Создание объекта для работы с разделами текстовых страниц */
	$obCategory = new CCategory();
	$obElement=new CElement();
	
	/* Строим дерево всех разделов текстовых страниц для вывода их в качестве списка */
	$expanded_tree = $obCategory->GetExpandedTree();
	
	/* Формируем данные для списка дерева разделов */
	$parent_select = array("all" => "Из всех разделов");
	if (is_array($expanded_tree) && count($expanded_tree))
		foreach ($expanded_tree as $leaf)
		{
			$spaces = "";
			for ($q = 0; $q < $leaf["level"] * 4; $q++)
				$spaces .= "&nbsp;";
			$parent_select[$leaf["id"]] = $spaces . $leaf["title"];
		}
	
	/* Массив с возможными значениями количества анонсов в блоке */
	$announces_values = array();
	for ($i = 1; $i <= 10; $i++)
		$announces_values[$i] = $i;
	
	/**
	 * Определяем возможные поля для сортировки и фильтрации записей
	 */
	$arFilterFields=array(
		'title'=>'Название',
		'text_ident'=>'Текстовый идентификатор',
		'date_add'=>'Дата добавления',
		'views_count'=>'Количество просмотров',
		'date_edit'=>'Дата редактирования',
		'orderation'=>'По номеру сортировки',
	);
	
	if(class_exists('CFields'))
	{
		$obFields=new CFields();
		$arFields=$obFields->GetList(Array('id'=>'asc'),Array('module'=>"catsubcat",'type'=>$obElement->sTable));
		if(is_array($arFields))
		{
			foreach($arFields as $arItem)
			{
				$arFilterFields[$arItem['title']]=$arItem['description'];		
			}
		}
	}
	$arSortFields=$arFilterFields;
	$arSortFields['random']='Случайно';
	$filterFields = array
  	(
  		"title" => "Поля для фильтрации",
  		"type" => "checklist",
  		"value" => $arFilterFields,
  		"default_value"=>$params['filter'],
		"onchange" => "" .
				"var list=$('input:checked');" .
				"var res='';".
				"for(var i=0;i<list.length;i++)" .
				"res+='filter[]='+list.eq(i).val()+'&';" .
				"nextStep('widget', 'wmod=catsubcat&w=CatAnnounce', res);"
	);
	$arFields = array
	(
		"filter"=>$filterFields,
	);
	$arFilterOperations=array(
		'eq'=>'равно',
		'ne'=>'не равно',
		'gt'=>'больше',
		'lt'=>'меньше',
		'ge'=>'больше либо равно',
		'le'=>'меньше либо равно',
		//'in'=>'содержится в',
	);
	if(is_array($params['filter']))
	{
		foreach($params['filter'] as $field)
		{
			$arFields['fo_'.$field]=array(
				'title'=>'Принцип сравнения поля <i>'.$arFilterFields[$field].'</i>',
				'type'=>'select',
				'default_value'=>'eq',
				'value'=>$arFilterOperations
			);
			$arFields['ff_'.$field]=array(
				'title'=>'Значение для фильтрации '.$arFilterFields[$field],
				'type'=>'text',
			);
		}
	}
	$arFields=array_merge($arFields,array
	(
		"parent_id" => array
		(
			"title" => "Идентификатор раздела, из которого выбирать анонсируемые страницы",
			"type" => "select",
			"value" => $parent_select
		),
		"select_from_children" => array
		(
			"title" => "Выбирать анонсы из родительских разделов",
			"type" => "select",
			"value" => array("Y" => "Да", "N" => "Нет"),
			"default_value" => "N"
		),
		"announces_count" => array
		(
			"title" => "Количество анонсов на страницу",
			"type" => "select",
			"value" => $announces_values,
			"default_value" => 3
		),
		"use_page_navigation" => array
		(
			"title" => "Использовать постраничную навигацию",
			"type" => "select",
			"value" => array("Y" => "Да", "N" => "Нет"),
			"default_value" => "N"
		),
		"shift"=>array(
			'title'=>'Начинать с записи №',
			'type'=>'text',
			'value'=>'0',
			'default_value'=>'0',
		),
		"sort_by" => array
		(
			"title" => "Сортировать по",
			"type" => "select",
			"value" => $arSortFields		
		),
		'sort_order'=>array(
			'title'=>'Направление сортировки',
			'type'=>'select',
			'value'=>array('asc'=>'По возрастанию (от меньшего к большему)','desc'=>'По убыванию (от большего к меньшему)'),
		),
		'isAjax'=>array(
			'title'=>'Режим AJAX',
			'type'=>'select',
			'value'=>array('Y'=>'да','N'=>'нет'),
		)
	));
	
	return array
	(
		"fields" => $arFields
	);
}

?>