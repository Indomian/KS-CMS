<?php
/*
 * CMS-remote
 *
 * Created on 27.10.2008
 *
 * Developed by blade39
 *
 * Виджет, выполняет функцию поиска категории по ее коду и пути, если путь и код категории верные,
 * возвращаются данные иначе выбрасывается 404 ошибка.
 * входные данные
 * Параметры функции
 * params:
 * addToNavChain - Y|N добавлять найденный элемент в навигационную цепочку
 * global_template - название глобального шаблона
 * tpl - шаблон вывода компонента, по умолчанию - стандартный
 * ID - номер элемента для вывода
 */

function smarty_function_CatCategory($params, &$smarty)
{
	global $USER,$KS_MODULES;

	$access_level=$USER->GetLevel('catsubcat');
	$parent_id = 0;						// корень
	$obCategory = new CCategory();		// создание экземпляра объекта для работы с категориями текстовых страниц
	if (isset($params['ID']))
		$arFilter = array('id' => $params['ID']);
	$arFilter['active']=1;
	if($data['main_content']= $obCategory->GetRecord($arFilter))
	{
		/* Неплохо бы добавить дату добавления в понятном формате, чтобы юзеры в Смарти не мучились :) */
		$data['main_content']['date'] = date("d.m.Y", $data['main_content']['date_add']);

		//Проверка прав доступа
		if($access_level>9) throw new CAccessError("SYSTEM_NOT_ACCESS_MODULE");
		if($access_level>8)
		{
			if(!in_array($data['main_content']['access_view'],$USER->GetGroups()))
			{
				throw new CAccessError("CATSUBCAT_NOT_ACCESS_SECTION");
			}
		}
		$smarty->assign('data', $data);
		if(($KS_MODULES->GetConfigVar('catsubcat','set_title',1)==1)||($params['setPageTitle']=='Y'))
		{
			$sTitle=$data['main_content']['seo_title']!=''?$data['main_content']['seo_title']:$data['main_content']['title'];
			$smarty->assign('TITLE',($sTitle!=''?$sTitle:$KS_MODULES->GetConfigVar('catsubcat','title_default','Текстовые страницы')));
			$smarty->assign('DESCRIPTION',$data['main_content']['seo_description']);
			$smarty->assign('KEYWORDS',$data['main_content']['seo_keywords']);
		}
		return $KS_MODULES->RenderTemplate($smarty,'/catsubcat/CatCategory',$params['global_template'],$params['tpl']);
	}
	else
	{
		throw new CHTTPError("SYSTEM_FILE_NOT_FOUND",404);
	}
}

function widget_params_CatCategory()
{
	$arFields = array
	(
		'addToNavChain' => array
		(
		  'title' => 'Добавлять раздел в навигационную цепочку?',
		  'type' => 'select',
		  'value' => array('Y' => 'Да', 'N' => 'Нет')
		),
		'setPageTitle' => array
		(
		  'title' => 'Использовать имя раздела в заголовке страницы?',
		  'type' => 'select',
		  'value' => array('Y' => 'Да', 'N' => 'Нет')
		),
		'ID' => array
		(
			'title' => 'Номер текстового раздела для вывода',
			'type' => 'text',
			'value' => ''
		)
	);
	return array
	(
		'fields' => $arFields
	);
}
?>
