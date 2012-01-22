<?php
/**
 * @file catsubcat/pages/options.php
 * Файл обработки настроек модуля catsubcat
 * Файл проекта kolos-cms.
 *
 * Изменен 14.01.2011
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CModuleOptions.php';
require_once MODULES_DIR.'/main/libs/class.CUserGroup.php';
require_once MODULES_DIR.'/main/libs/class.CAccess.php';
require_once MODULES_DIR.'/main/libs/class.CConfigParser.php';
require_once MODULES_DIR.'/catsubcat/libs/class.CElement.php';

class CcatsubcatAIoptions extends CModuleOptions
{
	private $obElement;

	function __construct($module='catsubcat',&$smarty,&$parent)
	{
		parent::__construct($module,$smarty,$parent);
		$this->obElement=new CElement();
	}

	function Run()
	{
		if($this->obUser->GetLevel($this->module)>0) throw new CAccessError('SYSTEM_NOT_ACCESS_SETTINGS');
		$arUserFields=$this->obElement->GetUserFields();
		$arSortFields=array(
			'date_add'=>$this->obModules->GetText('sort_date_add'),//дате создания',
			'title'=>$this->obModules->GetText('sort_title'),//названию',
			'text_ident'=>$this->obModules->GetText('sort_text_ident'),//текстовому идентификатору',
			'active'=>$this->obModules->GetText('sort_active'),//активности',
			'orderation'=>$this->obModules->GetText('sort_orderation'),//полю сортировки',
			'views_count'=>$this->obModules->GetText('sort_view_count'),//количеству показов',
			'date_edit'=>$this->obModules->GetText('sort_date_edit'),//дате редактирования'
		);
		$arAdminSortFields=$arSortFields;
		foreach($arUserFields as $key=>$item)
			$arSortFields['ext_'.$item['title']]='['.$item['description'].']';
		$arAccess=$this->GetAccessLevels();


		if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['action']) && $_POST['action']=='save')
		{
			$bad=array('\\',"'",'"',"?>","<?",".",",");
			try
			{
				$this->obConfig->Set('show_nav_chain',intval($_POST['sc_show_nav_chain']));
				$this->obConfig->Set('set_title',intval($_POST['sc_set_title']));
				$this->obConfig->Set('title_default',htmlentities($_POST['sc_title_default'],ENT_QUOTES,'UTF-8'));
				$this->obConfig->Set('sort_by',array_key_exists($_POST['sc_sort_by'],$arSortFields)?$_POST['sc_sort_by']:'id');
				$this->obConfig->Set('sort_dir',$_POST['sc_sort_dir']=='desc'?'desc':'asc');
				$this->obConfig->Set('count',intval($_POST['sc_count']));
				$this->obConfig->Set('admin_sort_by',array_key_exists($_POST['sc_admin_sort_by'],$arSortFields)?$_POST['sc_admin_sort_by']:'id');
				$this->obConfig->Set('admin_sort_dir',$_POST['sc_admin_sort_dir']=='desc'?'desc':'asc');
				$this->obConfig->Set('select_from_children',$_POST['sc_select_from_children']=='Y'?'Y':'N');
				$this->obConfig->WriteConfig();
				$this->SaveAccessLevels();
				$this->obModules->AddNotify('CATSUBCAT_OPTIONS_SAVED','',NOTIFY_MESSAGE);
				$this->obUrl->Redirect("admin.php?module=".$this->module."&page=options");
			}
			catch (EXCEPTION $e)
			{
				$smarty->assign('last_error',$e);
			}
		}
		$this->smarty->assign('sort',$arSortFields);
		$this->smarty->assign('adminSort',$arAdminSortFields);
		$this->smarty->assign('data',$this->obConfig->GetConfig());
		$this->smarty->assign('access',$arAccess);
		return '_options';
	}
}

