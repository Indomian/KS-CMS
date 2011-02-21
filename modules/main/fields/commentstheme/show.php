<?php
/**
 * \file show.php
 * Файл формирует вывод поля для связи темы комментариев с элементом
 * Файл проекта CMS-local.
 *
 * Создан 26.11.2008
 *
 * \author blade39
 * \version 0.1
 * \todo Добавить возможность вызова дополнительного окна с выбором существующей темы
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}
global $KS_MODULES;
if($KS_MODULES->IsActive('forum'))
{
	include_once MODULES_DIR.'/forum/libs/class.CForumGroup.php';
	include_once MODULES_DIR.'/forum/libs/class.CForum.php';

	if($params['value']!=0)
	{
		if($params['value']>0)
		{
			$sResult="Комментарии находятся в теме номер: ".$params['value'];
			$sResult.='<label><input type="checkbox" name="'.$params['prefix'].'ext_'.$params['field']['title'].'_del" value="1" '.$checked.'/> Удалить комментарии</label>';
			$sResult.='<input type="hidden" name="'.$params['prefix'].'ext_'.$params['field']['title'].'" value="'.$params['value'].'"/>';
		}
		else
		{
			$checked=' checked="checked" ';
			$sResult='<label><input type="checkbox" name="'.$params['prefix'].'ext_'.$params['field']['title'].'_sw" value="1" '.$checked.'/> Использовать комментарии</label>';
			$sResult.=' размещать комментарии в форуме:';
			$obForum=new CForum();
			$arForums=$obForum->GetList();
			$sResult.='<select name="'.$params['prefix'].'ext_'.$params['field']['title'].'_forum">';
			foreach($arForums as $forum)
			{
				if($forum['id']==(-1*$params['value']))
				{
					$checked=' selected="selected" ';
				}
				else
				{
					$checked='';
				}
				$sResult.='<option value="'.$forum['id'].'"'.$checked.'>'.$forum['title'].'</option>';
			}
			$sResult.='</select>';
			$sResult.='<input type="hidden" name="'.$params['prefix'].'ext_'.$params['field']['title'].'" value="0"/>';
		}
	}
	else
	{
		$sResult='<label><input type="checkbox" name="'.$params['prefix'].'ext_'.$params['field']['title'].'_sw" value="1" '.$checked.'/> Использовать комментарии</label>';
		$sResult.=' размещать комментарии в форуме:';
		$obForum=new CForum();
		$arForums=$obForum->GetList();
		$sResult.='<select name="'.$params['prefix'].'ext_'.$params['field']['title'].'_forum">';
		foreach($arForums as $forum)
		{
			$sResult.='<option value="'.$forum['id'].'">'.$forum['title'].'</option>';
		}
		$sResult.='</select>';
		$sResult.='<input type="hidden" name="'.$params['prefix'].'ext_'.$params['field']['title'].'" value="0"/>';
	}
}
else
{
	$sResult=$KS_MODULES->GetText('forum_module_required');
}
?>
