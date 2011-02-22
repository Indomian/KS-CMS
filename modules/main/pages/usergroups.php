<?php

if( !defined('KS_ENGINE') )
{
  die("Hacking attempt!");
}

/*KS Engine ADMIN SYSTEM

File: usergroups.php
Original Code by BlaDe39 (c) 2008
Назначение: управление группами пользователей*/

require_once MODULES_DIR.'/main/libs/class.CUserGroup.php';
require_once MODULES_DIR.'/main/libs/class.CUGModules.php';
require_once MODULES_DIR.'/main/libs/class.CAccess.php';

global $KS_URL;

$USERGROUP=new CUserGroup;

//Проверка прав доступа
if($USER->GetLevel('main')>2) throw new CAccessError("MAIN_NOT_RIGHTS_MANAGE_USER_GROUPS");

/*Обрабатываем входные данные (постраничный вывод)*/
$obPages=new CPageNavigation($USERGROUP);
/*Обработка сортировки*/
$arSortFields=array('id','title','level');
$sOrderField=(in_array($_REQUEST['order'],$arSortFields))?$_REQUEST['order']:'id';
if($_REQUEST['dir']=='asc'):$sOrderDir='asc';$sNewDir='desc';else: $sOrderDir='desc';$sNewDir='asc';endif;
$arOrder=array(
	$sOrderField=>$sOrderDir,
);
/*Получаем затребованную операцию*/

$action=$_REQUEST['ACTION'];

if ($action=='new')
{
	$userdata['id']=-1;
	$obModule=new CUGModules();
	$userdata['MODULES']=$obModule->GetList();
	//Добавляем в список главный модуль
	$userdata['MODULES'][]=array(
		'name'=>'Главный модуль',
		'directory'=>'main',
		'LEVELS'=>$KS_MODULES->GetAccessArray('main'));
	$obAccess=new CModulesAccess();
	$userdata['ACCESS']=$obAccess->GetList(array('id'=>'asc'),array('group_id'=>$id));
	$page='_edit';
}
elseif ($action=='edit')
{
	$id=intval($_REQUEST['id']);
	$userdata=$USERGROUP->GetRecord(array('id'=>$id));
	$obModule=new CUGModules();
	$userdata['MODULES']=$obModule->GetList();
	//Добавляем в список главный модуль
	$userdata['MODULES'][]=array(
		'name'=>'Главный модуль',
		'directory'=>'main',
		'LEVELS'=>$KS_MODULES->GetAccessArray('main'));
	$obAccess=new CModulesAccess();
	$userdata['ACCESS']=$obAccess->GetList(array('id'=>'asc'),array('group_id'=>$id));
	$page='_edit';
}
elseif ($action=='save')
{
	try
	{
		$USERGROUP->AddAutoField('id');
		$id=$USERGROUP->Save("CUG_");
		if(!($id===false))
		{
			$obAccess=new CModulesAccess();
			foreach($_POST['CUG_level'] as $key=>$value)
			{
				$obAccess->Set($id,$key,min($value));
			}
		}
		CUrlParser::Redirect("/admin.php?".$KS_URL->GetUrl(Array('ACTION','id')));
	}
	catch (CError $e)
	{
		$userdata=$USERGROUP->GetRecord(array('id'=>$id));
		$smarty->assign('last_error',$e);
		$page='_edit';
	}
}
elseif ($action=='delete')
{
	if(in_array($_REQUEST['id'],$USER->GetGroups()))
	{
		throw new CError("MAIN_NOT_DELETE_GROUP_YOU_BELONG");
	}
	else
	{
		$id=intval($_REQUEST['id']);
		$USERGROUP->Delete($id);
		$obAccess=new CModulesAccess();
		$obAccess->DeleteItems(array('group_id'=>$id));
		CUrlParser::Redirect("/admin.php?".$KS_URL->GetUrl(Array('ACTION','id')));
	}
}

if($page=='_edit')
{
	$smarty->assign('userdata',$userdata);
}
else
{
	$totalUsers=$USERGROUP->count();
	$list=$USERGROUP->GetList($arOrder,$arFilter,$obPages->GetLimits($totalUsers));
	$smarty->assign('list',$list);
	$smarty->assign('groups_num',$totalUsers);
	$smarty->assign('pages',$obPages->GetPages($totalUsers));
	$smarty->assign('num_visible',$obPages->GetVisible());
	$smarty->assign('order',Array('newdir'=>$sNewDir,'curdir'=>$sOrderDir,'field'=>$sOrderField));
}
/*подготавливаем данные для вывода*/
$page='_usersgroup'.$page;
?>