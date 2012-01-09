<?php
/**
 * @file main/pages/events
 * Административный интерфейс управления отправленными сообщениями
 * Файл проекта kolos-cms.
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */

if( !defined('KS_ENGINE') )die("Hacking attempt!");

include_once MODULES_DIR.'/main/libs/class.CEvents.php';
include_once MODULES_DIR.'/main/libs/class.CEventTemplates.php';
require_once MODULES_DIR.'/main/libs/class.CModuleAdmin.php';

class CmainAIevents extends CModuleAdmin
{
	private $obEvents;

	function __construct($module='main',&$smarty,&$parent)
	{
		parent::__construct($module,$smarty,$parent);
		if($this->obUser->GetLevel('main')>6)
			throw new CAccessError("MAIN_NOT_RIGHTS_MANAGE_MAIL");
		$this->obEvents=new CEvents();
	}

	function NewEvent()
	{
		$data=array('id'=>'-1');
		$obETemplates=new _CEventTemplates();
		$data['templates']=$obETemplates->GetList(array('id'=>'asc'),false);
		$this->smarty->assign('data',$data);
		return '_edit';
	}

	function Table()
	{
		$arSortFields=$this->obEvents->GetFields();
		// Обработка порядка вывода элементов
		list($sOrderField,$sOrderDir)=$this->InitSort($arSortFields);
		$sNewDir=($sOrderDir=='desc')?'asc':'desc';

		/*Обрабатываем входные данные (постраничный вывод)*/
		$obPages=$this->InitPages();
		$totalEvents=$this->obEvents->count();
		if($data=$this->obEvents->GetList(array($sOrderField=>$sOrderDir),false,$obPages->GetLimits($totalEvents)))
			foreach($data as $key => $message)
				$data[$key]['content'] = nl2br(trim($data[$key]['content'],"\x00..\x1F"));
		$this->smarty->assign('data',$data);
		$this->smarty->assign('pages',$obPages->GetPages($totalEvents));
		$this->smarty->assign('order',Array('newdir'=>$sNewDir,'curdir'=>$sOrderDir,'field'=>$sOrderField));
		return '';
	}

	function Run()
	{
		$this->ParseAction();
		$id=0;
		$page='';
		if(isset($_REQUEST['id']) && intval($_REQUEST['id'])>0)
			$id=intval($_REQUEST['id']);
		try
		{
			switch ($this->sAction)
			{
				case 'new':
					$page=$this->NewEvent();
				break;
				case 'delete':
					if($id>0)
					{
						$this->obEvents->Delete($id);
						$this->obModules->AddNotify('MAIN_EVENTS_DELETE_OK','',NOTIFY_MESSAGE);
						$this->obUrl->redirect('/admin.php?module=main&modpage=events');
					}
					else
						throw new CError('MAIN_EVENTS_DELETE_ERROR');
				break;
				case 'save':
					///@todo Такого метода нет в классе, выяснить что за операция и удалить по необходимости
					$this->obEvents->SaveTemplate();
				break;
				case 'activate':
					if($id>0)
					{
						if($this->obEvents->Activate($id))
						{
							$this->obModules->AddNotify('MAIN_EVENTS_MESSAGE_ACTIVATE_OK','',NOTIFY_MESSAGE);
							$this->obUrl->redirect('/admin.php?module=main&modpage=events');
						}
						else
							throw new CError('MAIN_EVENTS_MESSAGE_ACTIVATE_ERROR');
					}
					else
						throw new CError('MAIN_EVENTS_MESSAGE_ACTIVATE_ERROR');
				break;
				case 'tpl_selected':
					if(isset($_REQUEST['tpl']) && $_REQUEST['tpl']!='' && IsTextIdent($_REQUEST['tpl']))
					{
						$obETemplates=new CEventTemplates();
						if($data=$obETemplates->GetTemplateVarNames($_REQUEST['tpl']))
							echo json_encode(array('tpl_fields'=>$data, 'error'=>'no'));
						else
							echo json_encode(array('error'=>'yes'));
					}
					else
						echo json_encode(array('error'=>'yes'));
					die;
				break;
				case 'common':
					if(array_key_exists('comdel', $_REQUEST) && isset($_REQUEST['sel']['elm']) && is_array($_REQUEST['sel']['elm']))
					{
						$arElements = $_REQUEST['sel']['elm'];
						foreach($arElements as $iId)
							$this->obEvents->Delete($iId);
						$this->obModules->AddNotify('MAIN_EVENTS_DELETE_OK','',NOTIFY_MESSAGE);
						$this->obUrl->redirect('/admin.php?module=main&modpage=events');
					}
					elseif(array_key_exists('comact', $_REQUEST) && isset($_REQUEST['sel']['elm']) && is_array($_REQUEST['sel']['elm']))
					{
						$arElements = $_REQUEST['sel']['elm'];
						foreach($arElements as $iId)
							$this->obEvents->Activate($iId);
						$this->obModules->AddNotify('MAIN_EVENTS_MESSAGE_ACTIVATE_OK','',NOTIFY_MESSAGE);
						$this->obUrl->redirect('/admin.php?module=main&modpage=events');
					}
				break;
				default:
					$page=$this->Table();
			}
		}
		catch(CError $e)
		{
			$this->smarty->assign('last_error',$e->__toString());
			$page=$this->Table();
		}

		return '_events'.$page;
	}
}
