<?php
/**
 * \file self.simple_expanding_menu.php
 * Файл для вывода меню c вложенными элементами в виде дерева
 * Файл проекта CMS-local.
 *
 * Создан 25.02.2009
 *
 * \author north-e <pushkov@kolosstudio.ru>
 * \version 0.1
 * \todo
 *
 * @var $menu_params - входной массив данных о типе навигации (меню),
 * структура: id 	text_ident 	name 	description 	script_name 	active;
 */

/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/navigation/libs/class.CNav.php';


if(!function_exists('self_tree_menu_GetChildren'))
{
	function self_tree_menu_GetChildren($id,$arList)
	{
		$arResult=array();
		foreach($arList as $arItem)
		{
			if($arItem['parent_id']==$id)
			{
				if($id!=$arItem['id'])
				{
					$arItem['children']=self_tree_menu_GetChildren($arItem['id'],$arList);
					$arResult[$arItem['id']]=$arItem;
				}
			}
		}
		return $arResult;
	}
}

$obMenu = new CNavElement();
if($arItems = $obMenu->GetList(array('orderation'=>'asc'),array('type_id'=>$menu_params['id'],'active'=>1)))
{
	$output=self_tree_menu_GetChildren(0,$arItems);
}
