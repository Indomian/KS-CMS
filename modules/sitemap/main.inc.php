<?php
/**************************************************************
/	KS ENGINE
/	(c) 2008 ALL RIGHTS RESERVED
/**************************************************************
/**************************************************************
/	Author: BlaDe39 <blade39@kolosstudio.ru>
/	http://kolos-studio.ru/
/	http://dotj.ru/
/**************************************************************
/**************************************************************
/	Назначение: работа с блогами
/	Версия:	0.1
/	Последняя модификация: 25.03.2008
/**************************************************************
*/

/*
ИНФОРМАЦИЯ ПО ОТДАВАЕМОЙ ИНФОРМАЦИИ
модуль должен отдать переменную $output, в общем случае, с отформатированными данными
*/

if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

//Задаем название модуля
$module='sitemap';
//Определяем глобальные переменные которые могут понадобиться внутри модуля
global $USER,$KS_MODULES,$smarty;

//Получаем переменные прав на доступ к модулю и разделам модуля
$access_level=$USER->GetLevel($module);
$arUserGroups=$USER->GetGroups();
if($access_level==10) throw new CAccessError('SYSTEM_NOT_ACCESS_MODULE');

$smarty->plugins_dir[] = MODULES_DIR.'/'.$module.'/widgets/';

try
{
	/**
		* Работаем как модуль, значит надо провести полную проверку переданного пути
		* на правильность и на права доступа, если что-то не так, лучше отдать ошибку.
		*/
	$root_path=$KS_MODULES->GetSitePath($module);
	if($root_path!='/')
	{
		if($KS_MODULES->IsActive('navigation'))
			CNNavChain::get_instance()->Add($KS_MODULES->GetTitle($module),'/'.$root_path.'/');
		$sUrl='/'.$root_path.'';
		$iBase=2;
		if(count($this->GetPathDirs())>2) throw new CError('SYSTEM_FILE_NOT_FOUND');
	}
	else
	{
		$sUrl='';
		$iBase=1;
		if(count($this->GetPathDirs())>1) throw new CError('SYSTEM_FILE_NOT_FOUND');
	}
	if(!function_exists('smarty_function_sitemap')) include MODULES_DIR.'/'.$module.'/widgets/function.sitemap.php';
	$smarty->assign('TITLE',$KS_MODULES->GetTitle($module));
	$res=smarty_function_sitemap($module_parameters,$smarty);
}
catch(CAccessError $e)
{
	$res=$e->__toString();
}
$output['main_content']=$res;
