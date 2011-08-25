<?php
/**
 * @file modules/redirect/main.inc.php
 * Файл модуля redirect
 * Файл проекта kolos-cms.
 *
 * @since 06.05.2011
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

$module='redirect';
//Определяем глобальные переменные которые могут понадобиться внутри модуля
global $USER,$smarty;

//Получаем переменные прав на доступ к модулю и разделам модуля
$access_level=$USER->GetLevel($module);
if($access_level==10) throw new CAccessError('SYSTEM_NOT_ACCESS_MODULE');

$smarty->plugins_dir[] = MODULES_DIR.'/'.$module.'/widgets/';

$smarty->assign('TITLE',$this->GetTitle($module));
$smarty->assign('SEO_TITLE',$this->GetConfigVar($module,'seo_title',$this->GetTitle($module)));
$smarty->assign('SEO_KEYWORDS',$this->GetConfigVar($module,'seo_keywords'));
$smarty->assign('SEO_DESCRIPTION',$this->GetConfigVar($module,'seo_description'));

if(isset($_REQUEST['url']))
{
	$time=$this->GetConfigVar($module,'time',30);
	if($time>0)
	{
		$smarty->assign('content',str_replace('#LINK#','<a href="'.$_REQUEST['url'].'">'.$_REQUEST['url'].'</a>',$this->GetConfigVar($module,'content')));
		$smarty->assign('url',$_REQUEST['url']);
		$smarty->assign('pause',$time);
		$smarty->assign('use_pause',$this->GetConfigVar($module,'auto',0));
		$output['main_content']=$this->RenderTemplate($smarty,'/redirect/redirect');
	}
	else
	{
		CUrlParser::get_instance()->Redirect($_REQUEST['url']);
	}
}
else
{
	throw new CHttpError('SYSTEM_FILE_NOT_FOUND',404);
}

