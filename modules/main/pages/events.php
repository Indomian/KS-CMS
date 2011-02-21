<?php
/*
 * CMS-local
 * 
 * Created on 10.11.2008
 *
 * Developed by blade39
 * 
 */
if( !defined('KS_ENGINE') )die("Hacking attempt!");

include_once MODULES_DIR.'/main/libs/class.CEvents.php';
include_once MODULES_DIR.'/main/libs/class.CEventTemplates.php';

//Проверка прав доступа
if($USER->GetLevel('main')>6) throw new CAccessError("MAIN_NOT_RIGHTS_MANAGE_MAIL_TEMPLATES");

$action=$_REQUEST['ACTION'];

$KS_EVENTS=new CEvents();

global $KS_URL;

try{
	switch ($action){
		case 'new':
			$data=array('id'=>'-1');
			$KS_ETEMPLATES=new _CEventTemplates();
			$data['templates']=$KS_ETEMPLATES->GetList(array('id'=>'asc'),false);
			$page='_events_edit';
		break;
		case 'delete':
			$KS_EVENTS->Delete($_REQUEST['id']);
			$KS_URL->redirect('/admin.php?module=main&modpage=events');
		break;
		case 'save':
			$KS_EVENTS->SaveTemplate();
		break;
		case 'activate':
			$KS_EVENTS->Activate($_REQUEST['id']);
		break;
		case 'tpl_selected':
			$tpl = $_REQUEST['tpl'];
			$KS_ETEMPLATES=new CEventTemplates();
			$data=$KS_ETEMPLATES->GetTemplateVarNames($tpl);
			if(!empty($data)){
				echo json_encode(array('tpl_fields'=>$data, 'error'=>'no'));
			}
			die;
		break;
		case 'common':
			if(array_key_exists('comdel', $_REQUEST)){
				$arElements = $_REQUEST['sel']['elm'];
				foreach($arElements as $iId){
					$KS_EVENTS->Delete($iId);
				}
				$KS_URL->redirect('/admin.php?module=main&modpage=events');
			}elseif(array_key_exists('comact', $_REQUEST)){
				$arElements = $_REQUEST['sel']['elm'];
				foreach($arElements as $iId){
					$KS_EVENTS->Activate($iId);
				}
				$KS_URL->redirect('/admin.php?module=main&modpage=events');
			}
		break;
	}
}catch(CError $e){
	$smarty->assign('last_error',$e);
}

if($page=='')
{
	$ob=new CEvents();
	//Обработка вывода элементов
	/** @todo Переделать в новый стиль кода!*/
	$arSortFields=$ob->arFields;
	// Обработка порядка вывода элементов
	$sortField=$_REQUEST['order'];
	$sortDir=$_REQUEST['dir'];
	if($sortField!='')
		$sOrderField=(in_array($sortField,$arSortFields))?$sortField:$arSortFields[0];
	elseif($_SESSION['main']['admin_sort_main_events_by']!='')
		$sOrderField=$_SESSION['main']['admin_sort_main_events_by'];
	else
		$sOrderField=$KS_MODULES->GetConfigVar('main','admin_sort_main_events_by');
	//Направление сортировки
	if($sortDir!='')
		if($sortDir=='asc')	$sOrderDir='asc';
		else $sOrderDir='desc';
	else
		if($_SESSION['main']['admin_sort_main_events_dir']!='')	$sOrderDir=$_SESSION['main']['admin_sort_main_events_dir'];
		else $OrderDir=$KS_MODULES->GetConfigVar('main','admin_sort_main_events_dir');
	$sOrderField=(in_array($sOrderField,$arSortFields))?$sOrderField:$arSortFields[0];
	//Сохраняем сортировку в сессию
	$_SESSION['main']['admin_sort_main_events_by']=$sOrderField;
	$_SESSION['main']['admin_sort_main_events_dir']=$sOrderDir;
	$sNewDir=($sOrderDir=='desc')?'asc':'desc';
	/*Обрабатываем входные данные (постраничный вывод)*/
	$obPages=new CPageNavigation($ob);
	$totalUsers=$ob->count();
	$data=$ob->GetList(array($sOrderField=>$sOrderDir),false,$obPages->GetLimits($totalUsers));
	if(!empty($data))
		foreach($data as $key => $message)
			$data[$key]['content'] = nl2br(trim($data[$key]['content'],"\x00..\x1F"));
	$smarty->assign('pages',$obPages->GetPages($totalUsers));
	$smarty->assign('order',Array('newdir'=>$sNewDir,'curdir'=>$sOrderDir,'field'=>$sOrderField));
	$page='_events';
}

$smarty->assign('data',$data);

?>