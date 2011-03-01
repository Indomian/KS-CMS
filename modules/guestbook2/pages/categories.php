<?php
/**
 * @file categories.php
 * Файл для работы с разделами гостевой книги
 * Данный файл вызывается внутри класса CMain, $this указывает на объект $KS_MODULES.
 * Файл проекта kolos-cms.
 *
 * Создан 08.12.2009
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.5.4
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CModuleAdmin.php';
require_once MODULES_DIR.'/guestbook2/libs/class.CGB2Api.php';

class Cguestbook2AIcategories extends CModuleAdmin
{
	private $obGB2;

	function __construct($module='guestbook2',&$smarty,&$parent)
	{
		global $USER;
		parent::__construct($module,$smarty,$parent);
		$this->obGB2=CGB2API::get_instance();
		$this->obUser=$USER;
	}

	function EditForm($data=false)
	{
		if($data) $this->smarty->assign('data',$data);
		return '_edit';
	}

	function GetDataFromPost($prefix,$data)
	{
		$arResult=array();
		$arFields=$this->obGB2->GetCategoryFields();
		foreach($arFields as $sField)
		{
			$arResult[$sField]=$data[$prefix.$sField];
		}
		$arResult['answer']['content']=$data[$prefix.'answer'];
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
			$bError=0;
			$arFields=array(
				'title'=>htmlspecialchars($_POST['OS_title'],ENT_QUOTES,'UTF-8',false),
				'active'=>intval($_POST['OS_active']),
				'text_ident'=>$_POST['OS_text_ident'],
				'content'=>$_POST['content'],
				'orderation'=>intval($_POST['OS_orderation']),
			);
			if(strlen($arFields['title'])<1) $bError=$this->obModules->AddNotify('GB2_CATEGORY_TITLE_REQUIERED');
			if(strlen($arFields['text_ident'])<1) $arFields['text_ident']=Translit($arFields['title']);
			if(!IsTextIdent($arFields['text_ident'])) $bError=$this->obModules->AddNotify('GB2_CATEGORY_TEXT_IDENT_WRONG');
			if($bError>0) throw new CDataError('GB2_CATEGORY_FIELDS_ERROR');
			if($id>0)
			{
				$this->obGB2->obCategories->Update($id,$arFields);
			}
			else
			{
				$arFields['id']=-1;
				$this->obGB2->obCategories->AddAutoField('id');
				$this->obGB2->obCategories->Save('',$arFields);
			}
			if(!array_key_exists('update',$_REQUEST))
			{
				CUrlParser::Redirect("admin.php?".$KS_URL->GetUrl(Array('action','id')));
			}
			else
			{
				CUrlParser::Redirect("admin.php?".$KS_URL->GetUrl(array('action','id')).'&action=edit&id='.$id);
			}
		}
		catch(CDataError $e)
		{
			$arOrder=$this->GetDataFromPost('OS_',$_POST);
			$this->smarty->assign('last_error',$e->getMessage());
			$page=$this->EditForm($arOrder);
		}
		catch(CError $e)
		{
			$arOrder=$this->GetDataFromPost('OS_',$_POST);
			$this->smarty->assign('last_error',$e->getMessage());
			$page=$this->EditForm($arOrder);
		}
		return $page;
	}

	function Table()
	{
		/**
		 * @todo Убрать русский текст
		 */
		global $KS_MODULES;
		$arSortFields=$this->obGB2->GetCategoryFields();
		// Обработка порядка вывода элементов
		list($sOrderField,$sOrderDir)=$this->InitSort($arSortFields,$_REQUEST['order'],$_REQUEST['dir']);
		$sNewDir=($sOrderDir=='desc')?'asc':'desc';
		// Фильтр элементов
		$arFilter=array();
		if (class_exists('CFilterFrame'))
		{
			$obFilter=new CFilterFrame();
			$obFilter->AddField(array('FIELD'=>'id','METHOD'=>'='));
			$obFilter->AddField(array('FIELD'=>'title','METHOD'=>'~'));
			$obFilter->AddField(array('FIELD'=>'text_ident','METHOD'=>'~'));
			$obFilter->AddField(array('FIELD'=>'active','METHOD'=>'=','TYPE'=>'SELECT','VALUES'=>array(''=>$this->obModules->GetText('any'),'0'=>$this->obModules->GetText('inactive'),'1'=>$this->obModules->GetText('active'))));
			$arFilter=$obFilter->GetFilter();
			$obFilter->SetSmartyFilter('filter');
			$arTitles=array(
				'id'=>$this->obModules->GetText('field_category_id'),
				'title'=>$this->obModules->GetText('field_category_title'),
				'text_ident'=>$this->obModules->GetText('field_text_ident'),
				'active'=>$this->obModules->GetText('field_category_active'),
			);
			$this->smarty->assign('ftitles',$arTitles);
		}
		$this->obGB2->obCategories->Count($arFilter);
		$obPages = new CPageNavigation($this->obGB2->obCategories);
		$arSelect=$arSortFields;
		$arCategories=$this->obGB2->obCategories->GetList(array($sOrderField=>$sOrderDir),$arFilter,$obPages->GetLimits(),$arSelect);
		$this->smarty->assign('ITEMS',$arCategories);
		$this->smarty->assign('pages',$obPages->GetPages());
		$this->smarty->assign('order',Array('newdir'=>$sNewDir,'curdir'=>$sOrderDir,'field'=>$sOrderField));
		return '';
	}

	function Run()
	{
		global $USER,$KS_URL;
		//Проверка прав доступа
		if($USER->GetLevel($this->module)>KS_ACCESS_GB_CONFIG) throw new CAccessError('GB2_ACCESS_CONFIG');
		$action=$_REQUEST['action'];
		$id=intval($_REQUEST['id']);
		$this->page='';
		if (array_key_exists('comdel',$_POST))
		{
			$arElements=$_POST['sel']['elm'];
			$this->obGB2->DeleteCategory($arElements);
			$this->obModules->AddNotify('GB2_CATEGORY_NOTIFY_DELETE_OK','',NOTIFY_MESSAGE);
		}
		if (array_key_exists('comact',$_POST))
		{
			$arElements=$_POST['sel']['elm'];
			$this->obGB2->obCategories->Update($arElements,array('active'=>'1'));
			$this->obModules->AddNotify('GB2_CATEGORY_NOTIFY_ACTIVE_OK','',NOTIFY_MESSAGE);
		}
		if (array_key_exists('comdea',$_POST))
		{
			$arElements=$_POST['sel']['elm'];
			$this->obGB2->obCategories->Update($arElements,array('active'=>'0'));
			$this->obModules->AddNotify('GB2_CATEGORY_NOTIFY_DEACTIVE_OK','',NOTIFY_MESSAGE);
		}
		switch($action)
		{
			case "edit":
				$data=$this->obGB2->obCategories->GetRecord(array('id'=>$id));
			case "new":
				$page=$this->EditForm($data);
			break;
			case "save":
				$page=$this->Save($id);
			break;
			case "delete":
				$this->obGB2->DeleteCategory($id);
				$this->obModules->AddNotify('GB2_CATEGORY_NOTIFY_DELETE_OK','',NOTIFY_MESSAGE);
				CUrlParser::Redirect("admin.php?".$KS_URL->GetUrl(Array('action','id')));
			default:
				$page=$this->Table();
		}
		return '_category'.$page;
	}
}

