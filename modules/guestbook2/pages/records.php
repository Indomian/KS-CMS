<?php
/**
 * \file recors.php
 * Файл для работы с записями гостевой книги
 * Данный файл вызывается внутри класса CMain, $this указывает на объект $KS_MODULES.
 * Файл проекта kolos-cms.
 *
 * Создан 08.12.2009
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 2.5.3
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CModuleAdmin.php';
require_once MODULES_DIR.'/guestbook2/libs/class.CGB2Api.php';

class Cguestbook2AIrecords extends CModuleAdmin
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
		$this->smarty->assign('show_editor',$this->obModules->GetConfigVar($this->module,'int_htmleditor',0));
		$this->smarty->assign('categories',$this->obGB2->GetCategories());
		return '_edit';
	}

	function GetDataFromPost($prefix,$data)
	{
		$arResult=array();
		$arFields=$this->obGB2->GetPostFields();
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
			if($id>0)
			{
				$_POST['OS_active']=intval($_POST['OS_active']);
				if(strlen($_POST['OS_content'])<1) throw new CError('GB2_CONTENT_REQUIRED');
				$this->obGB2->UpdatePost($id,'OS_',$_POST);
				if(strlen($_POST['OS_answer'])>0)
				{
					$this->obGB2->AddAnswer($id,$_POST['OS_answer']);
				}
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
		global $KS_MODULES;
		$arSortFields=$this->obGB2->GetPostFields();
		// Обработка порядка вывода элементов
		list($sOrderField,$sOrderDir)=$this->InitSort($arSortFields,$_REQUEST['order'],$_REQUEST['dir']);
		$sNewDir=($sOrderDir=='desc')?'asc':'desc';
		$arCategories=$this->obGB2->obCategories->GetList(array('orderation'=>'asc'));
		$arCatFilter=array(
			''=>'Любая',
		);
		foreach($arCategories as $arRow)
		{
			$arCatFilter[$arRow['id']]=$arRow['title'];
		}
		// Фильтр элементов
		$arFilter=array();
		if (class_exists('CFilterFrame'))
		{
			$obFilter=new CFilterFrame();
			$obFilter->AddField(array('FIELD'=>'id','METHOD'=>'='));
			$obFilter->AddField(array('FIELD'=>'user_id','METHOD'=>'='));
			$obFilter->AddField(array('FIELD'=>'user_name','METHOD'=>'~'));
			$obFilter->AddField(array('FIELD'=>'title','METHOD'=>'~'));
			$obFilter->AddField(array('FIELD'=>'active','METHOD'=>'=','TYPE'=>'SELECT','VALUES'=>array('1'=>$this->obModules->GetText('visible'),'2'=>$this->obModules->GetText('invisible'))));
			$obFilter->AddField(array('FIELD'=>'category_id','METHOD'=>'=','TYPE'=>'SELECT','VALUES'=>$arCatFilter));
			$arFilter=$obFilter->GetFilter();
			$obFilter->SetSmartyFilter('filter');
			$arTitles=array(
				'id'=>$this->obModules->GetText('field_id'),
				'user_id'=>$this->obModules->GetText('field_user_id'),
				'user_name'=>$this->obModules->GetText('field_user_name'),
				'title'=>$this->obModules->GetText('field_title'),
				'active'=>$this->obModules->GetText('field_element_active'),
				'category_id'=>$this->obModules->GetText('field_category')
			);
			$this->smarty->assign('ftitles',$arTitles);
		}
		$arFilter['<?'.$this->obGB2->obPosts->sTable.'.user_id']=$this->obUser->sTable.'.id';
		$this->obGB2->obPosts->Count($arFilter);
		$obPages = new CPageNavigation($this->obGB2->obPosts);
		$arSelect=$arSortFields;
		foreach($this->obUser->arFields as $sItem)
			$arSelect[]=$this->obUser->sTable.'.'.$sItem;
		$arOrders=$this->obGB2->obPosts->GetList(array($sOrderField=>$sOrderDir),$arFilter,$obPages->GetLimits(),$arSelect);
		$this->smarty->assign('ITEMS',$arOrders);
		$this->smarty->assign('pages',$obPages->GetPages());
		$this->smarty->assign('categories',$arCatFilter);
		$this->smarty->assign('order',Array('newdir'=>$sNewDir,'curdir'=>$sOrderDir,'field'=>$sOrderField));
		return '';
	}

	function Run()
	{
		global $USER,$KS_URL;
		//Проверка прав доступа
		if($USER->GetLevel($this->module)>KS_ACCESS_GB2_REPLY)
			throw new CAccessError('GB2_ACCESS_REPLY');

		$action=$_REQUEST['action'];
		$id=intval($_REQUEST['id']);
		$this->page='';

		if (array_key_exists('comdel',$_POST))
		{
			$arElements=$_POST['sel']['elm'];
			$this->obGB2->DeletePost($arElements);
			$this->obModules->AddNotify('GB2_NOTIFY_DELETE_OK','',NOTIFY_MESSAGE);
		}
		if (array_key_exists('comact',$_POST))
		{
			$arElements=$_POST['sel']['elm'];
			$this->obGB2->obPosts->Update($arElements,array('active'=>'1'));
			$this->obModules->AddNotify('GB2_NOTIFY_ACTIVE_OK','',NOTIFY_MESSAGE);
		}
		if (array_key_exists('comdea',$_POST))
		{
			$arElements=$_POST['sel']['elm'];
			$this->obGB2->obPosts->Update($arElements,array('active'=>'0'));
			$this->obModules->AddNotify('GB2_NOTIFY_DEACTIVE_OK','',NOTIFY_MESSAGE);
		}
		switch($action)
		{
			case "edit":
				$data=$this->obGB2->GetPost($id);
			case "new":
				$page=$this->EditForm($data);
			break;
			case "save":
				$page=$this->Save($id);
			break;
			case "delete":
				$this->obGB2->DeletePost($id);
				CUrlParser::Redirect("admin.php?".$KS_URL->GetUrl(Array('action','id')));
			default:
				$page=$this->Table();
		}
		return $page;
	}
}

