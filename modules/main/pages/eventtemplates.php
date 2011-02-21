<?php
/**
 * @file main/pages/eventtemplates.php
 * Файл управления шаблонами сообщений системы
 * Файл проекта kolos-cms.
 *
 * Изменен 18.01.2011
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.5.5
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CModuleAdmin.php';
include_once MODULES_DIR.'/main/libs/class.CEventTemplates.php';

class CmainAIeventtemplates extends CModuleAdmin
{
	private $obTemplates;

	function __construct($module='main',&$smarty,&$parent)
	{
		global $USER;
		parent::__construct($module,$smarty,$parent);
		$this->obUser=$USER;
		$this->obTemplates=new CEventTemplates();
	}

	function Table()
	{
		$ob=new _CEventTemplates();
		/*Обрабатываем входные данные (постраничный вывод)*/
		$obPages=new CPageNavigation($ob);
		$totalUsers=$ob->count();
		$data=$ob->GetList(array('id'=>'asc'),false,$obPages->GetLimits($totalUsers));
		$this->smarty->assign('pages',$obPages->GetPages($totalUsers));
		$this->smarty->assign('data',$data);
		return '';
	}

	function Run()
	{
		if($this->obUser->GetLevel('main')>6) throw new CAccessError("MAIN_NOT_RIGHTS_MANAGE_MAIL_TEMPLATES");
		$action=$_REQUEST['ACTION'];
		try
		{
			switch ($action)
			{
				case 'edit':
					$data=$this->obTemplates->GetTemplate($_GET['id']);
					$this->smarty->assign('data',$data);
					$page='_edit';
				break;
				case 'new':
					$data=array('id'=>'-1','file_id'=>'');
					$this->smarty->assign('data',$data);
					$page='_edit';
				break;
					case 'delete':
					$this->obTemplates->Delete($_GET['id']);
					$this->obModules->AddNotify('MAIN_EVENTS_DELETE_DONE','',NOTIFY_MESSAGE);
					CUrlParser::Redirect('/admin.php?module=main&modpage=eventtemplates');
				break;
				case 'save':
					$this->obTemplates->SaveTemplate();
					$this->obModules->AddNotify('MAIN_EVENTS_SAVE_DONE','',NOTIFY_MESSAGE);
					CUrlParser::Redirect('/admin.php?module=main&modpage=eventtemplates');
				break;
				default:
					$page=$this->Table();
			}
		}
		catch(CError $e)
		{
			$this->smarty->assign('last_error',$e);
		}
		return '_etemplates'.$page;
	}
}


