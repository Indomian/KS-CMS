<?php
/**
 * @file main/pages/fields.php
 * Административный интерфейс управления дополнительными полями
 * Файл проекта kolos-cms.
 *
 * Создан 2008
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CModuleAdmin.php';

class CmainAIfields extends CModuleAdmin
{
	private $obFields;
	private $arRights;

	function __construct($module='main',&$smarty,&$parent)
	{
		parent::__construct($module,$smarty,$parent);
		$this->obFields=new CFields();
	}

	/**
	 * Метод выводит форму редактирования поля
	 */
	function EditForm($data=false)
	{
		if($data)
		{
			if($this->obModules->IsActive($data['module']))
			{
				$mod=$data['module'];
				if(file_exists(MODULES_DIR.'/'.$mod.'/config.php'))
				{
					include MODULES_DIR.'/'.$mod.'/config.php';
					$varName='MODULE_'.$mod.'_db_config';
					if(isset($$varName))
					{
						$arModuleList=$$varName;
						$this->smarty->assign('tables',$arModuleList);
						$this->smarty->assign('data',$data);
						$result=$this->smarty->fetch('admin/main_fields_onmodulechange.tpl');
						$this->smarty->assign('type',$result);
					}
				}
			}
		}
		else
		{
			$data=Array('id'=>-1,'script'=>'text');
			$this->smarty->assign('type','');
		}
		//Получаем список модулей и ищем доступные
		$slist=$this->obModules->GetInstalledList();
		$list=array();
		foreach($slist as $i=>$arItem)
		{
			$arModuleList=false;
			if(file_exists(MODULES_DIR.'/'.$slist[$i]['directory'].'/config.php'))
			{
				include MODULES_DIR.'/'.$slist[$i]['directory'].'/config.php';
				$varName='MODULE_'.$slist[$i]['directory'].'_db_config';
				if(isset($$varName))
				{
					$arModuleList=$$varName;
					if(is_array($arModuleList)) $list[]=$slist[$i];
				}
			}
		}
		$this->smarty->assign('modules',$list);
		$this->smarty->assign('types',$this->obFields->GetTypes());
		$this->smarty->assign('data',$data);
		return '_edit';
	}

	/**
	 * Метод выполняет сохранение записи
	 */
	function Save()
	{
		if(!$this->obUser->IsAdmin()) throw new CAccessError('MAIN_FIELD_EDIT_DENIED');
		$this->obFields->AddCheckField('title');
		$this->obFields->AddCheckField('module');
		$this->obFields->AddCheckField('type');
		$this->obFields->AddAutoField('id');
		try
		{
			$id=$this->obFields->Save('CM_',$_POST);
			$this->obModules->AddNotify('MAIN_FIELDS_SAVE_OK','',NOTIFY_MESSAGE);
			if(!array_key_exists('update',$_REQUEST))
			{
				$this->obUrl->Redirect("/admin.php?".$this->obUrl->GetUrl(Array('ACTION','id')));
			}
			else
			{
				$this->obUrl->Redirect("/admin.php?".$this->obUrl->GetUrl('ACTION','id').'&ACTION=edit&id='.$id);
			}
		}
		catch (CError $e)
		{
			$this->smarty->assign('last_error',$e->__toString());
			$data=$this->obFields->GetRecordFromPost('CM_',$_POST);
			return $this->EditForm($data);
		}
	}

	/**
	 * Метод выполняет вывод таблицы со списком дополнительных полей
	 */
	function Table()
	{
		$obPages=new CPages(20);
		$totalUsers=$this->obFields->count();
		if($list=$this->obFields->GetList(array('id'=>'asc'),false,$obPages->GetLimits($totalUsers)))
		{
			foreach($list as $key=>$arRow)
			{
				$list[$key]['module_title']=$this->obModules->GetTitle($arRow['module']);
				$list[$key]['type_title']=$this->obModules->GetText($arRow['type']);
			}
		}
		$this->smarty->assign('list',$list);
		$this->smarty->assign('pages',$obPages->GetPages($totalUsers));
		return '';
	}

	function Run()
	{
		if ($this->obUser->GetLevel('main')>8) throw new CAccessError('MAIN_ACCESS_USER_FIELDS_CLOSED');
		$action='';
		if(array_key_exists('ACTION',$_REQUEST))
			$action=$_REQUEST['ACTION'];
		switch($action)
		{
			case "new":
				$page=$this->EditForm();
			break;
			case "edit":
				if(!isset($_REQUEST['id'])) throw new CDataError('MAIN_FIELDS_ID_REQUIRED');
				if($data=$this->obFields->GetById(intval($_REQUEST['id'])))
				{
					$page=$this->EditForm($data);
				}
				else
				{
					throw new CError('MAIN_FIELDS_NOT_FOUND');
				}
			break;
			case "delete":
				if(!isset($_REQUEST['id'])) throw new CDataError('MAIN_FIELDS_ID_REQUIRED');
				if($arField=$this->obFields->GetById(intval($_REQUEST['id'])))
				{
					$this->obFields->Delete($arField['id']);
					$this->obModules->AddNotify('MAIN_FIELDS_DELETE_OK','',NOTIFY_MESSAGE);
					$this->obUrl->Redirect("admin.php?".$this->obUrl->GetUrl(Array('ACTION','id')));
				}
				else
				{
					throw new CError('MAIN_FIELDS_NOT_FOUND');
				}
			break;
			case "onmodulechange":
				try
				{
					if(!isset($_GET['mod'])) throw new CDataError('MAIN_MODULE_CODE_REQUIRED');
					if($this->obModules->IsActive($_GET['mod']))
					{
						$mod=$_GET['mod'];
						if(file_exists(MODULES_DIR.'/'.$mod.'/config.php'))
						{
							include MODULES_DIR.'/'.$mod.'/config.php';
							$varName='MODULE_'.$mod.'_db_config';
							if(isset($$varName))
							{
								$arModuleList=$$varName;
								$this->smarty->assign('tables',$arModuleList);
								$result=$this->smarty->fetch('admin/main_fields_onmodulechange.tpl');
								echo $result;
								die();
							}
							throw new CDataError('MAIN_FIELD_MODULE_NO_TABLES');
						}
						throw new CDataError('MAIN_FIELD_MODULE_NO_TABLES');
					}
					throw new CDataError('MAIN_FIELD_MODULE_INACTIVE');
				}
				catch(CError $e)
				{
					echo $e->__toString();
					die();
				}
				die();
			break;
			case "onfieldchange":
				if(isset($_GET['field']))
				{
					$data=array('script'=>$_GET['field']);
					$this->smarty->assign('data',$data);
					$result=$this->smarty->fetch('admin/main_fields_onfieldchange.tpl');
					echo $result;
					die();
				}
				die();
			break;
			case "save":
				$page=$this->Save();
			break;
			default:
				$page=$this->Table();
		}
		return '_fields'.$page;
	}
}

