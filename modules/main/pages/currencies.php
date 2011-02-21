<?php

if( !defined('KS_ENGINE') )
{
  die("Hacking attempt!");
}

/*KS Engine ADMIN SYSTEM

File: currencies.php
Original Code by BlaDe39 (c) 2008
Назначение: управление валютами*/

require_once MODULES_DIR.'/etc/CCurrency.php';

$CURRENCY=new CCurrency;

$num_visible=5;
if (array_key_exists('CC_page',$_REQUEST))
{
	$show_from=($_REQUEST['CC_page']-1)*$num_visible;
	if ($show_from<0) $show_from=0;
}

if ($CURRENCY->num_rows>$num_visible)
{
	$page_num=floor($CURRENCY->num_rows/$num_visible)+1;
	$pages['num']=$page_num;
	$pages['active']=$_REQUEST['CC_page'];
	if ($pages['active']==0) $pages['active']=1;
	$uri="";
	foreach($_GET as $key=>$item)
	{
		if ($key!="CC_page")
		{
			$uri.="$key=$item&";
		}
	}
	$uri=chop($uri,"& ");
	for($i=1;$i<=$page_num;$i++)
	{
		$pages['pages'][$i]="CC_page=".$i."&$uri";
	}
	$smarty->assign('pages',$pages);
}

if (array_key_exists('CC_ACTION',$_REQUEST))
{
	$action=$_REQUEST['CC_ACTION'];
	if ($action=='new')
	{
		$smarty->assign('userdata',$userdata);
		$page='_edit';
	}
	if ($action=='edit')
	{
		$id=intval($_REQUEST['CC_id']);
		$userdata=$CURRENCY->GetById($id);
		$smarty->assign('userdata',$userdata);
		$page='_edit';
	}
	if ($action=='save')
	{
		$id=$CURRENCY->Save();
		$userdata=$CURRENCY->GetById($id);
		if ($error_class->error!=0)
		{
			$smarty->assign('userdata',$userdata);
			$page='_edit';
		}
		else
		{
			$list=$CURRENCY->GetList("",0,$order_query,$show_from,$num_visible);
			$smarty->assign('list',$list);
			$smarty->assign('groups_num',$CURRENCY->num_rows);
		}
	}
	if ($action=='delete')
	{
		$id=intval($_REQUEST['CC_id']);
		$userdata=$CURRENCY->GetById($id);
		$smarty->assign('message','Вы действительно хотите удалить валюту: '.$userdata['title'].', имеющую номер: '.$userdata['id']);
		$smarty->assign('userdata',$userdata);
		$page='_msgbox';
	}
	if ($action=='delete_confirm')
	{
		$id=intval($_REQUEST['CC_id']);
		$CURRENCY->Del($id);
		$list=$CURRENCY->GetList("",0,$order_query,$show_from,$num_visible);
		$smarty->assign('list',$list);
		$smarty->assign('groups_num',$CURRENCY->num_rows);
	}
}
else
{
	$list=$CURRENCY->GetList("",0,$order_query,$show_from,$num_visible);
	$smarty->assign('list',$list);
	$smarty->assign('groups_num',$CURRENCY->num_rows);
}

?>