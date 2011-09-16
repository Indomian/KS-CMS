<?php
/**
 * @file dummy/pages/index.php
 * Файл обработки основных операций модуля dummy
 * Файл проекта kolos-cms.
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 * @since 31.08.2011
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CModuleAdmin.php';

class CdummyAIindex extends CModuleAdmin
{
	private $obDummy;
	private $access_level;

	function __construct($module='dummy',&$smarty,&$parent)
	{
		global $USER;
		parent::__construct($module,$smarty,$parent);
		$this->obDummy=new CDummy();
		$this->access_level=10;
	}

	function EditForm($data=false)
	{
		return '';
	}

	/**
	 * Метод выполняет реализацию операции сохранения данных
	 */
	function Save($id)
	{
		$KS_URL=CUrlParser::get_instance();
		if(!array_key_exists('update',$_REQUEST))
			$KS_URL->Redirect("admin.php?".$KS_URL->GetUrl(Array('ACTION','type','CSC_id')));
		else
			$KS_URL->Redirect("admin.php?".$KS_URL->GetUrl('ACTION','CSC_id','CSC_catid').'&ACTION=edit'.$sAdd);
	}

	/**
	 * Метод выводит таблицу записей текстовых страниц
	 */
	function Table()
	{
		// Получаем полный список элементов и разделов
		$arFilter=array();
		if (class_exists('CFilterFrame'))
		{
			$obFilter=new CFilterFrame();
			$obFilter->AddField(array('FIELD'=>'title','METHOD'=>'~'));
			$arFilter=$obFilter->GetFilter();
			$obFilter->SetSmartyFilter('filter');
			$arTitles=array(
				'title'=>$this->obModules->GetText('dummy'),
			);
			$this->smarty->assign('ftitles',$arTitles);
		}
		$arSortFields=Array('id','title','text_ident','date_add','date_edit','orderation','active','views_count');
		//Определяем порядок сортировки записей
		list($sOrderField,$sOrderDir)=$this->InitSort($arSortFields,$_REQUEST['order'],$_REQUEST['dir']);
		$sNewDir=($sOrderDir=='desc')?'asc':'desc';
		$arSort=array($sOrderField=>$sOrderDir);

		$arResult=$this->obDummy->GetList($arSort,$arFilter);
		$this->smarty->assign('dataList',$arResult);
		$this->smarty->assign('order',Array('newdir'=>$sNewDir,'curdir'=>$sOrderDir,'field'=>$sOrderField));
		return '';
	}

	function Run()
	{
		global $KS_URL;
		//Проверка прав доступа
		$this->access_level = $this->obUser->GetLevel($this->module);
		if($this->access_level>0)
			throw new CAccessError("DUMMY_NOT_MANAGE");
		$action='';
		$Id=0;
		$data=false;
		if(isset($_REQUEST['action'])) $action=$_REQUEST['action'];
		if(isset($_REQUEST['id'])) $Id=intval($_REQUEST['id']);
		switch($action)
		{
			case "edit":
				$data=$this->obDummy->GetById($iId);
			case "new":
				$page=$this->EditForm($data);
			break;
			case "save":
				$page=$this->Save($Id);
			break;
			default:
				$page=$this->Table();
		}
		return $page;
	}
}
