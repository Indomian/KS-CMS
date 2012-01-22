<?php
/**
 * KS-CMS
 * @filesource catsubcat/widgets/function.CatElement.php
 * @since 27.10.2008
 * @version 2.6
 * @author blade39 <blade39@kolosstudio.ru>
 *
 * Виджет, выполняет функцию поиска элемента по его коду и пути, если путь и код элемента верные,
 * возвращаются данные иначе выбрасывается 404 ошибка.
 * входные данные
 * Параметры функции
 * params:
 * addToNavChain - Y|N добавлять найденный элемент в навигационную цепочку
 * global_template - название глобального шаблона
 * tpl - шаблон вывода компонента, по умолчанию - стандартный
 * ID - номер элемента для вывода
 */

function smarty_function_CatElement($params, &$smarty)
{
	global $USER,$KS_MODULES;

	if(!isset($params['url'])) $sUrl = ''; else	$sUrl=$params['url'];
	$access_level=$USER->GetLevel('catsubcat');
	if($access_level>8) throw new CAccessError("CATSUBCAT_NOT_VIEW_ELEMENTS");
	if(isset($params['text_ident']) && IsTextIdent($params['text_ident']))
		$arElmFilter=array('text_ident'=>$params['text_ident']);
	elseif(isset($params['ID']) && ($params['ID']>0))
		$arElmFilter=array('id'=>intval($params['ID']));
	else
		throw new CDataError("CATSUBCAT_ID_PARAM_REQUIRED");
	$obElement=CCatsubcatAPI::get_instance()->Element();
	/* обратились к элементу */
	$arElmFilter['active']=1;
	if($arElement = $obElement->GetRecord($arElmFilter))
	{
		/* Неплохо бы добавить дату добавления в понятном формате, чтобы юзеры в Смарти не мучились :) */
		$arElement['date'] = date("d.m.Y", $arElement['date_add']);

		if($sUrl=='')
		{
			$obCategory=new CCategory();
			$parent_id=$arElement['parent_id'];
			$i=0;
			while(($i<30)&&($parent_id!=0))
			{
				$arFilter=array('id'=>$parent_id);
				if($arRow=$obCategory->GetRecord($arFilter))
				{
					$parent_id=$arRow['parent_id'];
					$sUrl='/'.$arRow['text_ident'].$sUrl;
				}
				$i++;
			}
		}
		$sUrl.='/'.$arElement['text_ident'].'.html';

		$smarty->assign('data', $arElement);
		$smarty->assign('url',$sUrl);

		if(array_key_exists('addToNavChain',$params) && $params['addToNavChain']=='Y' && $KS_MODULES->IsActive('navigation'))
			CNNavChain::get_instance()->Add($arElement['title'],$sUrl);

		if($params['setPageTitle']=='Y')
		{
			$sTitle=$arElement['seo_title']!=''?$arElement['seo_title']:$arElement['title'];
			$smarty->assign('TITLE',($sTitle!=''?$sTitle:$KS_MODULES->GetConfigVar('catsubcat','title_default','Текстовые страницы')));
			$smarty->assign('DESCRIPTION',$arElement['title']['seo_description']);
			$smarty->assign('KEYWORDS',$arElement['title']['seo_keywords']);
		}
		$sResult=$KS_MODULES->RenderTemplate($smarty,'/catsubcat/CatElement',$params['global_template'],$params['tpl']);
		/* Шаблон найдем, можно увеличить количество показов страницы на единицу */
		if(!isset($_COOKIE['cscp'.$arElement['id']]) || $_COOKIE['cscp'.$arElement['id']]!='1')
		{
			$obElement->Update($arElement['id'],array('views_count'=>intval($arElement['views_count']) + 1));
			setcookie('cscp'.$arElement['id'],1,time()+30000);
		}
		return $sResult;
	}
	throw new CHTTPError("SYSTEM_FILE_NOT_FOUND", 404);
}

function widget_params_CatElement()
{
	$arFields = array
	(
		'addToNavChain' => array
		(
		  'title' => 'Добавлять элемент в навигационную цепочку?',
		  'type' => 'select',
		  'value' => array('Y' => 'Да', 'N' => 'Нет')
		),
		'setPageTitle' => array
		(
		  'title' => 'Использовать имя элемента в заголовке страницы?',
		  'type' => 'select',
		  'value' => array('Y' => 'Да', 'N' => 'Нет')
		),
		'ID' => array
		(
			'title' => 'Номер текстового элемента для вывода',
			'type' => 'text',
			'value' => ''
		)
	);
	return array
	(
		'fields' => $arFields
	);
}
