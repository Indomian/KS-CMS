<?php
/**
 * \file class.CModuleAdmin.php
 * Файл контейнер для класса управление административным интерфейсом
 * Файл проекта kolos-cms.
 *
 * Создан 10.09.2009
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 1.0
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CMain.php';
require_once MODULES_DIR.'/main/libs/class.CConfigParser.php';

class CModuleAdmin extends CBaseAPI
{
	protected $module;
	protected $page;
	protected $smarty;
	protected $obModules;
	protected $obUser;
	protected $sAction;
	protected $obUrl;

	function __construct($module_name,&$smarty,CModuleManagment &$parent)
	{
		global $USER;
		$this->module=$module_name;
		$this->smarty=$smarty;
		$this->obModules=$parent;
		$this->sAction='';
		$this->obUser=&$USER;
		$this->obUrl=CUrlParser::get_instance();
		if($this->obModules->IsActive('interfaces'))
			$this->obModules->IncludeModule('interfaces');
		else
			throw new CError('MAIN_MODULE_NOT_FOUND','','interfaces');
	}

	/**
	 * Метод выполняет инициализацию сортировки
	 * @param $arSortFields array - список полей по которым можно выполнять сортировку записей
	 * @param $sortField string - поле сортировки
	 * @param $sortDir string - направление сортировки
	 * @return array - массив, 0 элемент - поле сортировки, 1 - направление
	 */
	protected function InitSort($arSortFields,$sortField=false,$sortDir=false, $sOrderField = 'id', $sOrderDir = 'asc')
	{
		if(!array_key_exists($this->module,$_SESSION)) $_SESSION[$this->module]=array();
		if($sortField==false && isset($_REQUEST['order']))
			$sortField=$_REQUEST['order'];
		else
			$sortField='';
		if($sortDir==false && isset($_REQUEST['dir']))
			$sortDir=$_REQUEST['dir'];
		else
			$sortDir='';
		// Обработка порядка вывода элементов
		if($sortField!='')
			$sOrderField=(in_array($sortField,$arSortFields))?$sortField:$arSortFields[0];
		elseif(array_key_exists('admin_sort_'.$this->page.'_by',$_SESSION[$this->module]) && $_SESSION[$this->module]['admin_sort_'.$this->page.'_by']!='')
			$sOrderField=$_SESSION[$this->module]['admin_sort_'.$this->page.'_by'];
		else
			$sOrderField=$this->obModules->GetConfigVar($this->module,'admin_sort_'.$this->page.'_by');
		//Направление сортировки
		$sOrderDir='desc';
		if($sortDir!='')
		{
			if($sortDir=='asc')
				$sOrderDir='asc';
		}
		elseif(array_key_exists('admin_sort_'.$this->page.'_dir',$_SESSION[$this->module]) && $_SESSION[$this->module]['admin_sort_'.$this->page.'_dir']!='')
			$sOrderDir=$_SESSION[$this->module]['admin_sort_'.$this->page.'_dir'];
		else
			$OrderDir=$this->obModules->GetConfigVar($this->module,'admin_sort_'.$this->page.'_dir');
		$sOrderField=(in_array($sOrderField,$arSortFields))?$sOrderField:$arSortFields[0];
		//Сохраняем сортировку в сессию
		$_SESSION[$this->module]['admin_sort_'.$this->page.'_by']=$sOrderField;
		$_SESSION[$this->module]['admin_sort_'.$this->page.'_dir']=$sOrderDir;
		return array($sOrderField,$sOrderDir);
	}

	/**
	 * Метод выполняет инициализацию объекта постраничной навигации
	 */
	protected function InitPages($iCount=false)
	{
		if(!$iCount)
			$iCount=$this->obModules->GetConfigVar('main','admin_items_count',10);
		if(array_key_exists('n',$_REQUEST) && $_REQUEST['n']>0)
			$iCount=intval($_REQUEST['n']);
		return new CPages($iCount);
	}

	/**
	 * Метод выполняет формирование кода операции
	 */
	protected function ParseAction($action='')
	{
		if($this->sAction=='')
		{
			if(($action=='') && array_key_exists('action',$_REQUEST))
				$action=$_REQUEST['action'];
			elseif($action=='')
				$action='index';
			$this->sAction=$action;
		}
	}

	/**
	 * Данный метод выполняет обработку текущей страницы.
	 * Возвращет название шаблона для рендеринга
	 */
	function Run()
	{
		return '';
	}

	/**
	 * Данный метод вызывается при выполнении ajax запроса к административному интерфейсу
	 */
	function RunAjax()
	{
		$arResult=array(
			'result'=>'ok',
		);
		return json_encode();
	}
}

/**
 * Класс выполняет работу со страницами табличной структуры в система администрирования
 */
class CAdminTable extends CModuleAdmin
{
	protected $arColumns; /**<Массив описывающий колонки таблицы*/
	protected $obConfigParser; /**<Объект работы с файлами конфигурации*/
	protected $sTableName;	/**<Название таблицы*/

	function __construct($module_name,&$smarty,&$parent)
	{
		parent::__construct($module_name,$smarty,$parent);
		$this->arColumns=array();
		$this->obConfigParser=new CConfigParser($module_name);
		$this->obConfigParser->LoadConfig();
	}

	/**
	 * Метод выполняет подготовку параметров колонок
	 */
	protected function PrepareColumns()
	{
		$arCols=$this->obModules->GetConfigVar($this->module,'table'.$this->sTableName);
		foreach($this->arColumns as $key=>$arColumn)
		{
			if($arCols[$key]!='')
				$this->arColumns[$key]['show']=intval($arCols[$key]);
			else
				$this->arColumns[$key]['show']=intval($arColumn['default']);
		}
	}

	/**
	 * Метод выполняет отрисовку таблицы
	 */
	function Table()
	{
		$this->PrepareColumns();
	}

	/**
	 * Метод выполняет обработку операций
	 */
	function Run($action='')
	{
		global $KS_URL;
		$this->ParseAction($action);
		switch($this->sAction)
		{
			case 'confcols':
				//Выполняем конфигурацию колонок
				$this->PrepareColumns();
				$this->smarty->assign('columns',$this->arColumns);
				$this->obModules->AddChainItem('title_config_columns',$KS_URL->Url());
				$page='confcols';
			break;
			case 'savecols':
				if($_SERVER['REQUEST_METHOD']=='POST' && !array_key_exists('cancel',$_POST))
				{
					$arResult=array();
					foreach($this->arColumns as $key=>$arColumn)
					{
						$arResult[$key]=intval($_POST['show'][$key]);
					}
					$this->obConfigParser->Set('table'.$this->sTableName,$arResult);
					$this->obConfigParser->WriteConfig();
				}
				$KS_URL->redirect($KS_URL->Url(array('action')));
			break;
			default:
				$page=parent::Run();
		}
		return $page;
	}
}

