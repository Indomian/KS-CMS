<?php

/**
 * Страница управления активностью модулей
 *
 * @filesource modules.php
 * @author BlaDe39 <blade39@kolosstudio.ru>, North-E <pushkov@kolosstudio.ru>
 * @version 1.1
 * @since 21.04.2009
 */

if (!defined('KS_ENGINE'))
	die("Hacking attempt!");
global $KS_URL;
/* Проверка прав доступа */
if ($USER->GetLevel('main') > 0)
	throw new CAccessError("MAIN_ACCESS_SITE_PREFERENCES_CLOSED");

/* Проверка, что пользователь является администратором */
if ($USER->is_admin())
{
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
			$this->Update($in, $update_field);
			$this->RecountDBStructure();
			$this->RecountTextStructure();
		}
	}

	$arActivate=array('active'=>0);

	/* Определяем запрошенное действие и выбираем соответствующий шаблон для Смарти */
	switch($_REQUEST['CM_ACTION'])
	{
		case "activate":
			$arActivate=array('active'=>intval($_REQUEST['ac']==1)?0:1);
			$this->Update(intval($_REQUEST['CM_id']), $arActivate);
			$this->RecountDBStructure();
			$this->RecountTextStructure();
			CUrlParser::Redirect("admin.php?".$KS_URL->GetUrl(Array('CM_ACTION','CM_id','ac')));
		break;
		case "edit":
		    $data=$this->GetRecord(array('id'=>$_REQUEST['CM_id']));
		    $smarty->assign('SITE', array('home_url' => "http://" . $_SERVER['HTTP_HOST']));
		    $smarty->assign('data',$data);
		    $page='_modules_edit';
		break;
		case "save":
			if ($USER->is_admin())
			{
				try
				{
					if($_POST['CM_URL_ident']=='default')
					{
						$arModules=$this->GetList(array('id'=>'asc'),array('URL_ident'=>'default','!id'=>intval($_POST['CM_id'])));
						if(is_array($arModules))
						{
							throw new CError("MAIN_NOT_MAKE_TWO_MODULES_DEFAULT");
						}
					}
					$data=$this->GetRecord(array('id'=>$_REQUEST['CM_id']));
					if(!is_array($data)) throw new CDataError("MAIN_SYSTEM_ERROR_PROCESSING_MODULE");
					if($data['active']!=$_POST['CM_active'])
					{
						if($_POST['CM_active']==1)
						{
							if(file_exists(MODULES_DIR.'/'.$data['directory'].'/events/onBeforeActivate.php'))
								include MODULES_DIR.'/'.$data['directory'].'/events/onBeforeActivate.php';
						}
						else
						{
							if(file_exists(MODULES_DIR.'/'.$data['directory'].'/events/onBeforeDeactivate.php'))
								include MODULES_DIR.'/'.$data['directory'].'/events/onBeforeDeactivate.php';
						}
					}
					$id = $this->Save('CM_');
					if($data['active']!=$_POST['CM_active'])
					{
						if($_POST['CM_active']==1)
						{
							if(file_exists(MODULES_DIR.'/'.$data['directory'].'/events/onAfterActivate.php'))
								include MODULES_DIR.'/'.$data['directory'].'/events/onAfterActivate.php';
						}
						else
						{
							if(file_exists(MODULES_DIR.'/'.$data['directory'].'/events/onAfterDeactivate.php'))
								include MODULES_DIR.'/'.$data['directory'].'/events/onAfterDeactivate.php';
						}
					}
					if(!array_key_exists('update',$_REQUEST))
				    {
		    			CUrlParser::Redirect("admin.php?".$KS_URL->GetUrl(Array('ACTION','CM_id')));
		    		}
		    		else
		    		{
		    			CUrlParser::Redirect("admin.php?".$KS_URL->GetUrl('ACTION','CM_id').'&ACTION=edit&CM_id='.$id);
		    		}
		    	}
	   			catch (CError $e)
	   			{
	   				$smarty->assign('last_error',$e);
	   				$showList=false;
	   				$data=$this->GetRecord(array('id'=>$_REQUEST['CM_id']));
	   				//$data=array_merge($this->GetRecordFromPost('CM_',$_POST));
	   				//print_r($data);
					$smarty->assign('data',$data);
					$page='_modules_edit';
	   			}
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
				if($this->IsInstallable($sModuleName))
				{
					$showButtons=$this->Install($sModuleName);
					$smarty->assign('canInstall',$showButtons);
				}
			}
			catch(CModuleError $e)
			{
				$smarty->assign('last_error',$e);
				$smarty->assign('canInstall',0);
			}
			catch(CError $e)
			{
				$smarty->assign('last_error',$e);
				$smarty->assign('canInstall',0);
			}
			$smarty->assign('module_name',$sModuleName);
			$page='_modules_install';
		break;
		case "uninstall":
			try
			{
				$sModuleName=$_GET['mod'];
				$showButtons=$this->UnInstall($sModuleName);
				$smarty->assign('canInstall',$showButtons);
			}
			catch(CModuleError $e)
			{
				$smarty->assign('last_error',$e);
			}
			catch(CError $e)
			{
				$smarty->assign('last_error',$e);
			}
			$smarty->assign('module_name',$sModuleName);
			$page='_modules_uninstall';
		break;
		case "def":
		{
			if($USER->is_admin())
			{
				$arModules=$this->GetList(array('id'=>'asc'),array('URL_ident'=>'default'));
				if(is_array($arModules))
				{
					$this->Update($arModules[0]['id'],array('URL_ident'=>$arModules[0]['directory']));
				}
				$this->Update(intval($_REQUEST['CM_id']),array('URL_ident'=>'default'));
			}
		}
		default:
			$list=$this->GetInstalledList();
			$ulist=$this->GetUninstalledList();
			$smarty->assign('list',$list);
			$smarty->assign('ulist',$ulist);
			$page='_modules';
	}
}
else
{
	$page='';
}