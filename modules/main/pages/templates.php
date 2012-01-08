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
	$sAction='';
	if(isset($_REQUEST['ACTION'])) $sAction=$_REQUEST['ACTION'];
	if($sAction=='clearCache')
	{
		$smarty->clear_all_cache();
		$smarty->clear_compiled_tpl();
		$page=_ShowList($KS_TEMPLATES);
	}
	elseif($sAction=='clearPicCache')
	{
		global $KS_FS;
		if(!$KS_FS->cleardir(UPLOADS_DIR.'/PicCache'))
			$this->AddNotify('MAIN_PICTURE_CACHE_CLEAN_FAIL');
		$page=_ShowList($KS_TEMPLATES);
	}
	elseif($sAction=='getgroups')
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
	elseif($sAction=='saveLinks')
	{
		if(isset($_POST['links']) && is_array($_POST['links']))
			foreach ($_POST['links'] as $id=>$arItem)
				if($id>0)
					$obTpl->Update($id,$arItem);
		if (isset($_POST['newlinks']) && is_array($_POST['newlinks']))
			foreach ($_POST['newlinks'] as $arItem)
				if((isset($arItem['url_path']) && $arItem['url_path']!='')||
					(isset($arItem['function1']) && $arItem['function1']!=''))
					$obTpl->Save("",$arItem);
		if(isset($_POST['delete']) && is_array($_POST['delete']))
			$obTpl->DeleteByIds(array_keys($_POST['delete']));
		$smarty->assign('tabLinks','1');
		$smarty->assign('tabTempl',0);
		$page=_ShowList($KS_TEMPLATES);
	}
	elseif($page=='')
		$page=_ShowList($KS_TEMPLATES);
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

function _ShowList($KS_TEMPLATES)
{
	global $smarty;
	$arResult=$KS_TEMPLATES->GetList();
	//$arLinks['TEMPLATES']=$arResult;
	foreach($arResult as $value)
	{
		$arSubSchemes=$KS_TEMPLATES->GetSchemeList($value);
		if(is_array($arSubSchemes))
			foreach($arSubSchemes as $scheme)
				$arLinks['TEMPLATES'][]=$value.':'.$scheme;
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
