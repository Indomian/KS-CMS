<?php
/**
 * @file main.inc.php
 * Файл модуля Гостевая книга 2.0
 * Файл проекта kolos-cms.
 *
 * Создан: 08.09.2009
 * Изменения: 13.09.2010
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.5.4-13
 */
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

//Задаем название модуля
$module='guestbook2';
//Определяем глобальные переменные которые могут понадобиться внутри модуля
global $USER,$KS_IND_matches,$smarty;

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
	$root_path=$this->GetSitePath($module);
	if($root_path!='/')
	{
		if($this->IsActive('navigation'))
			CNNavChain::get_instance()->Add($this->GetTitle($module),$root_path);
		$sUrl='/'.$root_path.'';
		$iBase=2;
		if(count($KS_IND_matches[1])>3) throw new CError('SYSTEM_FILE_NOT_FOUND');
	}
	else
	{
		$sUrl='';
		$iBase=1;
		if(count($KS_IND_matches[1])>1) throw new CError('SYSTEM_FILE_NOT_FOUND');
	}

	if(is_numeric($KS_IND_matches[3]))
	{
		$smarty->assign('element_id',intval($KS_IND_matches[3]));
		$res=$this->RenderTemplate($smarty,'/guestbook2/gb2item',$this->GetTemplate());
	}
	elseif(count($KS_IND_matches[1])>2)
	{
		$res=$this->RenderTemplate($smarty,'/guestbook2/gb2inner',$this->GetTemplate());
	}
	else
	{
		$res=$this->RenderTemplate($smarty,'/guestbook2/gb2index',$this->GetTemplate());
	}
	$smarty->assign('TITLE',$this->GetTitle($module));
}
catch(CAccessError $e)
{
	$res=$e->__toString();
}
$output['main_content']=$res;
