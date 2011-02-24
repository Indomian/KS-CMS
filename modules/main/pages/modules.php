<?php
/**
 * @file main/pages/modules.php
 * Файл управления модулями системы
 * Файл проекта kolos-cms.
 *
 * Изменен 22.02.2011
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CModuleAdmin.php';

class CmainAImodules extends CModuleAdmin
{
	function __construct($module='main',&$smarty,&$parent)
	{
		global $USER;
		parent::__construct($module,$smarty,$parent);
		$this->obUser=$USER;
	}

	function Run()
	{
		global $KS_URL;
		/* Проверка прав доступа */
		if ($this->obUser->GetLevel('main') > 0) throw new CAccessError("MAIN_ACCESS_SITE_PREFERENCES_CLOSED");

		/* Проверка, что пользователь является администратором */
		if (!$this->obUser->is_admin()) throw new CAccessError("MAIN_ACCESS_SITE_PREFERENCES_CLOSED");

		/* Проверяем, не нужно ли выполнить общее действие над выбранными модулями */
		if(array_key_exists('ACTION',$_POST)&&($_POST['ACTION']=='common'))
		{
			$selected = $_POST['sel'];
			$in = array();
			foreach ($selected as $key => $item)
				if (preg_match("#^(\d)+$#", $item))
					$in[] = $item;
			if (count($in)>0)
			{
				if (array_key_exists('comact',$_POST))
					$update_field = array('active' => '1');
				else
					$update_field = array('active' => '0');
				$this->obModules->Update($in, $update_field);
				$this->obModules->RecountDBStructure();
				$this->obModules->RecountTextStructure();
			}
		}

		$arActivate=array('active'=>0);

		/* Определяем запрошенное действие и выбираем соответствующий шаблон для Смарти */
		switch($_REQUEST['CM_ACTION'])
		{
			case "activate":
				$arActivate=array('active'=>intval($_REQUEST['ac']==1)?0:1);
				$this->obModules->Update(intval($_REQUEST['CM_id']), $arActivate);
				$this->obModules->RecountDBStructure();
				$this->obModules->RecountTextStructure();
				CUrlParser::Redirect("admin.php?".$KS_URL->GetUrl(Array('CM_ACTION','CM_id','ac')));
			break;
			case "edit":
				$data=$this->obModules->GetRecord(array('id'=>$_REQUEST['CM_id']));
				$this->smarty->assign('SITE', array('home_url' => "http://" . $_SERVER['HTTP_HOST']));
				$this->smarty->assign('data',$data);
				$page='_modules_edit';
			break;
			case "save":
				if ($this->obUser->is_admin())
				{
					try
					{
						if($_POST['CM_URL_ident']=='default')
						{
							$arModules=$this->obModules->GetList(array('id'=>'asc'),array('URL_ident'=>'default','!id'=>intval($_POST['CM_id'])));
							if(is_array($arModules))
							{
								throw new CError("MAIN_NOT_MAKE_TWO_MODULES_DEFAULT");
							}
						}
						if($data=$this->obModules->GetRecord(array('id'=>$_REQUEST['CM_id'])))
						{
							$id = $this->Save('CM_');
							if(!array_key_exists('update',$_REQUEST))
							{
								CUrlParser::Redirect("admin.php?".$KS_URL->GetUrl(Array('ACTION','CM_id')));
							}
							else
							{
								CUrlParser::Redirect("admin.php?".$KS_URL->GetUrl('ACTION','CM_id').'&ACTION=edit&CM_id='.$id);
							}
						}
						else
						{
							throw new CDataError("MAIN_SYSTEM_ERROR_PROCESSING_MODULE");
						}

					}
					catch (CError $e)
					{
						$this->smarty->assign('last_error',$e->__toString());
						$showList=false;
						$data=$this->GetRecord(array('id'=>$_REQUEST['CM_id']));
						//$data=array_merge($this->GetRecordFromPost('CM_',$_POST));
						//print_r($data);
						$this->smarty->assign('data',$data);
						$page='_modules_edit';
					}
				}
				else
				{
					throw new CAccessError("MAIN_ACCESS_SITE_PREFERENCES_CLOSED");
				}
			break;
			case "install":
				$sModuleName='';
				foreach($_POST as $key=>$value)
				{
					if(preg_match('#install_([a-z0-9_\-]+)#i',$key,$matches))
					{
						$sModuleName=$matches[1];
						break;
					}
				}
				try
				{
					if($this->obModules->IsInstallable($sModuleName))
					{
						$showButtons=$this->obModules->Install($sModuleName);
						$this->smarty->assign('canInstall',$showButtons);
					}
				}
				catch(CError $e)
				{
					$this->smarty->assign('last_error',$e);
					$this->smarty->assign('canInstall',0);
				}
				$this->smarty->assign('module_name',$sModuleName);
				$page='_modules_install';
			break;
			case "uninstall":
				try
				{
					$sModuleName=$_GET['mod'];
					$showButtons=$this->obModules->UnInstall($sModuleName);
					$this->smarty->assign('canInstall',$showButtons);
				}
				catch(CError $e)
				{
					$this->smarty->assign('last_error',$e);
				}
				$this->smarty->assign('module_name',$sModuleName);
				$page='_modules_uninstall';
			break;
			case "def":
				if($USER->is_admin())
				{
					if($arModule=$this->GetRecord(array('URL_ident'=>'default')))
					{
						$this->obModules->Update($arModule['id'],array('URL_ident'=>$arModule['directory']));
					}
					$this->obModules->Update(intval($_REQUEST['CM_id']),array('URL_ident'=>'default'));
					CUrlParser::Redirect('/admin.php?module=main&modpage=modules');
				}
				else
				{
					throw new CAccessError("MAIN_ACCESS_SITE_PREFERENCES_CLOSED");
				}
			break;
			default:
				$list=$this->obModules->GetInstalledList();
				$ulist=$this->obModules->GetUninstalledList();
				$this->smarty->assign('list',$list);
				$this->smarty->assign('ulist',$ulist);
				$page='_modules';
		}
		return $page;
	}
}