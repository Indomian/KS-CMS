<?php
/**
 * @file banners.php
 * Файл обработки баннеров
 * Файл проекта kolos-cms.
 *
 * Создан 08.04.2010
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CModuleAdmin.php';
require_once MODULES_DIR.'/banners/libs/class.CBannersApi.php';

class CbannersAIbanners extends CModuleAdmin
{
	private $obBanners;

	function __construct($module='banners',&$smarty,&$parent)
	{
		parent::__construct($module,$smarty,$parent);
		$this->obBanners=CBannersAPI::get_instance();
	}

	function EditForm($data=false)
	{
		$arData=$this->obBanners->obBannerTypes->GetList();
		$arBannerTypes=array();
		if(is_array($arData))
		{
			foreach($arData as $arRow)
			{
				$arBannerTypes[$arRow['id']]=$arRow;
			}
		}
		if($data) $this->smarty->assign('data',$data);
		$this->smarty->assign('TYPES',$arBannerTypes);
		return '_edit';
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
		global $KS_URL,$USER;
		try
		{
			if(strlen($_POST['OS_title'])==0) throw new CDataError('BANNERS_TITLE_TOO_SHORT');
			if(strlen($_POST['OS_text_ident'])==0) throw new CDataError('BANNERS_TEXT_IDENT_TOO_SHORT');
			if(!preg_match('#^[a-z0-9]+$#i',$_POST['OS_text_ident'])) throw new CDataError('BANNERS_TEXT_IDENT_WRONG');
			$_POST['OS_active_from']=strtotime($_POST['OS_active_from']);
			$_POST['OS_active_to']=strtotime($_POST['OS_active_to']);
			$_POST['OS_active']=intval($_POST['OS_active']);
			$this->obBanners->SaveBanner('OS_',$_POST);
			if(!array_key_exists('update',$_REQUEST))
			{
				CUrlParser::Redirect("admin.php?".$KS_URL->GetUrl(array('action','id')));
			}
			else
			{
				CUrlParser::Redirect("admin.php?".$KS_URL->GetUrl(array('action','id')).'&action=edit&id='.$id);
			}
		}
		catch(CError $e)
		{
			$arOrder=$this->GetDataFromPost('OS_',$_POST);
			$this->smarty->assign('last_error',$e);
			$page=$this->EditForm($arOrder);
		}
		return $page;
	}

	function Table()
	{
		//Список полей для сортирвки
		$arSortFields=array('id','title','active','date_add','client_title','type_title');
		//Получение списка баннеропозиций
		$arFilterBT=array(''=>$this->obModules->GetText('any'));
		if($arData=$this->obBanners->Type()->GetList())
		{
			foreach($arData as $arRow)
			{
				$arFilterBT[$arRow['id']]=$arRow['title'].' ['.$arRow['text_ident'].']';
			}
		}
		//Получение списка рекламных кампаний
		$arFilterCT=array(''=>$this->obModules->GetText('any'));
		if($arData=$this->obBanners->Client()->GetList())
		{
			foreach($arData as $arRow)
			{
				$arFilterCT[$arRow['id']]=$arRow['title'];
			}
		}
		// Обработка порядка вывода элементов
		list($sOrderField,$sOrderDir)=$this->InitSort($arSortFields,$_REQUEST['order'],$_REQUEST['dir']);
		$sNewDir=($sOrderDir=='desc')?'asc':'desc';
		// Фильтр элементов
		$arFilter=array();
		if (class_exists('CFilterFrame'))
		{
			$obFilter=new CFilterFrame();
			$obFilter->AddField(array('FIELD'=>'title','METHOD'=>'~'));
			$obFilter->AddField(array('FIELD'=>'active','METHOD'=>'=','TYPE'=>'SELECT','VALUES'=>array('1'=>$this->obModules->GetText('active'),'0'=>$this->obModules->GetText('inactive'))));
			$obFilter->AddField(array('FIELD'=>'type_id','METHOD'=>'=','TYPE'=>'SELECT','VALUES'=>$arFilterBT));
			$obFilter->AddField(array('FIELD'=>'client_id','METHOD'=>'=','TYPE'=>'SELECT','VALUES'=>$arFilterCT));
			$arFilter=$obFilter->GetFilter();
			$obFilter->SetSmartyFilter('filter');
			$arTitles=array(
				'title'=>$this->obModules->GetText('filter_title'),
				'active'=>$this->obModules->GetText('filter_active'),
				'type_id'=>$this->obModules->GetText('filter_type_id'),
				'client_id'=>$this->obModules->GetText('filter_client_id'),
			);
			$this->smarty->assign('ftitles',$arTitles);
		}
		if(!is_array($arFilter)) $arFilter=array();
		$this->obBanners->Banner()->Count($arFilter);
		$obPages = new CPages();
		$arSelect=array(
			'id',
			'title',
			'active',
			'date_add',
			'type_id',
			'client_id',
			$this->obBanners->Client()->sTable.'.title'=>'client_title',
			$this->obBanners->Type()->sTable.'.title'=>'type_title',
		);
		$arFilter['<?'.$this->obBanners->Banner()->sTable.'.client_id']=$this->obBanners->Client()->sTable.'.id';
		$arFilter['<?'.$this->obBanners->Banner()->sTable.'.type_id']=$this->obBanners->Type()->sTable.'.id';
		$arBanners=$this->obBanners->Banner()->GetList(array($sOrderField=>$sOrderDir),$arFilter,$obPages->GetLimits(),$arSelect);
		$this->smarty->assign('ITEMS',$arBanners);
		$this->smarty->assign('pages',$obPages->GetPages());
		$this->smarty->assign('order',Array('newdir'=>$sNewDir,'curdir'=>$sOrderDir,'field'=>$sOrderField));
		return '';
	}

	function Run()
	{
		$KS_URL=CUrlParser::get_instance();
		//Проверка прав доступа
		if($this->obUser->GetLevel($this->module)>KS_ACCESS_BANNERS_VIEW) throw new CAccessError('BANNERS_ACCESS_REPLY');
		$action='';
		if(array_key_exists('action',$_REQUEST))
			$action=$_REQUEST['action'];
		$id=0;
		if(array_key_exists('id',$_REQUEST))
			$id=intval($_REQUEST['id']);
		$this->page='';
		if (array_key_exists('comdel',$_POST))
		{
			$arElements=$_POST['sel']['elm'];
			$this->obBanners->DeleteBanners($arElements);
		}
		switch($action)
		{
			case "edit":
				$data=$this->obBanners->GetBanner($id);
			case "new":
				$page=$this->EditForm($data);
			break;
			case "save":
				$page=$this->Save($id);
			break;
			case "delete":
				$this->obBanners->DeleteBanners($id);
				CUrlParser::Redirect("admin.php?".$KS_URL->GetUrl(Array('action','id')));
			default:
				$page=$this->Table();
		}
		return $page;
	}
}
