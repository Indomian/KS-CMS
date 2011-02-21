<?php
/**
 * \file smilies.php
 * Файл для управления смайликами добавленными в систему
 * Файл проекта kolos-cms.
 * 
 * Создан 08.06.2009
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 1.0
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

/*Здесь подключаем глобальные переменные*/
global $KS_URL,$MODULE_interfaces_db_config;

/*Здесь подключаем все необходимые нам библиотеки*/
include_once MODULES_DIR.'/interfaces/libs/class.CSmile.php';
include MODULES_DIR.'/interfaces/config.php';


/*Здесь делаем проверку прав доступа, если что-то не так выбрасываем исключение*/

/*Здесь получаем текущую операцию*/
$action=$_GET['action'];
$showList=true;
$obSmile=new CSmile($MODULE_interfaces_db_config['smilies']);

// Обработка действий множественного выбора  
if (array_key_exists('ACTION',$_POST)&&($_POST['ACTION']=='common'))
{
	$arElements=$_POST['sel']['elm'];
	$action='common';
	if (array_key_exists('comdel',$_POST))
	{
		// Удаление выделенных элементов
		$sCommonAction='delete';
		$obSmile->DeleteByIds($arElements);
	}
}

/*Пробуем определить, что-ж мы всетаки собираемся делать*/
if($action=='new')
{
	$data=array('id'=>-1);
	$smarty->assign('data',$data);
	$showList=false;
}
elseif($action=='edit')
{
	$data=$obSmile->GetRecord(array('id'=>$_GET['id']));
	$smarty->assign('data',$data);
	$showList=false;
}
elseif($action=='save')
{
	try
	{
		$iId=$_POST['FG_id'];
		$obSmile->SetCheckMethod("AND");
		$obSmile->AddCheckField('smile');
		$obSmile->AddAutoField('id');
		$obSmile->AddFileField('img');
		$id=$obSmile->Save('FG_');
		if($id===false)
		{
			throw new CError('SYSTEM_UNKNOWN_ERROR');
		}
		if(strlen($_POST['apply'])>0)
		{
			$KS_URL->redirect('/admin.php?module=interfaces&page=smilies&action=edit&id='.$id);
		}
		else
		{
			$KS_URL->redirect('/admin.php?module=interfaces&page=smilies');
		}
	}
	catch (CError $e)
	{
		$smarty->assign('last_error',$e);
		$obSmile=new CForumGroup();
		$data=$obSmile->GetRecord(array('id'=>$iId));
		$smarty->assign('data',$data);
		$showList=false;
	}
}
elseif($action=='delete')
{
	try
	{
		$obSmile->Delete($_GET['id']);
	}
	catch (CError $e)
	{
		$smarty->assign('last_error',$e);
	}
}

/*Смотрим, что надо отобразить, список элементов или элемент*/
if($showList)
{
	/*Обрабатываем входные данные (постраничный вывод)*/
	$obPages=new CPageNavigation($obSmile);
	/*Обработка сортировки*/
	$arSortFields=array('id','smile','group');
	$sOrderField=(in_array($_REQUEST['order'],$arSortFields))?$_REQUEST['order']:'id';
	if($_REQUEST['dir']=='asc'):$sOrderDir='asc';$sNewDir='desc';else: $sOrderDir='desc';$sNewDir='asc';endif;
	$arFilter=array();
	$arOrder=array($sOrderField=>$sOrderDir);
	/*Получаем списочек и отдаем его смарти*/
	$arList['TOTAL']=$obSmile->Count($arFilter);
	$arList['ITEMS']=$obSmile->GetList($arOrder,$arFilter,$obPages->GetLimits($arList['TOTAL']));
	/*подготавливаем данные для вывода*/
	$smarty->assign('dataList',$arList);
	$smarty->assign('pages',$obPages->GetPages());
	$smarty->assign('num_visible',$obPages->GetVisible());
	$smarty->assign('order',Array('newdir'=>$sNewDir,'curdir'=>$sOrderDir,'field'=>$sOrderField));
	$page='_smilies';
}
else
{
	$arGroups=$obSmile->Count(false,'`group`');
	$smarty->assign('groups',$arGroups);
	$page='_smilies_edit';
}
?>
