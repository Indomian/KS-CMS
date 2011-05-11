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
	private $arRights;

	function __construct($module='banners',&$smarty,&$parent)
	{
		parent::__construct($module,$smarty,$parent);
		$this->obBanners=CBannersAPI::get_instance();
		$this->arRights=array(
			'canView'=>false,
			'canEdit'=>false,
		);
	}

	function EditForm($data=false)
	{
		$arBannerTypes=$this->obBanners->Type()->GetList();
		$arClients=$this->obBanners->Client()->GetList();
		if(!$data)
		{
			if(!$this->arRights['canEdit'])
			{
				$this->obModules->AddNotify('BANNERS_ACCESS_REPLY');
				$this->obUrl->Redirect("admin.php?".$this->obUrl->GetUrl(Array('action','id')));
			}
			$data=array();
			$data['times']=array();
			for($i=0;$i<7;$i++)
				for($j=0;$j<24;$j++)
					$data['times'][$i][$j]=1;
		}
		else
		{
			$arStatistics=array();
			if($arStat=$this->obBanners->GetStatistics($data['id']))
			{
				$arTmp=$arStat['list'];
				if(is_array($arTmp))
				{
					foreach($arTmp as $arDay)
					{
						$sDay=date('d.m.Y',$arDay['date']);
						$sHour=date('H',$arDay['date']);
						$arStatistics[$sDay][intval($sHour)]=$arDay;
					}
				}
				$data['statisticsFrom']=$arStat['date_from'];
				$data['statisticsTo']=$arStat['date_to'];
			}
			//$data['statistics']=$this->obBanners->GetStatistics($data['id']);
			$data['statistics']=$arStatistics;
		}
		if (class_exists('CFields'))
		{
			$obFields=new CFields();
			if($arFields=$obFields->GetList(Array('id'=>'asc'),Array('module'=>$this->module,'type'=>$this->obBanners->Banner()->sTable)))
			{
				foreach($arFields as $item)
				{
					$arDefaults['ext_'.$item['title']]=$item['default'];
				}
				$this->smarty->assign('addFields',$arFields);
			}
		}
		$this->smarty->assign('data',$data);
		$this->smarty->assign('TYPES',$arBannerTypes);
		$this->smarty->assign('CLIENTS',$arClients);
		$this->obModules->UseJavaScript('/banners/admin.js');
		return '_edit';
	}

	function GetDataFromPost()
	{
		$arResult=array();
		$arFields=$this->obBanners->GetBannerFields();
		foreach($arFields as $sField)
		{
			if(array_key_exists('OS_'.$sField,$_POST))
			{
				$arResult[$sField]=$_POST['OS_'.$sField];
			}
		}
		if(array_key_exists('times',$_POST) && is_array($_POST['times']))
		{
			$arResult['times']=array();
			for($i=1;$i<8;$i++)
			{
				$arResult['times'][$i-1]=array();
				if(array_key_exists($i,$_POST['times']) && is_array($_POST['times'][$i]))
				{
					for($j=1;$j<25;$j++)
						if(array_key_exists($j,$_POST['times'][$i]))
							$arResult['times'][$i-1][$j-1]=1;
						else
							$arResult['times'][$i-1][$j-1]=0;
				}
				else
				{
					for($j=1;$j<25;$j++)
						$arResult['times'][$i-1][$j-1]=0;
				}
			}
		}
		else
		{
			$arResult['times']=array();
			for($i=1;$i<8;$i++)
				for($j=1;$j<25;$j++)
					$arResult['times'][$i-1][$j-1]=1;
		}
		return $arResult;
	}
	/**
	 * Метод выполняет реализацию операции сохранения данных
	 */
	function Save($id)
	{
		if(!$this->arRights['canEdit'])
			throw new CAccessError('BANNERS_ACCESS_REPLY');
		try
		{
			$bErrors=0;
			if(strlen($_POST['OS_title'])==0)
				$bErrors=$this->obModules->AddNotify('BANNERS_TITLE_TOO_SHORT');
			else
				$_POST['OS_title']=EscapeHTML($_POST['OS_title']);
			if(strlen($_POST['OS_text_ident'])==0)
				$bErrors=$this->obModules->AddNotify('BANNERS_TEXT_IDENT_TOO_SHORT');
			elseif(!IsTextIdent($_POST['OS_text_ident']))
				$bErrors=$this->obModules->AddNotify('BANNERS_TEXT_IDENT_WRONG');
			if(isset($_POST['OS_active_from'])&&strlen($_POST['OS_active_from'])>0)
				$_POST['OS_active_from']=String2Time($_POST['OS_active_from']);
			else
				$_POST['OS_active_from']=0;
			if(isset($_POST['OS_active_to'])&&strlen($_POST['OS_active_to'])>0)
				$_POST['OS_active_to']=String2Time($_POST['OS_active_to']);
			else
				$_POST['OS_active_to']=0;
			//Активность баннера
			if(isset($_POST['OS_active']))
				$_POST['OS_active']=intval($_POST['OS_active']);
			else
				$_POST['OS_active']=0;
			//Ведение статистики
			if(isset($_POST['OS_save_stats']))
				$_POST['OS_save_stats']=intval($_POST['OS_save_stats']);
			else
				$_POST['OS_save_stats']=0;
			if($_POST['OS_type_id']>0)
				if(!$this->obBanners->Type()->GetById(intval($_POST['OS_type_id'])))
					$bErrors=$this->obModules->AddNotify('BANNERS_TYPE_DONT_EXISTS');
			if($_POST['OS_client_id']>0)
				if(!$this->obBanners->Client()->GetById(intval($_POST['OS_client_id'])))
					$bErrors=$this->obModules->AddNotify('BANNERS_CLIENT_DONT_EXISTS');
			if($bErrors==0)
			{
				$id=$this->obBanners->SaveBanner('OS_');
				$this->obModules->AddNotify('BANNERS_SAVE_OK','',NOTIFY_MESSAGE);
				if(!array_key_exists('update',$_REQUEST))
					$this->obUrl->Redirect("admin.php?".$this->obUrl->GetUrl(array('action','id')));
				else
					$this->obUrl->Redirect("admin.php?".$this->obUrl->GetUrl(array('action','id')).'&action=edit&id='.$id);
			}
			else
			{
				throw new CDataError('BANNERS_FIELDS_ERROR');
			}
		}
		catch(CError $e)
		{
			$arBanner=$this->GetDataFromPost();
			$this->smarty->assign('last_error',$e->__toString());
			$page=$this->EditForm($arBanner);
		}
		return $page;
	}

	/**
	 * Метод возвращает статистику по баннеру
	 */
	function GetStatistics($id)
	{
		if($arBanner=$this->obBanners->GetBanner($id))
		{
			if(isset($_GET['dateFrom'])) $dateFrom=$_GET['dateFrom']; else $dateFrom=time()-2592000;
			if(isset($_GET['dateTo'])) $dateTo=$_GET['dateTo']; else $dateTo=time();
			if($arStat=$this->obBanners->GetStatistics($id,$dateFrom,$dateTo))
			{
				$arTmp=$arStat['list'];
				if(is_array($arTmp))
				{
					$arStatistics=array();
					foreach($arTmp as $arDay)
					{
						$sDay=date('d.m.Y',$arDay['date']);
						$sHour=date('H',$arDay['date']);
						$arStatistics[$sDay][intval($sHour)]=$arDay;
					}
				}
				$data['statisticsFrom']=$arStat['date_from'];
				$data['statisticsTo']=$arStat['date_to'];
			}
			//$data['statistics']=$this->obBanners->GetStatistics($data['id']);
			$data['list']=$arStatistics;
		}
		else
		{
			$data['error']=$this->obModules->GetText('banner_not_found');
		}
		echo json_encode($data);
		die();
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
		$iCount=$this->obBanners->Banner()->Count($arFilter);
		$obPages = new CPages();
		$arSelect=array(
			'id',
			'title',
			'active',
			'date_add',
			'type_id',
			'client_id',
			'save_stats',
			$this->obBanners->Client()->sTable.'.title'=>'client_title',
			$this->obBanners->Type()->sTable.'.title'=>'type_title',
		);
		$arFilter['<?'.$this->obBanners->Banner()->sTable.'.client_id']=$this->obBanners->Client()->sTable.'.id';
		$arFilter['<?'.$this->obBanners->Banner()->sTable.'.type_id']=$this->obBanners->Type()->sTable.'.id';
		$arBanners=$this->obBanners->Banner()->GetList(array($sOrderField=>$sOrderDir),$arFilter,$obPages->GetLimits($iCount),$arSelect);
		$this->smarty->assign('ITEMS',$arBanners);
		$this->smarty->assign('pages',$obPages->GetPages($iCount));
		$this->smarty->assign('order',Array('newdir'=>$sNewDir,'curdir'=>$sOrderDir,'field'=>$sOrderField));
		return '';
	}

	/**
	 * Метод проверяет права доступа и устанавливает соответсвующие флаги
	 */
	private function CheckRights()
	{
		$iLevel=$this->obUser->GetLevel($this->module);
		if($iLevel>KS_ACCESS_BANNERS_VIEW)
			throw new CAccessError('BANNERS_ACCESS_REPLY');
		elseif($iLevel==KS_ACCESS_BANNERS_VIEW)
		{
			$this->arRights['canView']=true;
		}
		elseif($iLevel<KS_ACCESS_BANNERS_VIEW)
		{
			$this->arRights['canView']=true;
			$this->arRights['canEdit']=true;
		}
		$this->smarty->assign('rights',$this->arRights);
	}

	function Run()
	{
		//Проверка прав доступа
		$this->CheckRights();
		$action='';
		if(array_key_exists('action',$_REQUEST))
			$action=$_REQUEST['action'];
		$id=0;
		if(array_key_exists('id',$_REQUEST))
			$id=intval($_REQUEST['id']);
		$this->page='';
		if(array_key_exists('comdel',$_POST)&&$this->arRights['canEdit'])
		{
			$arElements=$_POST['sel']['elm'];
			$this->obBanners->DeleteBanners($arElements);
			$this->obModules->AddNotify('BANNERS_DELETE_OK','',NOTIFY_MESSAGE);
			$this->obUrl->Redirect("admin.php?".$this->obUrl->GetUrl(Array('action','id')));
		}
		$data=false;
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
			case "getStatistics":
				$this->GetStatistics($id);
			break;
			case "delete":
				$this->obBanners->DeleteBanners($id);
				$this->obModules->AddNotify('BANNERS_DELETE_OK','',NOTIFY_MESSAGE);
				$this->obUrl->Redirect("admin.php?".$this->obUrl->GetUrl(Array('action','id')));
			default:
				$page=$this->Table();
		}
		return $page;
	}
}
