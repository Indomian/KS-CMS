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

	function GetTemplatesFiles()
	{
		global $KS_FS;
		if($arModules=$this->obModules->GetList(false,array('active'=>1)))
		{
			$arRealTemplates=array();
			foreach($arModules as $arModule)
			{
				if(file_exists(MODULES_DIR.'/'.$arModule['directory'].'/install/templates/events') && is_dir(MODULES_DIR.'/'.$arModule['directory'].'/install/templates/events'))
				{
					$arTemplates=$KS_FS->GetDirList(MODULES_DIR.'/'.$arModule['directory'].'/install/templates/events');
					foreach($arTemplates as $sFile)
					{
						if(!preg_match('#~$#',$sFile))
						$arRealTemplates[str_replace(MODULES_DIR.'/'.$arModule['directory'].'/install/templates/events/','',$sFile)]=$arModule['directory'];
					}
				}
			}
			return $arRealTemplates;
		}
		return false;
	}

	function Table()
	{
		$ob=new _CEventTemplates();
		$arRealTemplates=array();
		if($arRealTemplates=$this->GetTemplatesFiles())
		{
			$arRealTemplates=array_keys($arRealTemplates);
		}
		$obPages=new CPageNavigation($ob);
		$totalUsers=$ob->count();
		if($data=$ob->GetList(array('id'=>'asc'),false,$obPages->GetLimits($totalUsers)))
		{
			$arFiles=array();
			foreach($data as &$arItem)
			{
				if(!in_array($arItem['file_id'],$arRealTemplates)) $arItem['deleted']=1;
				$arFiles[]=$arItem['file_id'];
			}
			$arNotInstalledFiles=array_diff($arRealTemplates,$arFiles);
			foreach($arNotInstalledFiles as $sFile)
			{
				$data[]=array(
					'id'=>'-1',
					'file_id'=>$sFile,
					'title'=>'',
					'new'=>1
				);
			}
		}
		$this->smarty->assign('pages',$obPages->GetPages($totalUsers));
		$this->smarty->assign('data',$data);
		return '';
	}

	function Run()
	{
		global $KS_FS;
		if($this->obUser->GetLevel('main')>6) throw new CAccessError("MAIN_NOT_RIGHTS_MANAGE_MAIL_TEMPLATES");
		$action='';
		if(isset($_REQUEST['ACTION']))
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
					if($arRealTemplates=$this->GetTemplatesFiles())
					{
						if(array_key_exists($_GET['id'],$arRealTemplates))
						{
							$obTpl=new _CEventTemplates();
							$arNewTemplate=array(
								'file_id'=>$_GET['id'],
								'title'=>$_GET['id']
							);
							$obTpl->Save('',$arNewTemplate);
							if(!file_exists(SYS_TEMPLATES_DIR.'/admin/eventTemplates/'))
								$KS_FS->makedir(SYS_TEMPLATES_DIR.'/admin/eventTemplates/');
							$KS_FS->CopyFile(MODULES_DIR.'/'.$arRealTemplates[$_GET['id']].'/install/templates/events/'.$_GET['id'],SYS_TEMPLATES_DIR.'/admin/eventTemplates/'.$_GET['id'],'');
							$this->obModules->AddNotify('MAIN_EVENTS_ADD_OK','',NOTIFY_MESSAGE);
						}
					}
					$page=$this->Table();
				break;
				case 'delete':
					$this->obTemplates->Delete($_GET['id']);
					$this->obModules->AddNotify('MAIN_EVENTS_DELETE_DONE','',NOTIFY_MESSAGE);
					CUrlParser::get_instance()->Redirect('/admin.php?module=main&modpage=eventtemplates');
				break;
				case 'save':
					$this->obTemplates->SaveTemplate();
					$this->obModules->AddNotify('MAIN_EVENTS_SAVE_DONE','',NOTIFY_MESSAGE);
					CUrlParser::get_instance()->Redirect('/admin.php?module=main&modpage=eventtemplates');
				break;
				default:
					$page=$this->Table();
			}
		}
		catch(CError $e)
		{
			$this->smarty->assign('last_error',$e);
			$page=$this->Table();
		}
		return '_etemplates'.$page;
	}
}


