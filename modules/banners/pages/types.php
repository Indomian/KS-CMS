<?php
/**
 * @file types.php
 * Файл обработки типов баннеров
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
require_once MODULES_DIR.'/main/libs/class.CImageUploader.php';

class CbannersAITypes extends CModuleAdmin
{
	private $obBanners;

	function __construct($module='banners',&$smarty,&$parent)
	{
		parent::__construct($module,$smarty,$parent);
		$this->obBanners=CBannersAPI::get_instance();
	}

	function EditForm($data=false)
	{
		if (class_exists('CFields'))
		{
			$obFields=new CFields();
			if($arFields=$obFields->GetList(Array('id'=>'asc'),Array('module'=>$this->module,'type'=>$this->obBanners->Type()->sTable)))
			{
				foreach($arFields as $item)
				{
					$arDefaults['ext_'.$item['title']]=$item['default'];
				}
				$this->smarty->assign('addFields',$arFields);
			}
		}
		if($data) $this->smarty->assign('data',$data);
		return '_edit';
	}

	function GetDataFromPost($prefix,$data)
	{
		$arResult=array();
		$arFields=$this->obBanners->GetTypesFields();
		foreach($arFields as $sField)
		{
			if(array_key_exists($prefix.$sField,$data))
			{
				$arResult[$sField]=$data[$prefix.$sField];
			}
			else
			{
				$arResult[$sField]='';
			}
		}
		return $arResult;
	}
	/**
	 * Метод выполняет реализацию операции сохранения данных
	 */
	function Save($id)
	{
		$KS_URL=CUrlParser::get_instance();
		try
		{
			$bError=0;
			//Картинка фона страницы
			$obImageUploader=new CImageUploader('F_icon','FILE_UPLOADER',32,32,10000);
			try
			{
				if($obImageUploader->IsReady())
				{
					if($sImageName=$obImageUploader->Upload($this->obBanners->Type()->GetUploadFolder()))
					{
						$_POST['OS_icon']=$sImageName;
					}
					else
					{
						$bError=$this->obModules->AddNotify('BANNERS_TYPE_ICON_UPLOAD_ERROR');
					}
				}
			}
			catch(CError $e)
			{
				$code=$e->getCode();
				switch($code)
				{
					case 1:
						$bError=$this->obModules->AddNotify('BANNERS_TYPE_ICON_TOO_BIG');
					break;
					case 2:
						$bError=$this->obModules->AddNotify('BANNERS_TYPE_ICON_TOO_WIDE');
					break;
					case 3:
						$bError=$this->obModules->AddNotify('BANNERS_TYPE_ICON_TOO_HIGH');
					break;
					default:
						$bError=$this->obModules->AddNotify('BANNERS_TYPE_ICON_UPLOAD_ERROR');
				}
			}
			$_POST['OS_title']=EscapeHTML($_POST['OS_title']);
			$_POST['OS_text_ident']=EscapeHTML($_POST['OS_text_ident']);
			$_POST['OS_description']=EscapeHTML($_POST['OS_description']);
			//Проверяем введённые данные
			if(strlen($_POST['OS_title'])==0)
				$bError=$this->obModules->AddNotify('BANNERS_TITLE_TOO_SHORT');
			if(strlen($_POST['OS_text_ident'])==0)
				$bError=$this->obModules->AddNotify('BANNERS_TEXT_IDENT_TOO_SHORT');
			elseif(!IsTextIdent($_POST['OS_text_ident']))
				$bError=$this->obModules->AddNotify('BANNERS_TEXT_IDENT_WRONG');
			if(array_key_exists('OS_active',$_POST))
				$_POST['OS_active']=intval($_POST['OS_active']);
			else
				$_POST['OS_active']=0;
			if($bError==0)
			{
				if($id=$this->obBanners->Type()->Save('OS_',$_POST))
				{
					if(!array_key_exists('update',$_REQUEST))
					{
						$KS_URL->Redirect("admin.php?".$KS_URL->GetUrl(array('action','id')));
					}
					else
					{
						$KS_URL->Redirect("admin.php?".$KS_URL->GetUrl(array('action','id')).'&action=edit&id='.$id);
					}
				}
				else
				{
					throw new CError('BANNERS_TYPE_SAVE_ERROR');
				}
			}
			else
			{
				throw new CDataError('BANNERS_TYPE_WRONG_FIELDS');
			}
		}
		catch(CError $e)
		{
			$arOrder=$this->GetDataFromPost('OS_',$_POST);
			$this->smarty->assign('last_error',$e->__toString());
			$page=$this->EditForm($arOrder);
		}
		return $page;
	}

	function Table()
	{
		$arSortFields=$this->obBanners->GetTypesFields();
		// Обработка порядка вывода элементов
		list($sOrderField,$sOrderDir)=$this->InitSort($arSortFields,$_REQUEST['order'],$_REQUEST['dir']);
		$sNewDir=($sOrderDir=='desc')?'asc':'desc';
		// Фильтр элементов
		$arFilter=array();
		if (class_exists('CFilterFrame'))
		{
			$obFilter=new CFilterFrame();
			$obFilter->AddField(array('FIELD'=>'text_ident','METHOD'=>'~'));
			$obFilter->AddField(array('FIELD'=>'title','METHOD'=>'~'));
			$obFilter->AddField(array('FIELD'=>'active','METHOD'=>'=','TYPE'=>'SELECT','VALUES'=>array(''=>$this->obModules->GetText('any'),'1'=>$this->obModules->GetText('active'),'0'=>$this->obModules->GetText('inactive'))));
			$arFilter=$obFilter->GetFilter();
			$obFilter->SetSmartyFilter('filter');
			$arTitles=array(
				'text_ident'=>$this->obModules->GetText('filter_text_ident'),
				'title'=>$this->obModules->GetText('filter_title'),
				'active'=>$this->obModules->GetText('filter_active'),
			);
			$this->smarty->assign('ftitles',$arTitles);
		}
		$iCount=$this->obBanners->Type()->Count($arFilter);
		$obPages = $this->InitPages(20);
		$arOrders=$this->obBanners->Type()->GetList(array($sOrderField=>$sOrderDir),$arFilter,$obPages->GetLimits($iCount));
		$this->smarty->assign('ITEMS',$arOrders);
		$this->smarty->assign('pages',$obPages->GetPages($iCount));
		$this->smarty->assign('order',Array('newdir'=>$sNewDir,'curdir'=>$sOrderDir,'field'=>$sOrderField));
		return '';
	}

	function Run()
	{
		$KS_URL=CUrlParser::get_instance();
		//Проверка прав доступа
		if($this->obUser->GetLevel($this->module)>KS_ACCESS_BANNERS_TYPES) throw new CAccessError('BANNERS_ACCESS_DENIED');
		$action='';
		if(array_key_exists('action',$_REQUEST))
			$action=$_REQUEST['action'];
		$id=0;
		if(array_key_exists('id',$_REQUEST))
			$id=intval($_REQUEST['id']);
		if (array_key_exists('comdel',$_POST)&&
			array_key_exists('sel',$_POST)&&
			is_array($_POST['sel'])&&
			array_key_exists('elm',$_POST['sel'])&&
			is_array($_POST['sel']['elm'])&&
			count($_POST['sel']['elm'])>0)
		{
			$this->obBanners->DeleteTypes($_POST['sel']['elm']);
		}
		$data=false;
		switch($action)
		{
			case "edit":
				$data=$this->obBanners->Type()->GetRecord(array('id'=>$id));
			case "new":
				$page=$this->EditForm($data);
			break;
			case "save":
				$page=$this->Save($id);
			break;
			case "delete":
				$this->obBanners->DeleteTypes($id);
				$KS_URL->Redirect("admin.php?".$KS_URL->GetUrl(Array('action','id')));
			default:
				$page=$this->Table();
		}
		$page='_types'.$page;
		return $page;
	}
}
