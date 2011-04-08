<?php
/**
 * @file catsubcat/pages/basket.php
 * Файл обеспечивающий функционирование корзины модуля catsubcat
 * Файл проекта kolos-cms.
 *
 * Создан 14.01.2011
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CModuleAdmin.php';

class CcatsubcatAIbasket extends CModuleAdmin
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
	}

	/**
	 * Метод выводит таблицу записей текстовых страниц
	 */
	function Table()
	{
		// Получаем полный список элементов и разделов
		if(class_exists('CFilterFrame'))
		{
			$obFilter=new CFilterFrame();
			$obFilter->AddField(array('FIELD'=>'title','METHOD'=>'~'));
			$obFilter->AddField(array('FIELD'=>'text_ident','METHOD'=>'~'));
			$obFilter->AddField(array('FIELD'=>'deleted','TYPE'=>'DATE','METHOD'=>'<>'));
			$obFilter->AddField(array('FIELD'=>'date_add','TYPE'=>'DATE','METHOD'=>'<>'));
			/**
			 * @todo Сделать текстовые константы
			 */
			$obFilter->AddField(array(
				'FIELD'=>'TYPE',
				'TYPE'=>'SELECT',
				'VALUES'=>array(
					''=>'Любой',
					'cat'=>'Раздел',
					'elm'=>'Страница'),
			));
			$arFilter=$obFilter->GetFilter();
			$obFilter->SetSmartyFilter('filter');
			$arTitles=array(
				'title'=>'Название',
				'text_ident'=>'Текстовый идентификатор',
				'TYPE'=>'Тип',
				'deleted'=>'Дата удаления',
				'date_add'=>'Дата добавления'
			);
			$this->smarty->assign('ftitles',$arTitles);
		}
		$arFilter=array_merge(array('>deleted'=>'0'),$arFilter);
		//Определяем количество элементов на странице
		$iElCount=intval($_REQUEST['n']);
		$iElCount=($iElCount<10)?10:$iElCount;
		$iPage=(intval($_REQUEST['p1'])<1)?1:intval($_REQUEST['p1']);
		$arSortFields=Array('id','title','text_ident','date_add','date_edit','orderation','active','views_count');
		//Определяем порядок сортировки записей
		list($sOrderField,$sOrderDir)=$this->InitSort($arSortFields,$_REQUEST['order'],$_REQUEST['dir']);
		$sNewDir=($sOrderDir=='desc')?'asc':'desc';
		$arSort=array($sOrderField=>$sOrderDir);

		$arTables=array(
			'element'=>$this->obElement,
			'category'=>$this->obCategory
		);
		$arResult=GetAllList(array($sOrderField=>$sOrderDir),$arFilter,Array($iElCount*($iPage-1),$iElCount),$arTables);

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

		/* Перебрасываем главную страницу к файлам и формируем полную ссылку */
		$mainItem = false;

		if($arResult['ITEMS']&&count($arResult['ITEMS'])>0)
		{
			$arResult['sections']=array();
			foreach ($arResult['ITEMS'] as $arKey => $arValue)
			{
				/* Добавляем в массив полный путь к странице и его сокращённый вид для отображения в подсказке */
				if($arValue['TYPE']!='elm')
				{
					if(!array_key_exists($arValue['id'],$arResult['sections']))
					{
						$arResult['sections'][$arValue['id']]=$this->obCategory->GetFullPath($arValue['id']);
					}
					$arValue['path']=$arResult['sections'][$arValue['id']];
					$full_url = $arValue['path'];
				}
				else
				{
					if(!array_key_exists($arValue['parent_id'],$arResult['sections']))
					{
						$arResult['sections'][$arValue['parent_id']]=$this->obCategory->GetFullPath($arValue['parent_id']);
					}
					$arValue['path']=$arResult['sections'][$arValue['parent_id']];
					$full_url = $arValue['path'].$arValue['text_ident'].'.html';
				}
				$arValue['full_url'] = $full_url;
				$arValue['short_url'] = ShorterUrl($short_url);
				$arResult['ITEMS'][$arKey] = $arValue;
			}
			// Передаем данные смарти
			$this->smarty->assign('num_visible',$iElCount);
			$this->smarty->assign('dataList',$arResult);
			$this->smarty->assign('pages',$pages);
			$this->smarty->assign('order',Array('newdir'=>$sNewDir,'curdir'=>$sOrderDir,'field'=>$sOrderField));
			$this->smarty->assign('tree_to_move_to', $tree_to_move_to);
		}
		return '_basket';
	}

	/**
	 * Метод выполняет групповые действия над записями
	 */
	function CommonActions()
	{
		$arElements=$_POST['sel']['elm'];
		$arCats=$_POST['sel']['cat'];
		if (array_key_exists('comres',$_POST))
		{
			//Снятие общей активности для элементов
			if(is_array($arElements)&&count($arElements)>0)
			{
				$this->obElement->RestoreItems(array('->id'=>'('.join(',',$arElements).')'));
			}
			if(is_array($arCats)&&count($arCats)>0)
			{
				$this->obCategory->RestoreItems(array('->id'=>'('.join(',',$arCats).')'));
			}
		}
		elseif (array_key_exists('comdel',$_POST))
		{
			if($this->access_level>0) throw new CAccessError("CATSUBCAT_BASKET_NOT_DELETE_RECORD");
			if($_POST['deleteAll']!=1)
			{
				// Удаление выделенных элементов
				if(is_array($arElements)&&count($arElements)>0)
				{
					$this->obElement->DeleteItems(array('->id'=>'('.join(',',$arElements).')','>deleted'=>0));
				}
				if(is_array($arCats)&&count($arCats)>0)
				{
					$this->obCategory->DeleteItems(array('->id'=>'('.join(',',$arCats).')','>deleted'=>0));
				}
			}
			else
			{
				$this->obElement->DeleteItems(array('>deleted'=>0));
				$this->obCategory->DeleteItems(array('>deleted'=>0));
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
		$this->iCurSection=(intval($_REQUEST['CSC_catid'])<0)?0:intval($_REQUEST['CSC_catid']);
		$action=$_REQUEST['ACTION'];

		/* Получение типа данных (категория или элемент) */
		$sType = $_REQUEST['type'];

		/* Определение идентификатора записи */
		$iId = ($sType=='cat') ? intval($_REQUEST['CSC_catid']) : intval($_REQUEST['CSC_id']);
		// Обработка действий множественного выбора
		if(array_key_exists('ACTION',$_POST)&&($_POST['ACTION']=='common'))
		{
			$this->CommonActions();
		}
		else
		{
			$id=intval($_REQUEST['id']);
			if ($sType == "cat")
				$this->obEditable = $this->obCategory;
			elseif ($sType == "elm")
				$this->obEditable = $this->obElement;
			$this->page='';
			switch($action)
			{
				case "restore":
					$this->obEditable->RestoreItems(array('id'=>$_GET['CSC_id']));
					CUrlParser::get_instance()->Redirect("admin.php?".$KS_URL->GetUrl(Array('ACTION','CSC_id')));
				break;
				case "delete":
					if($iUserLevel>0) throw new CAccessError("CATSUBCAT_BASKET_NOT_DELETE_RECORD");
					$arData=$this->obEditable->GetById($iId);
					if(($iId==$arData['parent_id'])&&($iId==0)) throw new CError("CATSUBCAT_NOT_DELETE_MAIN");
					//Работа с модулем поиска
					if($this->obModules->IsActive('search'))
					{
						$obSearch=new CSearch();
						$obSearch->Delete('catsubcat',$this->obEditable->GenHash($arData));
					}
					$this->obEditable->DeleteItems(array('id'=>$iId,'>deleted'=>'-1'));
					$KS_URL->Set('CSC_catid',$arData['parent_id']);
					CUrlParser::get_instance()->Redirect("admin.php?".$KS_URL->GetUrl(Array('ACTION')));
				break;
				default:
					$page=$this->Table();
			}
		}
		$this->smarty->assign('userLevel',$this->access_level);
		if($_GET['mode']=='small')
		{
			echo $smarty->get_template_vars('last_error');
			$smarty->display('admin/catsubcat'.$page.'.tpl');
			die();
		}
		return $page;
	}
}
