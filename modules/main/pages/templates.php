<?php

/**
 * Страница модуля main редактирования шаблонов
 *
 * @filesource templates.php
 * @author blade39 <blade39@kolosstudio.ru>, north-e <pushkov@kolosstudio.ru>
 * @version 1.0
 * @since 04.05.2009
 */

if (!defined('KS_ENGINE'))
	die("Hacking attempt!");

include_once MODULES_DIR . '/main/libs/class.CTemplates.php';
include_once MODULES_DIR . '/main/libs/class.CUserGroup.php';

global $KS_URL;
$smarty->assign('tabTempl',1);
try
{
	if ($USER->GetLevel('main') > 7)
		throw new CAccessError("MAIN_ACCESS_MANAGEMENT_TEMPLATES_CLOSED",403);

	/* Объект для работы с глобальными шаблонами */
	$obTpl = new CGlobalTemplates();
	/* Объект для работы с шаблонами */
	$KS_TEMPLATES = new CTemplates;
	$sName='';
	switch($_REQUEST['ACTION'])
	{
		case 'save':
			$sName=$_POST['id'];
			try
			{
				if(array_key_exists('id',$_POST))
				{
					if(strlen($sName)<3) throw new CError("MAIN_TEMPLATE_NAME_LENGHT_ERROR");
					if(!preg_match('#^[a-zA-Z0-9\._]+$#',$sName)) throw new CError("MAIN_TEMPLATE_NAME_ERROR");
				}
				$sResult=$KS_TEMPLATES->SaveTemplate($sName,$_POST['scheme']);
				if(!array_key_exists('update',$_REQUEST))
				{
	    			CUrlParser::Redirect("/admin.php?".$KS_URL->GetUrl(Array('ACTION','id')));
	    		}
	    		else
	    		{
		    		CUrlParser::Redirect("/admin.php?".$KS_URL->GetUrl('ACTION','id').'&ACTION=edit&id='.$sName);
				}
			}
			catch(CError $e)
			{
				$smarty->assign('last_error',$e);
			}
		case "edit":
			$sName=$_GET['id'];
			$data['name'] = $sName;
		case "new":
			/* Вывод списка всех локальных шаблонов в глобальном шаблоне для редактирования */
			setcookie('lastSelectedTab','templates_'.time().'_tab0',time()+36000);
			$_COOKIE['lastSelectedTab']='templates_'.time().'_tab0';
			if($sName=='')
			{
				$data['is_new']=1;
				$sName='.default';
			}
			/* Читаем глобальный шаблон по идентификатору */
			$arSchemes=$KS_TEMPLATES->GetTemplate($sName);
			foreach($arSchemes as $key=>$value)
			{
				$data['templates'][$key]= htmlentities($value,ENT_QUOTES,'UTF-8');
			}
			$data['template'] = $data['templates']['index'];
			/* Читаем локальные шаблоны глобального шаблона */
			$data['modules'] = $KS_TEMPLATES->GetSubTemplates($sName);
			/* Устанавливаем данные для Смарти */
			$smarty->assign('data', $data);
			/* Переменная для определения административного шаблона Смарти */
			$page = '_templates_edit';
		break;
		case 'copyname':
			/* Задание имени шаблона для копирования */
			if (strlen(trim($_GET['id']))>0)
			{
				$id = trim($_GET['id']);
				$global_templates = $KS_TEMPLATES->GetList();
				$new_id = str_replace('.','',$id) . "_copy";
				$copy_number = 0;
				while (in_array($new_id, $global_templates))
				{
					$copy_number++;
					$new_id = str_replace('.','',$id) . "_copy_" . $copy_number;
				}
				$smarty->assign('templateName',$new_id);
				$smarty->assign('templateId',$id);
				$smarty->assign('action','copy');
				$page='_templates_editname';
			}
		break;
		case 'copy':
			/* Копирование шаблона */
			if (strlen(trim($_REQUEST['id']))>0)
			{
				if(!preg_match('#^[\.a-z0-9_]+$#i',$_REQUEST['id'])
				||!preg_match('#^[a-z0-9_]+$#i',$_REQUEST['newId']))
				{
					$smarty->assign('templateName',$_REQUEST['newId']);
					$smarty->assign('templateId',$_REQUEST['id']);
					$smarty->assign('action','copy');
					$page='_templates_editname';
					throw new CDataError("MAIN_TEMPLATE_NAME_ERROR");
				}
				$id = trim($_REQUEST['id']);
				$new_id=trim($_REQUEST['newId']);
				$global_templates = $KS_TEMPLATES->GetList();
				if($new_id=='')
				{
					$new_id = $id . "_copy";
					$copy_number = 0;
					while (in_array($new_id, $global_templates))
					{
						$copy_number++;
						$new_id = $id . "_copy_" . $copy_number;
					}
				}
				else
				{
					if(in_array($new_id,$global_templates))
					{
						$smarty->assign('templateName',$new_id);
						$smarty->assign('templateId',$id);
						$smarty->assign('action','copy');
						$page='_templates_editname';
						throw new CDataError("MAIN_TEMPLATE_ALREADY_EXISTS");
					}
				}
				$KS_TEMPLATES->Copy($id, $new_id);
				if($_GET['mode']=='small')
				{
					die();
				}
				$KS_URL->redirect("/admin.php?module=main&modpage=templates");
			}
		break;
		case 'copysub':
			$data['make_copy']=1;
		case 'editsub':
			/* Редактирование шаблона виджета */
			setcookie('lastSelectedTab','templates_'.time().'_tab0',time()+36000);
			$_COOKIE['lastSelectedTab']='templates_'.time().'_tab0';
			/* Имя глобального шаблона */
			$sName = $_GET['id'];
			/* Имя шаблона виджета */
			$sSubTemplate = $_GET['template'];
			/* Читаем шаблон виджета */
			$arRes = $KS_TEMPLATES->SubTemplate($sName, $sSubTemplate);
			$data['template'] = htmlentities($arRes['content'], ENT_QUOTES, 'UTF-8');
			$data['modules'] = $KS_TEMPLATES->GetSubTemplates($sName);
			$data['is_sub'] = 1;
			$data['sub_name'] = $arRes['name'];
			$data['name'] = $sName;
			$data['module'] = $arRes['module'];
			$data['file'] = $arRes['file'];
			/* Ищем справку по редактированию данного виджета */
			$expl = explode("/", $sSubTemplate);
			if (isset($expl[1]))
			{
				/* Имя модуля, к которому относится шаблон */
				$wmodule = $expl[0];
				/* Имя файла шаблона */
				$wtemplate = $expl[1];
				$arHelp=$KS_TEMPLATES->GetDescriptions($wmodule);
				if(count($arHelp)>0)
				{
					// Ищем в массиве с описаниями виджетов подходящий для данного шаблона ключ
					$corresponding_widget_key = false;
					foreach ($arHelp as $widget_key => $widget_data)
					{
						if (preg_match("#^(" . $widget_key. ")(.*)(.tpl)$#", $wtemplate))
						{
							$corresponding_widget_key = $widget_key;
						}
					}
					// Если ключ найдет, то смотрим, есть ли описание для шаблона виджета
					if ($corresponding_widget_key)
						if (isset($arHelp[$corresponding_widget_key]["help"]))
						{
							$data["widget_name"] = $arHelp[$corresponding_widget_key]["name"];
							$data["help"] = $arHelp[$corresponding_widget_key]["help"];
						}
				}
			}
			/* Отправляем данные о шаблоне в Смарти */
			$smarty->assign('data',$data);
			$page = '_templates_edit';
		break;
	}

	if($_POST['ACTION']=='savesub')
	{
		if($_POST['copysub']==1)
		{
			try
			{
				$sSubTemplate=$_POST['file'];
				$sTemplate=$_POST['template_file'];
				$sModule=$_POST['s_module'];
				$sPath=TEMPLATES_DIR.'/'.$_POST['id'].'/';
				$sFile=$sModule.'/'.$sSubTemplate;
				if(strlen($_POST['subid'])<3) throw new CError("MAIN_TEMPLATE_NAME_LENGHT_ERROR");
				if(!preg_match('#^[a-zA-Z0-9\-_]+$#',$_POST['subid'])) throw new CError("MAIN_TEMPLATE_NAME_ERROR");
				if(preg_match('#^([\w_\.]+)\.tpl#',$_POST['file'],$matches))
				{
					$_POST['file']=$matches[1].$_POST['subid'].'.tpl';
					if(file_exists($sPath.$sModule.'/'.$_POST['file'])) throw new CError("MAIN_TEMPLATE_ALREADY_EXISTS");
					$sName=$_POST['id'];
					if ($KS_TEMPLATES->SaveSub($sName))
					{
						$KS_URL->Set('ACTION','edit');
						$KS_URL->Set('id',$sName);
						$KS_URL->redirect("/admin.php?".$KS_URL->GetUrl(Array('template')));
					}
					else
					{
						throw new CError("SYSTEM_UNKNOWN_ERROR");
					}
				}
				else
				{
					throw new CError("SYSTEM_STRANGE_ERROR", 0, $_POST['file']);
				}
			}
			catch(CError $e)
			{
				$sName=$_POST['id'];
				$sSubTemplate=$_POST['s_module'].'/'.$sSubTemplate;
				$arRes=$KS_TEMPLATES->SubTemplate($sName,$sSubTemplate);
				$data['template']=htmlentities($_POST['template_file'],ENT_QUOTES,'UTF-8');
				$data['modules']=$KS_TEMPLATES->GetSubTemplates($sName);
				$data['is_sub']=1;
				$data['sub_name']=$arRes['name'];
				$data['new_name']=$_POST['subid'];
				$data['name']=$sName;
				$data['module']=$arRes['module'];
				$data['file']=$arRes['file'];
				$data['make_copy']=1;
				$smarty->assign('data',$data);
				$smarty->assign('last_error',$e);
				$page='_templates_edit';
			}
		}
		else
		{
			try
			{
				$sName=$_POST['id'];
				if ($KS_TEMPLATES->SaveSub($sName))
				{
					if(!array_key_exists('update',$_REQUEST))
					{
				    	$KS_URL->Set('ACTION','edit');
						$KS_URL->Set('id',$sName);
						$KS_URL->redirect("/admin.php?".$KS_URL->GetUrl(Array('template')));
		    		}
		    		else
		    		{
		    			$KS_URL->Set('ACTION','editsub');
		    			$KS_URL->Set('id',$sName);
		    			$KS_URL->Set('template',$_POST['s_module'].'/'.$_POST['file']);
		    			CUrlParser::Redirect("/admin.php?".$KS_URL->GetUrl());
			    	}
				}
				else
				{
					throw new CError("SYSTEM_UNKNOWN_ERROR",2);
				}
			}
			catch(CError $e)
			{
				$sName=$_GET['id'];
				$sSubTemplate=$_GET['template'];
				$arRes=$KS_TEMPLATES->SubTemplate($sName,$sSubTemplate);
				$data['template']=htmlentities($_POST['template_file'],ENT_QUOTES,'UTF-8');
				$data['modules']=$KS_TEMPLATES->GetSubTemplates($sName);
				$data['is_sub']=1;
				$data['sub_name']=$arRes['name'];
				$data['name']=$sName;
				$data['module']=$arRes['module'];
				$data['file']=$arRes['file'];
				$smarty->assign('data',$data);
				$smarty->assign('last_error',$e);
				$page='_templates_edit';
			}
		}
	}
	elseif($_POST['ACTION']=='clearCache')
	{
		$smarty->clear_all_cache();
		$smarty->clear_compiled_tpl();
		$page=_ShowList($KS_TEMPLATES);
	}
	elseif($_POST['ACTION']=='clearPicCache')
	{
		global $KS_FS;
		if(!$KS_FS->cleardir(UPLOADS_DIR.'/PicCache'))
		{
			$this->AddNotify('MAIN_PICTURE_CACHE_CLEAN_FAIL');
		}
		$page=_ShowList($KS_TEMPLATES);
	}
	elseif($_GET['ACTION']=='delete')
	{
		if($_GET['id']!='.default')
		{
			if($KS_TEMPLATES->Delete($_GET['id']))
			{
				$obTpl->DeleteByTemplate($_GET['id']);
			}
			else
			{
				throw new CError("MAIN_ERROR_DELETE_TEMPLATE");
			}
		}
		else
		{
			throw new CError("MAIN_ERROR_DELETE_DEFAULT_TEMPLATE");
		}
		$page=_ShowList($KS_TEMPLATES);
	}
	elseif($_GET['ACTION']=='deletesub')
	{
		$sName=$_GET['id'];
		try
		{
			$KS_TEMPLATES->DeleteSub($sName,$_GET['template']);
			CUrlParser::Redirect("/admin.php?module=main&modpage=templates&ACTION=edit&id=".$sName);
		}
		catch(CError $e)
		{
			$smarty->assign('last_error',$e);
			$sName=$_GET['id'];
			$data['template']=htmlentities($KS_TEMPLATES->GetTemplate($sName),ENT_QUOTES,'UTF-8');
			$data['modules']=$KS_TEMPLATES->GetSubTemplates($sName);
			$data['name']=$sName;
			$smarty->assign('data',$data);
			$page='_templates_edit';
		}
	}
	elseif($_GET['ACTION']=='getgroups')
	{
		$obGroups=new CUserGroup();
		$arGroups=$obGroups->GetList();
		$smarty->assign('groups',$arGroups);
		$smarty->assign('mode','groupslist');
		$smarty->assign('tdId',$_GET['tdId']);
		$smarty->assign('id',$_GET['id']);
		$result = array('tdId' => $_GET['tdId'],'id' => $_GET['id']);
		$result['html'] = $smarty->fetch('admin/main_templates_ajax.tpl');
		echo json_encode($result);
		die();
	}
	elseif($_POST['ACTION']=='saveLinks')
	{
		if (is_array($_POST['links']))
		{
			foreach ($_POST['links'] as $id=>$arItem)
			{
				if($id>0)
				{
					//print_r($arItem);
					$obTpl->Update($id,$arItem);
				}
			}
		}
		if (is_array($_POST['newlinks']))
		{
			foreach ($_POST['newlinks'] as $arItem)
			{
				if(($arItem['url_path']!='')||($arItem['function1']!=''))
					//print_r($arItem);
					$obTpl->Save("",$arItem);
			}
		}
		if(is_array($_POST['delete']))
		{
			$obTpl->DeleteByIds(array_keys($_POST['delete']));
		}
		$smarty->assign('tabLinks','1');
		$smarty->assign('tabTempl',0);
		$page=_ShowList($KS_TEMPLATES);
	}
	elseif($page=='')
	{
		$page=_ShowList($KS_TEMPLATES);
  	}

}
catch (CAccessError $e)
{
	$smarty->assign('last_error',$e);
	throw $e;
}
catch (CDataError $e)
{
	$smarty->assign('last_error',$e);
	if($page=='') $page=_ShowList($KS_TEMPLATES);
}
catch (CError $e)
{
	$smarty->assign('last_error',$e);
	$page=_ShowList($KS_TEMPLATES);
}

if($_GET['mode']=='small')
{
	echo $smarty->get_template_vars('last_error');
	$smarty->display('admin/main'.$page.'.tpl');
	die();
}

function _ShowList($KS_TEMPLATES)
{
	global $smarty;
	$arResult=$KS_TEMPLATES->GetList();
	//$arLinks['TEMPLATES']=$arResult;
	foreach($arResult as $value)
	{
		$arSubSchemes=$KS_TEMPLATES->GetSchemeList($value);
		if(is_array($arSubSchemes))
		{
			foreach($arSubSchemes as $scheme)
			{
				$arLinks['TEMPLATES'][]=$value.':'.$scheme;
			}
		}
	}
	$obTpl=new CGlobalTemplates();

  	$arLinks['LINKS']=$obTpl->GetList(array('orderation'=>'asc'));
  	$obGroups=new CUserGroup();
	$arGroups=$obGroups->GetList();
	$smarty->assign('groups',$arGroups);
	$smarty->assign('dataList',$arResult);
	$smarty->assign('linksList',$arLinks);
    return '_templates';
}

?>
