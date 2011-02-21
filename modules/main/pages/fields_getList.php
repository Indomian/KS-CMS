<?php

if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}
/*KS Engine ADMIN SYSTEM

File: field_getList.php
Original Code by BlaDe39 (c) 2008
Назначение: вывод окна отображения привязки к элементам дополнительных полей*/

//Устанавливаем шаблон отрисовки результатов
global $_ks_modules_linkable;
$page='admin/main_getList.tpl';
if($_REQUEST['type']=='elm')
{	$page='admin/main_getElmList.tpl';
}
//Получаем значения входных переменных

global $KS_MODULES;
$arModules=$KS_MODULES->GetInstalledList();
foreach ($arModules as $item)
{
	$arTitles[$item['directory']]=$item['name'];
}
foreach ($_ks_modules_linkable as $item)
{
    if ($item==$_GET['mod'])
    	$sel="selected=\"selected\"";
    else
    	$sel="";
	$arResult[]=Array('id'=>$item,'title'=>$arTitles[$item],'sel'=>$sel);
}

// Формирование данных для вывода
$smarty->assign('num_visible',$iElCount);
$smarty->assign('dataList',$arResult);
$smarty->assign('pages',$pages);
$smarty->assign('order',Array('newdir'=>$sNewDir,'curdir'=>$sOrderDir,'field'=>$sOrderField));
$smarty->display($page);
die();

?>