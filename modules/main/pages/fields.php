<?php

/*KS Engine ADMIN SYSTEM

File: modules.php
Original Code by BlaDe39 (c) 2008
Назначение: управление модулями*/

if( !defined('KS_ENGINE') )
{
  die("Hacking attempt!");
}

include_once MODULES_DIR.'/main/libs/class.CUrlParser.php';
global $KS_URL,$ks_db;

if ($USER->GetLevel('main')<=8)
{
	$obFields=new CFields();
	//Проверка на структуру таблицы и обновление в случае необходимости
	$arFields=$obFields->GetTableFields();
	if(!array_key_exists('option_1',$arFields)) $ks_db->AddColumn($obFields->sTable,'option_1');
	if(!array_key_exists('option_2',$arFields)) $ks_db->AddColumn($obFields->sTable,array('title'=>'option_2','type'=>'text'));
	switch($_REQUEST['ACTION'])
	{
		case "new":
		    $data=Array('id'=>-1,'script'=>'text');
		    $smarty->assign('data',$data);
		    //Получаем список модулей и ищем доступные
		    $slist=$KS_MODULES->GetInstalledList();
			$list=array();
			foreach($slist as $i=>$arItem)
			{
				$arModuleList=false;
				if(file_exists(MODULES_DIR.'/'.$slist[$i]['directory'].'/config.php'))
				{
					include MODULES_DIR.'/'.$slist[$i]['directory'].'/config.php';
					$varName='MODULE_'.$slist[$i]['directory'].'_db_config';
					$arModuleList=$$varName;
					if(is_array($arModuleList)) $list[]=$slist[$i];
		    	}
		    }
		    $smarty->assign('type','');
		    $smarty->assign('modules',$list);
		    $smarty->assign('types',$obFields->GetTypes());
		    $page='_fields_edit';
		break;
		case "edit":
		    $data=$obFields->GetRecord(Array('id'=>$_REQUEST['id']));
		       //Получаем список модулей и ищем доступные
		    $slist=$KS_MODULES->GetInstalledList();
		    $list=array();
		    $smarty->assign('data',$data);
			foreach($slist as $i=>$arItem)
		    {
		    	$arModuleList=false;
		    	if(file_exists(MODULES_DIR.'/'.$slist[$i]['directory'].'/config.php'))
		    	{
		    		include MODULES_DIR.'/'.$slist[$i]['directory'].'/config.php';
					$varName='MODULE_'.$slist[$i]['directory'].'_db_config';
					$arModuleList=$$varName;
					if(is_array($arModuleList)) $list[]=$slist[$i];
		    	}
		    }
		    if($KS_MODULES->IsActive($data['module']))
			{
				$mod=$data['module'];
				if(file_exists(MODULES_DIR.'/'.$mod.'/config.php'))
				{
					include MODULES_DIR.'/'.$mod.'/config.php';
					$varName='MODULE_'.$mod.'_db_config';
					$arModuleList=$$varName;
					$smarty->assign('tables',$arModuleList);
					$result=$smarty->fetch('admin/main_fields_onmodulechange.tpl');
					$smarty->assign('type',$result);
				}
			}
			$smarty->assign('modules',$list);
		    $smarty->assign('types',$obFields->GetTypes());
		    $page='_fields_edit';
		break;
		case "delete":
			$obFields->Delete($_REQUEST['id']);
			$KS_URL->Redirect("admin.php?".$KS_URL->GetUrl(Array('ACTION','id')));
		break;
		case "onmodulechange":
			if($KS_MODULES->IsActive($_GET['mod']))
			{
				$mod=$_GET['mod'];
				if(file_exists(MODULES_DIR.'/'.$mod.'/config.php'))
				{
					include MODULES_DIR.'/'.$mod.'/config.php';
					$varName='MODULE_'.$mod.'_db_config';
					$arModuleList=$$varName;
					$smarty->assign('tables',$arModuleList);
					$result=$smarty->fetch('admin/main_fields_onmodulechange.tpl');
					echo $result;
					die();
				}
			}
			echo $_GET['mod'];
			die();
		break;
		case "onfieldchange":
			$data=array('script'=>$_GET['field']);
			$smarty->assign('data',$data);
			$result=$smarty->fetch('admin/main_fields_onfieldchange.tpl');
			echo $result;
			die();
		break;
		case "save":
			if ($USER->is_admin())
			{
 				$obFields->AddCheckField('title');
 				$obFields->AddCheckField('module');
 				$obFields->AddCheckField('type');
				$obFields->AddAutoField('id');
				try
				{
					$id=$obFields->Save('CM_',$_POST);
					$arField=$obFields->GetRecord(array('id'=>$id));
					if(!array_key_exists('update',$_REQUEST))
				    {
		    			CUrlParser::get_instance()->Redirect("/admin.php?".$KS_URL->GetUrl(Array('ACTION','id')));
		    		}
		    		else
		    		{
		    			CUrlParser::get_instance()->Redirect("/admin.php?".$KS_URL->GetUrl('ACTION','id').'&ACTION=edit&id='.$id);
		    		}
				}
				catch (CError $e)
				{
					$smarty->assign('last_error',$e);
					//$data=$obFields->GetRecord(Array('id'=>$_REQUEST['id']));
					$data=$obFields->GetRecordFromPost('CM_',$_POST);
					$smarty->assign('data',$data);
					if($KS_MODULES->IsActive($data['module']))
					{
						$mod=$data['module'];
						if(file_exists(MODULES_DIR.'/'.$mod.'/config.php'))
						{
							include MODULES_DIR.'/'.$mod.'/config.php';
							$varName='MODULE_'.$mod.'_db_config';
							$arModuleList=$$varName;
							$smarty->assign('tables',$arModuleList);
							$result=$smarty->fetch('admin/main_fields_onmodulechange.tpl');
							$smarty->assign('type',$result);
						}
					}
			    	$list=$KS_MODULES->GetInstalledList();
		    		$smarty->assign('modules',$list);
		    		$smarty->assign('types',$obFields->GetTypes());
		    		$page='_fields_edit';
		    		break;
				}
			}
		default:
			$obPages=new CPageNavigation($obFields);
			$totalUsers=$obFields->count();
			$list=$obFields->GetList(array('id'=>'asc'),false,$obPages->GetLimits($totalUsers));
			$smarty->assign('list',$list);
			$smarty->assign('pages',$obPages->GetPages($totalUsers));
			$page='_fields';
	}
}
else
{
	$page='';
	throw new CAccessError("MAIN_ACCESS_USER_FIELDS_CLOSED",403);
}

