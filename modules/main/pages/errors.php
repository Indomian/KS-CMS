<?php

/**
 * Страница модуля main редактирования текстовых констант ошибок
 * 
 * @filesource errors.php
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.5.2
 * @since 04.05.2009
 */

if (!defined('KS_ENGINE'))
	die("Hacking attempt!");

include_once MODULES_DIR . '/main/libs/class.CErrorsParser.php';
include_once MODULES_DIR . '/main/libs/class.CModuleAdmin.php';

global $KS_URL;

if ($USER->GetLevel('main') > 7) throw new CAccessError("MAIN_ACCESS_ERRORS_CLOSED",403);

class CErrorsAI extends CModuleAdmin
{
	private $obEdit;
	
	function __construct($module='main',&$smarty,&$parent)
	{
		parent::__construct($module,$smarty,$parent);
		$this->obEdit=new CErrorsParser('error.conf');	
	}
	
	/**
	 * Метод выполняет реализацию операции сохранения данных
	 */
	function Save($id)
	{
		global $KS_URL,$USER;
		try
		{
			if(preg_match('#^[a-z0-9]+$#i',$_POST['locale']))
			{
				$this->obEdit->LoadLocale($_POST['locale']);
				$this->obEdit->Save($_POST['locale'],$_POST);
				CUrlParser::Redirect("admin.php?module=main&modpage=errors");
			}
			else
			{
				throw new CError('MAIN_ERROR_LOCALE_REQUIRED');
			}
		}
		catch(CError $e)
		{
			$this->smarty->assign('last_error',$e);
			$page=$this->Table();
		}
		return $page;
	}
	
	function Table()
	{
		$arSortFields=Array('text_ident','ru');
		// Обработка порядка вывода элементов
		list($sOrderField,$sOrderDir)=$this->InitSort($arSortFields,$_REQUEST['order'],$_REQUEST['dir']);
		$sNewDir=($sOrderDir=='desc')?'asc':'desc';
		// Фильтр элементов
		$arFilter=array();
		if (class_exists('CFilterFrame'))
		{
			$obFilter=new CFilterFrame();
			$obFilter->AddField(array('FIELD'=>'text_ident','METHOD'=>'~'));
			$obFilter->AddField(array('FIELD'=>'ru','METHOD'=>'~'));
			$obFilter->AddField(array('FIELD'=>'locale','METHOD'=>'=','DEFAULT'=>$_SESSION['main']['errors']['locale']));
			$arFilter=$obFilter->GetFilter();
			$obFilter->SetSmartyFilter('filter');
			$arTitles=array(
				'text_ident'=>'Код ошибки',
				'ru'=>'Текст ошибки',
				'locale'=>'Локаль',
			);
			$this->smarty->assign('ftitles',$arTitles);
		}
		if($arFilter['locale']!='')
		{
			$this->obEdit->LoadLocale($arFilter['locale']);
			$_SESSION['main']['errors']['locale']=$arFilter['locale'];
		}
		unset($arFilter['locale']);
		$iCount=$this->obEdit->Count($arFilter);
		$obPages = new CPageNavigation($this->obEdit,$iCount);
		$arList=$this->obEdit->GetList(array($sOrderField=>$sOrderDir),$arFilter,$obPages->GetLimits($iCount));
		$this->smarty->assign('ITEMS',$arList);
		$this->smarty->assign('fields',$this->obEdit->arFields);
		$this->smarty->assign('locale',$_SESSION['main']['errors']['locale']);
		$this->smarty->assign('pages',$obPages->GetPages($iCount));
		$this->smarty->assign('order',Array('newdir'=>$sNewDir,'curdir'=>$sOrderDir,'field'=>$sOrderField));
		return '_errors';
	}
	
	function Run()
	{
		global $USER,$KS_URL;
		//Проверка прав доступа
		if($USER->GetLevel('main')>7) throw new CAccessError('MAIN_ACCESS_ERRORS_CLOSED');

		$action=$_REQUEST['action'];
		$id=intval($_REQUEST['id']);
		$page='_errors';
		switch($action)
		{
			case "save":
				$page=$this->Save($id);
			break;
			default:
				$page=$this->Table();
		}
		return $page;
	}
}

$obInterface=new CErrorsAI('main',$smarty,$this);
$page=$obInterface->Run();
?>
