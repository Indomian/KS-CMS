<?php
/**
 * Страница модуля main редактирования шаблонов
 *
 * @filesource templates.php
 * @author blade39 <blade39@kolosstudio.ru>, north-e <pushkov@kolosstudio.ru>
 * @version 2.7
 * @since 04.05.2009
 */

if (!defined('KS_ENGINE')) die("Hacking attempt!");

require_once MODULES_DIR.'/main/libs/class.CModuleAdmin.php';
require_once MODULES_DIR.'/main/libs/class.CUserGroup.php';

class CmainAItemplates extends CModuleAdmin
{
	//Глобальные шаблоны
	private $obGlobalTpl;
	//Локальные шаблоны
	private $obTemplates;

	function __construct($module='main',&$smarty,&$parent)
	{
		parent::__construct($module,$smarty,$parent);
		if ($this->obUser->GetLevel('main') > 7)
			throw new CAccessError("MAIN_ACCESS_MANAGEMENT_TEMPLATES_CLOSED",403);
		/* Объект для работы с глобальными шаблонами */
		$this->obGlobalTpl = new CGlobalTemplates();
		/* Объект для работы с шаблонами */
		$this->obTemplates = new CTemplates;
	}

	/**
	 * Метод выполняет сохранение связей между шаблоном и адресом
	 */
	function SaveLinks()
	{
		try
		{
			if(isset($_POST['links']) && is_array($_POST['links']))
				foreach ($_POST['links'] as $id=>$arItem)
					if($id>0)
						$this->obGlobalTpl->Update($id,$arItem);
			if (isset($_POST['newlinks']) && is_array($_POST['newlinks']))
				foreach ($_POST['newlinks'] as $arItem)
					if((isset($arItem['url_path']) && $arItem['url_path']!='')||
						(isset($arItem['function1']) && $arItem['function1']!=''))
						$this->obGlobalTpl->Save("",$arItem);
			if(isset($_POST['delete']) && is_array($_POST['delete']))
				$obTpl->DeleteByIds(array_keys($_POST['delete']));
			$this->obModules->AddNotify('MAIN_TEMPLATES_LINKS_SAVED',0,NOTIFY_MESSAGE);
			$KS_URL=CUrlParser::get_instance();
			$KS_URL->Redirect($KS_URL->GetUrl());
		}
		catch (CError $e)
		{
			$this->obModules->AddNotify($e->getMessage(),$e->getCode());
			$page=$this->Table();
		}
	}

	function Table()
	{
		$arResult=$this->obTemplates->GetList();
		foreach($arResult as $value)
		{
			$arSubSchemes=$this->obTemplates->GetSchemeList($value);
			if(is_array($arSubSchemes))
				foreach($arSubSchemes as $scheme)
					$arLinks['TEMPLATES'][]=$value.':'.$scheme;
		}
	  	$arLinks['LINKS']=$this->obGlobalTpl->GetList(array('orderation'=>'asc'));
	  	$obGroups=new CUserGroup();
		$arGroups=$obGroups->GetList();
		$this->smarty->assign('groups',$arGroups);
		$this->smarty->assign('dataList',$arResult);
		$this->smarty->assign('linksList',$arLinks);
	    return '';
	}

	function Run()
	{
		$page='';
		switch($this->sAction)
		{
			case 'clearCache':
				$this->smarty->clear_all_cache();
				$this->smarty->clear_compiled_tpl();
				$page=$this->Table();
				$this->obModules->AddNotify('MAIN_CACHE_CLEAN_OK','',NOTIFY_MESSAGE);
			break;
			case 'clearPicCache':
				global $KS_FS;
				if(!$KS_FS->cleardir(UPLOADS_DIR.'/PicCache'))
					$this->obModules->AddNotify('MAIN_PICTURE_CACHE_CLEAN_FAIL');
				else
					$this->obModules->AddNotify('MAIN_PICTURE_CACHE_CLEAN_OK','',NOTIFY_MESSAGE);
				$page=$this->Table($KS_TEMPLATES);
			break;
			//@todo Переписать на использование Ajax запросов в json используя стандартный механизм
			case 'getgroups':
				$obGroups=new CUserGroup();
				$arGroups=$obGroups->GetList();
				$this->smarty->assign('groups',$arGroups);
				$this->smarty->assign('mode','groupslist');
				$this->smarty->assign('tdId',$_GET['tdId']);
				$this->smarty->assign('id',$_GET['id']);
				$result = array('tdId' => $_GET['tdId'],'id' => $_GET['id']);
				$result['html'] = $smarty->fetch('admin/main_templates_ajax.tpl');
				echo json_encode($result);
				die();
			break;
			case 'saveLinks':
				$page=$this->SaveLinks();
			break;
			default:
				$page=$this->Table();
		}
		return '_templates'.$page;
	}
}
