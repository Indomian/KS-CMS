<?php
/**
 * \file save.php
 * Сохранение значения в базу данных
 * Поддержка модуля catsubcat
 * Файл проекта CMS-local.
 * 
 * Создан 26.11.2008
 *
 * \author blade39
 * \version 0.2
 * \todo
 * 
 * Переменные доступные в шаблоне
 * $prefix - префикс в форме, если получение данных идет из формы
 * $arField - массив описывающий пользовательское поле
 * $value - значение введенное пользователем в поле для ввода
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/forum/libs/class.CForumAPI.php';

global $USER,$KS_EVENTS_HANDLER;

if(!function_exists('CreateNewTheme'))
{
	function CreateNewTheme(&$data,$params)
	{
		$obForum=new CForumAPI();
		$theme=$data['title'];
		$descr=$data['description'];
		if($themeId=$obForum->SaveTheme('',
			array(
				'forum_id'=>$params['forum_id'],
				'date_edit'=>time(),
				'date_add'=>time(),
				'title'=>$theme,
				'description'=>$descr,
				'content'=>$data['content'],
				'author_id'=>$params['user_id'],
				'author_name'=>$params['user_name'],
				'views'=>0,
				'replies'=>0,
				'status'=>1,
				'icon'=>0,
				)
		))
		{
			$sValue=$themeId;	
		}
		else
		{
			$sValue=0;
		}	
		$data['ext_'.$params['field_name']]=$sValue;
		return $sValue;	
	}
}

if($_POST[$prefix.'ext_'.$arField['title'].'_sw']==1)
{
	$arHandler=array('hFunc'=>'CreateNewTheme','bOnce'=>true,
		'arParams'=>array(
			'forum_id'=>intval($_POST[$prefix.'ext_'.$arField['title'].'_forum']),
			'field_name'=>$arField['title'],
			'user_id'=>$USER->ID(),
			'user_name'=>$USER->userdata['title']));
	$KS_EVENTS_HANDLER->AddEvent('main','onBeforeFieldsObjectSave',$arHandler);
}
elseif($_POST[$prefix.'ext_'.$arField['title'].'_del']==1)
{
	$obForum=new CForumAPI();
	$obForum->DeleteThemesByIds(array($value));
	$sValue=0;
}
else
{
	$sValue=$value;
}
?>
