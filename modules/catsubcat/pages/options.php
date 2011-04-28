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

require_once MODULES_DIR.'/main/libs/class.CModuleAdmin.php';
require_once MODULES_DIR.'/main/libs/class.CUserGroup.php';
require_once MODULES_DIR.'/main/libs/class.CAccess.php';
require_once MODULES_DIR.'/main/libs/class.CConfigParser.php';
require_once MODULES_DIR.'/catsubcat/libs/class.CElement.php';

class CcatsubcatAIoptions extends CModuleAdmin
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
		$obConfig=new CConfigParser($this->module);
		$obConfig->LoadConfig();
		$arUserFields=$this->obElement->GetUserFields();
		/**
		 * @todo Заменить на текстовые константы
		 */
		$arSortFields=array(
			'date_add' => 'дате создания',
			'title' => 'названию',
			'text_ident'=>'текстовому идентификатору',
			'active'=>'активности',
			'orderation'=> 'полю сортировки',
			'views_count'=>'количеству показов',
			'date_edit'=>'дате редактирования'
		);
		$arAdminSortFields=$arSortFields;
		foreach($arUserFields as $key=>$item)
		{
			$arSortFields['ext_'.$item['title']]='['.$item['description'].']';
		}

		//Получаем права на доступ к модулю
		$USERGROUP=new CUserGroup;
		$arAccess['groups']=$USERGROUP->GetList(array('title'=>'asc'));
		//Получаем список доступов для модуля
		$arAccess['module']=$this->obModules->GetAccessArray($this->module);
		$obAccess=new CModulesAccess();
		$arAccess['levels']=$obAccess->GetList(array('id'=>'asc'),array('module'=>$this->module));
		unset($arAccess['levels'][$this->module]);
		$arRes=array();
		foreach($arAccess['levels'] as $key=>$item)
		{
			$arRes[$item['group_id']]=$item;
		}
		foreach($arAccess['groups'] as $arGroup)
		{
			if(!array_key_exists($arGroup['id'],$arRes))
				$arRes[$arGroup['id']]=array(
					'id'=>'-1',
					'group_id'=>$arGroup['id'],
					'module'=>$this->module,
					'level'=>10,
				);
		}
		$arAccess['levels']=$arRes;

		if ($_POST['action']=='save')
		{
			$bad=array('\\',"'",'"',"?>","<?",".",",");
			try
			{
				$obConfig->Set('show_nav_chain',intval($_POST['sc_show_nav_chain']));
				$obConfig->Set('set_title',intval($_POST['sc_set_title']));
				$obConfig->Set('title_default',htmlentities($_POST['sc_title_default'],ENT_QUOTES,'UTF-8'));
				$obConfig->Set('sort_by',array_key_exists($_POST['sc_sort_by'],$arSortFields)?$_POST['sc_sort_by']:'id');
				$obConfig->Set('sort_dir',$_POST['sc_sort_dir']=='desc'?'desc':'asc');
				$obConfig->Set('count',intval($_POST['sc_count']));
				$obConfig->Set('admin_sort_by',array_key_exists($_POST['sc_admin_sort_by'],$arSortFields)?$_POST['sc_admin_sort_by']:'id');
				$obConfig->Set('admin_sort_dir',$_POST['sc_admin_sort_dir']=='desc'?'desc':'asc');
				$obConfig->Set('select_from_children',$_POST['sc_select_from_children']=='Y'?'Y':'N');
				$obConfig->WriteConfig();
				//Выполняем сохранение прав доступа
				if(is_array($_POST['sc_groupLevel']))
				{
					foreach($_POST['sc_groupLevel'] as $key=>$value)
					{
						//echo min($value);
						$obAccess->Set($key,$module_name,min($value));
					}
				}
				$this->obModules->AddNotify('CATSUBCAT_OPTIONS_SAVED','',NOTIFY_MESSAGE);
				CUrlParser::get_instance()->Redirect("admin.php?module=".$this->module."&page=options");
			}
			catch (EXCEPTION $e)
			{
				$smarty->assign('last_error',$e);
			}
		}
		$this->smarty->assign('sort',$arSortFields);
		$this->smarty->assign('adminSort',$arAdminSortFields);
		$this->smarty->assign('data',$obConfig->GetConfig());
		$this->smarty->assign('access',$arAccess);
		return '_options';
	}
}

