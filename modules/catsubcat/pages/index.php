<?php
/**
 * @file catsubcat/pages/index.php
 * Файл обработки основных операций модуля текстовые страницы
 * Файл проекта kolos-cms.
 *
 * Создан 13.01.2011
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CModuleAdmin.php';

class CcatsubcatAIindex extends CModuleAdmin
{
	private $obCategory;
	private $obElement;
	private $obEditable;
	private $iCurSection;
	private $sType;
	private $access_level;

	function __construct($module='catsubcat',&$smarty,&$parent)
	{
		global $USER;
		parent::__construct($module,$smarty,$parent);
		$this->obCategory=new CCategory();
		$this->obElement=new CElement();
		$this->obUser=$USER;
		$this->access_level=10;
		$this->iCurSection=0;
	}

	function EditForm($data=false)
	{
		$arDefaults=array();
		if (class_exists('CFields'))
		{
			$obFields=new CFields();
			if($arFields=$obFields->GetList(Array('id'=>'asc'),Array('module'=>$this->module,'type'=>$this->obEditable->sTable)))
			{
				foreach($arFields as $item)
				{
					$arDefaults['ext_'.$item['title']]=$item['default'];
				}
				$this->smarty->assign('addFields',$arFields);
			}
		}
		if(!$data)
		{
			//Создание новой записи
			if($access_level==3) throw new CAccessError("CATSUBCAT_NOT_CREATE");
			$arData=$this->obEditable->GetList(array('orderation'=>'desc'),array('parent_id'=>$ithis->CurSection),array(1),array('orderation'));
			$data=$arDefaults;
			$data['orderation']=intval($arData[0]['orderation'])+10;
	    	$data['type']=$sType;
	    	$data['id']=-1;
	    	$data['active']=1;
	    	$data['parent_id']=$this->iCurSection;

		}
		if($data) $this->smarty->assign('data',$data);
		// Мы что-то редактируем...
		//Получаем данные о текущем разделе
		if($arSectionRes=$this->obCategory->GetRecord(array('id'=>$this->iCurSection)))
		{
			//Получаем список родителей
			$obTree=$this->obCategory->GetParents($arSectionRes['id']);
			//Получаем адрес модуля
			$root_path=$this->obModules->GetSitePath($this->module);
			$this->smarty->assign('navChain',$obTree->GetNavChain($root_path));
			//Если есть модуль навигации, делаем список типов навигации и выводим создание нового пункта в меню
			if($this->obModules->IsModule('navigation'))
			{
				include_once MODULES_DIR.'/navigation/libs/class.CNav.php';
				$oType=new CNavTypes();
				$oElement=new CNavElement();
				if($arMenu['types']=$oType->GetList(array('name'=>'asc'),array('active'=>1)))
				{
					$arMenuType=current($arMenu['types']);
					$arMenu['ITEMS']=$oElement->GetList(array('orderation'=>'asc'),array('type_id'=>$arMenuType['id'],'parent_id'=>'0'));
					$this->smarty->assign('menu',$arMenu);
				}
			}

			/* Получаем дерево для перемещения элемента в другие разделы */
			$removing_leafs = array();
			if ($data['type'] == "cat")
				$removing_leafs = $this->obCategory->GetChildrenIds($data['id'], false);
			$tree_to_move_to = $this->obCategory->GetExpandedTree();
			foreach ($tree_to_move_to as $tree_leaf_key => $tree_leaf)
				if (($data['type'] == "cat" && $data['id'] == $tree_leaf['id']) || in_array($tree_leaf['id'], $removing_leafs))
					unset($tree_to_move_to[$tree_leaf_key]);
				else
				{
					$space = "";
					if ($tree_leaf['level'])
						for($i = 1; $i <= (5 * $tree_leaf['level']); $i++)
							$space .= "&nbsp;";
					$tree_to_move_to[$tree_leaf_key]['list_title'] = $space . $tree_leaf['title'];
				}
			$this->smarty->assign('tree_to_move_to', $tree_to_move_to);
			return '_edit';
		}
		else
		{
			$this->obModules->AddNotify('SYSTEM_SECTION_NOT_FOUND');
			return '';
		}
	}

	function GetDataFromPost($prefix,$data)
	{
		$arResult=array();
		//$arFields=$this->obBanners->GetBannersFields();
		//$arFields=$this->obBanners->GetFields();
		$arFields=$this->obBanners->GetBannerFields();
		foreach($arFields as $sField)
		{
			$arResult[$sField]=$data[$prefix.$sField];
		}
		return $arResult;
	}
	/**
	 * Метод выполняет реализацию операции сохранения данных
	 */
	function Save($id)
	{
		global $KS_URL;
		$this->obEditable->AddCheckField('text_ident');

		/* Добавление проверки на существование данного индентификатора "внутри" определенного раздела */
		$this->obEditable->AddCheckField('parent_id');
		$this->obEditable->AddAutoField('id');
		$this->obEditable->AddFileField('img');

		/* Проверка прав доступа на редактирование */
		if (($this->access_level == 3) && ($_REQUEST['CSC_id'] == -1))
			throw new CAccessError("CATSUBCAT_NOT_CREATE");
		/* Выполним проверку входных данных и попытаемся сохранить запись */
		try
		{
			$iError=0;
			/* Проверка заголовка записи */
			if ($_POST['CSC_title'] == "")
				$iError+=$this->obModules->AddNotify("CATSUBCAT_ENTER_NAME");

			/* Проверка текстового идентификатора */
			if ($_POST['CSC_text_ident'] == "" && $_POST['CSC_id'] != 0)
			{
				$_POST['CSC_text_ident'] = Translit($_POST['CSC_title']);
				if (!IsTextIdent($_POST['CSC_text_ident']))
					$iError+=$this->obModules->AddNotify("SYSTEM_NOT_IDENT");
			}

			if ($_POST['CSC_id'] == 0)
			{
				unset($_POST['CSC_parent_id']);
				unset($_POST['CSC_text_ident']);
			}

			$children_ids = $this->obCategory->GetChildrenIds($_POST['CSC_id'], 0);
			if (in_array($_POST['CSC_parent_id'], $children_ids))
				unset($_POST['CSC_parent_id']);
			//Проверяем дату/время
			if($_POST['CSC_date_add']!='')
				$_POST['CSC_date_add']=strtotime($_POST['CSC_date_add']);
			else
				unset($_POST['CSC_date_add']);
			/* Сохранение/обновление записи */
			if($iError>0) throw new CDataError('CATSUBCAT_FIELDS_ERROR');
			$id=$this->obEditable->Save("CSC_", $_POST);
			if($id===false)
			{
				throw new CError('CATSUBCAT_SAVE_ERROR');
			}
			else
			{
				//Операции выполняемые после сохранения записи в базе данных
				$data = $this->obEditable->GetById($id);

				if ($this->obModules->IsModule("navigation") && $_POST['CM_add'] == 1)
				{
					$_POST['CM_link'] = $data['URL'];
					include_once MODULES_DIR . "/navigation/libs/class.CNav.php";
					$oElement = new CNavElement();
					$oElement->AddAutoField('id');
   					$oElement->AddFileField('img');
					$oElement->Save('CM_', $_POST);
				}

				/* Работа с модулем поиска */
				if ($this->obModules->IsActive("search"))
				{
					$obSearch=new CSearch();
					$obSearch->Index($obEditable,$id,'catsubcat');
				}
				$this->obModules->AddNotify('CATSUBCAT_NOTIFY_SAVE_OK','',NOTIFY_MESSAGE);
				if(!array_key_exists('update',$_REQUEST))
				{
					//if($sType=='cat') $KS_URL->Set('CSC_catid',$data['parent_id']);
					if($this->obEditable instanceof CCategory) $KS_URL->Set('CSC_catid',$data['parent_id']);
					CUrlParser::get_instance()->Redirect("admin.php?".$KS_URL->GetUrl(Array('ACTION','type','CSC_id')));
				}
				else
				{
					if($this->obEditable instanceof CCategory) $sAdd='&CSC_catid='.$id;
					if($this->obEditable instanceof CElement) $sAdd='&CSC_id='.$id.'&CSC_catid='.intval($_POST['CSC_parent_id']);
					CUrlParser::get_instance()->Redirect("admin.php?".$KS_URL->GetUrl('ACTION','CSC_id','CSC_catid').'&ACTION=edit'.$sAdd);
				}
			}
		}
		catch (CError $e)
		{
			if($e->GetCode()==KS_ERROR_MAIN_ALREADY_EXISTS && IsTextIdent($_POST['CSC_text_ident']))
			{
				$arData=$obEditable->GetRecord(
					array(
						'parent_id'=>$_POST['CSC_parent_id'],
						'text_ident'=>$_POST['CSC_text_ident'],
						'>deleted'=>0,
					));
				if(is_array($arData))
				{
					$e=new CError("CATSUBCAT_DUBLICATE_BASKET",KS_ERROR_MAIN_ALREADY_EXISTS);
				}
			}
			$this->smarty->assign('last_error',$e->__toString());

			$showList=false;
			$data=$this->obEditable->GetRecordFromPost('CSC_',$_POST);
			return $this->EditForm($data);
		}
	}

	/**
	 * Метод выводит таблицу записей текстовых страниц
	 */
	function Table()
	{
		//Получаем данные о текущем разделе
		if($arSectionRes=$this->obCategory->GetRecord(array('id'=>$this->iCurSection)))
		{
			//Получаем список родителей
			$obTree[$arSectionRes['id']]=$this->obCategory->GetParents($arSectionRes['id']);
			//Получаем адрес модуля
			$root_path = $this->obModules->GetSitePath('catsubcat');
			// Получаем полный список элементов и разделов
			$arFilter=array();
			/**
			 * @todo Сделать текстовые константы вместо русского текста
			 */
			if (class_exists('CFilterFrame'))
			{
				$obFilter=new CFilterFrame();
				$obFilter->AddField(array('FIELD'=>'title','METHOD'=>'~'));
				$obFilter->AddField(array('FIELD'=>'text_ident','METHOD'=>'~'));
				$obFilter->AddField(array(
					'FIELD'=>'active',
					'TYPE'=>'SELECT',
					'VALUES'=>array(
						''=>'Любой',
						'1'=>'Активен',
						'0'=>'Неактивен'),
				));
				$obFilter->AddField(array('FIELD'=>'date_edit','TYPE'=>'DATE','METHOD'=>'<>'));
				$obFilter->AddField(array('FIELD'=>'date_add','TYPE'=>'DATE','METHOD'=>'<>'));
				$arFilter=$obFilter->GetFilter();
				$obFilter->SetSmartyFilter('filter');
				$arTitles=array(
					'title'=>'Название',
					'text_ident'=>'Текстовый идентификатор',
					'active'=>'Активность',
					'date_edit'=>'Дата редактирования',
					'date_add'=>'Дата добавления');
				$this->smarty->assign('ftitles',$arTitles);
			}
			$arFilter['parent_id']=$arSectionRes['id'];
			//Определяем количество элементов на странице
			$iElCount=10;
			if(array_key_exists('n',$_REQUEST))
			{
				$iElCount=intval($_REQUEST['n']);
				$iElCount=($iElCount<10)?10:$iElCount;
			}
			//Определяем номер страницы
			$iPage=1;
			if(array_key_exists('p1',$_REQUEST))
			{
				$iPage=(intval($_REQUEST['p1'])<1)?1:intval($_REQUEST['p1']);
			}
			$arSortFields=Array('id','title','text_ident','date_add','date_edit','orderation','active','views_count');
			//Определяем порядок сортировки записей
			list($sOrderField,$sOrderDir)=$this->InitSort($arSortFields,$_REQUEST['order'],$_REQUEST['dir']);
			$sNewDir=($sOrderDir=='desc')?'asc':'desc';
			$arSort=array($sOrderField=>$sOrderDir);

			$arTables=array(
				'element'=>$this->obElement,
				'category'=>$this->obCategory
			);
			$arResult=GetAllList($arSort,$arFilter,Array($iElCount*($iPage-1),$iElCount),$arTables);

			$arResult['SECTION'] = $arSectionRes;
			/* Перебрасываем главную страницу к файлам и формируем полную ссылку */
			$mainItem = false;
			$side_length = 20;
			if (count($arResult['ITEMS']))
			{
				foreach ($arResult['ITEMS'] as $arKey => $arValue)
				{
					if(!array_key_exists($arValue['parent_id'],$obTree))
					{
						$obTree[$arValue['parent_id']]=$obCategory->GetParents($arValue['parent_id']);
					}
					$full_path=$obTree[$arValue['parent_id']]->GetFullPath($root_path);
					/* Добавляем в массив полный путь к странице и его сокращённый вид для отображения в подсказке */
					if ($arValue['TYPE'] === 'cat')
					{
						$full_url = $this->obModules->GetConfigVar('main','home_url').$full_path.($arValue['text_ident'] != "" ? $arValue['text_ident'] . "/" : "");
					}
					else
					{
						$full_url = $this->obModules->GetConfigVar('main','home_url').$full_path.$arValue['text_ident'] . ".html";
					}
					$arValue['full_url'] = $full_url;
					$arValue['short_url'] = ShorterUrl($full_url);
					$arResult['ITEMS'][$arKey] = $arValue;

					if ($arValue['id'] == 0 && $arValue['TYPE'] === 'cat')
					{
						/* Запоминаем главную страницу */
						$mainItem = $arValue;
						$mainItem['TYPE'] = 'cat';
						unset($arResult['ITEMS'][$arKey]);
					}
				}
				$is_cat_now = true;
				$newItems = array();
				foreach ($arResult['ITEMS'] as $arKey => $arValue)
				{
					if ($arValue['TYPE'] === 'elm')
						$is_cat_now = false;
					if (!$is_cat_now && $mainItem)
					{
						$newItems[] = $mainItem;
						$mainItem = false;
					}
					$newItems[] = $arValue;
				}
				if ($mainItem)
					$newItems[] = $mainItem;
				$arResult['ITEMS'] = $newItems;
			}

			//Подготавливаем постраничный вывод
			/**
			 * @todo Сделать вызов нормального класса или генератора объекта класса
			 */
			$pages=array();
			$pages['TOTAL']=$arResult['TOTAL'];
			$pages['num']=$arResult['PAGES'];
			$pages['active']=$arResult['CURRENT_PAGE'];
			if ($pages['active']==0)
				$pages['active']=1;
			for($i=1;$i<=$pages['num'];$i++)
			{
				$pages['pages'][$i]=$i;
			}
			$pages['index']=1;
			$pages['visible']=$arResult['IN_PAGE'];

			/* Получаем дерево для перемещения разделов и элементов в другие разделы */
			$tree_to_move_to = $this->obCategory->GetExpandedTree();
			foreach ($tree_to_move_to as $tree_leaf_key => $tree_leaf)
				if ($tree_leaf['id'] == $this->iCurSection)
					unset($tree_to_move_to[$tree_leaf_key]);
				else
				{
					$space = "";
					if ($tree_leaf['level'])
						for($i = 1; $i <= (5 * $tree_leaf['level']); $i++)
							$space .= "&nbsp;";
					$tree_to_move_to[$tree_leaf_key]['list_title'] = $space . $tree_leaf['title'];
				}

			// Передаем данные смарти
			$this->smarty->assign('num_visible',$iElCount);
			$this->smarty->assign('dataList',$arResult);
			$this->smarty->assign('navChain',$obTree[$arSectionRes['id']]->GetNavChain($root_path));
			$this->smarty->assign('pages',$pages);
			$this->smarty->assign('order',Array('newdir'=>$sNewDir,'curdir'=>$sOrderDir,'field'=>$sOrderField));
			$this->smarty->assign('tree_to_move_to', $tree_to_move_to);
			return '';
		}
		else
		{
			$this->obModules->AddNotify('SYSTEM_SECTION_NOT_FOUND');
			return '';
		}
	}

	/**
	 * Метод выполняет групповые действия над записями
	 */
	function CommonActions()
	{
		$arElements=$_POST['sel']['elm'];
		$arSections=$_POST['sel']['cat'];
		$sAction='common';
		if (array_key_exists('comact',$_POST))
		{
			// Установка общей активности для выделенных элементов
			$this->obCategory->Update($arSections,Array('active'=>'1'));
			$this->obElement->Update($arElements,Array('active'=>'1'));
			$this->obModules->AddNotify('CATSUBCAT_NOTIFY_ACTIVATE_DONE','',NOTIFY_MESSAGE);
		}
		elseif (array_key_exists('comdea',$_POST))
		{
			//Снятие общей активности для элементов
			$this->obCategory->Update($arSections,Array('active'=>'0'));
			$this->obElement->Update($arElements,Array('active'=>'0'));
			$this->obModules->AddNotify('CATSUBCAT_NOTIFY_DEACTIVATE_DONE','',NOTIFY_MESSAGE);
		}
		elseif (array_key_exists('comdel',$_POST))
		{
			// Удаление выделенных элементов
			if(is_array($arSections)&&count($arSections)>0)
			{
				foreach($arSections as $i)
				{
					if($i==0)
					{
						$this->obModules->AddNotify('CATSUBCAT_NOT_DELETE_MAIN');
						continue;
					}
					$arData=$this->obCategory->GetRecord(array('id'=>$i));
					if(($i==$arData['parent_id'])&&($i==0)) throw new CError("CATSUBCAT_NOT_DELETE_MAIN");
					if($this->obModules->IsActive('search'))
					{
						$obSearch=new CSearch();
						$obSearch->Delete('catsubcat',$this->obCategory->GenHash($arData));
					}
					if($this->obCategory->Delete($i))
					{
						$this->obModules->AddNotify('CATSUBCAT_NOTIFY_DELETE_OK',$arData['title'],NOTIFY_MESSAGE);
					}
					else
					{
						$this->obModules->AddNotify('CATSUBCAT_NOTIFY_DELETE_BAD',$arData['title'],NOTIFY_WARNING);
					}
				}
			}
			$this->obElement->DeleteByIds($arElements);
		}
		elseif (array_key_exists('commove', $_POST))
		{
			/* Перемещение выделенных элементов */
			if (isset($_POST['move_selected_to']))
			{
				/* Получаем id раздела, куда перемещаем элементы */
				$move_selected_to = intval($_POST['move_selected_to']);

				/* Проверяем существование раздела */
				$destination_item = $this->obCategory->GetRecord(array("id" => $move_selected_to));
				if (is_array($destination_item) && count($destination_item))
				{
					/* Нужно проверить все перемещаемые разделы */
					if (is_array($arSections) && count($arSections))
						foreach ($arSections as $section_key => $section_id)
						{
							/* Смотрим, не является ли раздел главным (его нельзя перемещать), а также, не пытаемся ли мы переместиться в себя */
							if ($section_id == 0 || $section_id == $move_selected_to)
							{
								$this->obModules->AddNotify('CATSUBCAT_NOT_MOVE_MAIN');
								unset($arSections[$section_key]);
							}
							$children_ids = $this->obCategory->GetChildrenIds($section_id, false);
							if (in_array($move_selected_to, $children_ids))
								unset($arSections[$section_key]);
						}
					$this->obCategory->Update($arSections, array('parent_id' => $move_selected_to));
					$this->obElement->Update($arElements, array('parent_id' => $move_selected_to));
				}
			}
		}
	}

	function Run()
	{
		global $KS_URL;
		//Проверка прав доступа
		$this->access_level = $this->obUser->GetLevel($this->module);
		if($this->access_level>3)
			throw new CAccessError("CATSUBCAT_NOT_MANAGE");
		if(array_key_exists('CSC_catid',$_REQUEST))
		{
			$this->iCurSection=intval($_REQUEST['CSC_catid']);
		}
		if(array_key_exists('ACTION',$_REQUEST))
		{
			$action=$_REQUEST['ACTION'];
		}
		else
		{
			$action='';
		}

		/* Получение типа данных (категория или элемент) */
		if(array_key_exists('type',$_REQUEST))
		{
			$sType = $_REQUEST['type'];
		}
		else
		{
			$sType='';
		}

		/* Определение идентификатора записи */
		$iId=0;
		if($sType=='cat')
		{
			if(array_key_exists('CSC_catid',$_REQUEST))
				$iId=intval($_REQUEST['CSC_catid']);
		}
		else
		{
			if(array_key_exists('CSC_id',$_REQUEST))
				$iId=intval($_REQUEST['CSC_id']);
		}

		// Обработка действий множественного выбора
		if(array_key_exists('ACTION',$_POST)&&($_POST['ACTION']=='common'))
		{
			$this->CommonActions();
		}

		if(array_key_exists('id',$_REQUEST))
		{
			$id=intval($_REQUEST['id']);
		}
		else
		{
			$id=0;
		}

		if ($sType == "cat")
			$this->obEditable = $this->obCategory;
		elseif ($sType == "elm")
			$this->obEditable = $this->obElement;
		$this->page='';
		switch($action)
		{
			case "edit":
				$data=$this->obEditable->GetById($iId);
				$data['type']=$sType;
			case "new":
				$page=$this->EditForm($data);
			break;
			case "save":
				$page=$this->Save($id);
			break;
			case "delete":
				if($arData=$this->obEditable->GetById($iId))
				{
					if(($iId==$arData['parent_id'])&&($iId==0)) throw new CError("CATSUBCAT_NOT_DELETE_MAIN");
					if($this->obModules->IsActive('search'))
					{
						$obSearch=new CSearch();
						$obSearch->Delete('catsubcat',$this->obEditable->GenHash($arData));
					}
					if($this->obEditable->Delete($iId))
					{
						$KS_URL->Set('CSC_catid',$arData['parent_id']);
						$this->obModules->AddNotify('CATSUBCAT_NOTIFY_DELETE_OK',$arData['title'],NOTIFY_MESSAGE);
						CUrlParser::get_instance()->Redirect("admin.php?".$KS_URL->GetUrl(Array('ACTION')));
					}
					else
					{
						$this->obModules->AddNotify('CATSUBCAT_NOTIFY_DELETE_BAD',$arData['title'],NOTIFY_WARNING);
					}
				}
				else
				{
					$this->obModules->AddNotify('CATSUBCAT_NOT_FOUND');
				}
			default:
				$page=$this->Table();
		}
		if(array_key_exists('mode',$_GET) && $_GET['mode']=='small')
		{
			echo $smarty->get_template_vars('last_error');
			$smarty->display('admin/catsubcat'.$page.'.tpl');
			die();
		}
		return $page;
	}
}
