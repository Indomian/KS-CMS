<?php
/*
 * CMS-remote
 * 
 * Created on 27.10.2008
 *
 * Developed by blade39
 * 
 * Виджет, выполняет функцию поиска элемента по его коду и пути, если путь и код элемента верные,
 * возвращаются данные иначе выбрасывается 404 ошибка.
 * входные данные
 * $KS_IND_matches - разобранный GET запрос, структуру видно в файле /index.php.
 * Параметры функции
 * params:
 * addToNavChain - Y|N добавлять найденный элемент в навигационную цепочку
 * global_template - название глобального шаблона
 * tpl - шаблон вывода компонента, по умолчанию - стандартный
 * ID - номер элемента для вывода
 */

function smarty_function_CatElement($params, &$smarty)
{
	global $KS_IND_matches,$MODULE_catsubcat_config,$USER,$KS_MODULES,$global_template;
	
	$sUrl = '';
	$data = array();
	
	$access_level=$USER->GetLevel('catsubcat');
	if($access_level>8) throw new CAccessError("SYSTEM_NOT_ACCESS_MODULE");
	$arElmFilter=array('text_ident'=>$params['text_ident']);
	//Если не указан номер элемента который надо найти (т.е. работаем как основной контент или по адресу)
	//то пытаемся найти этот элемент в данном адресе.
	if(($params['ID']==''))
	{
		if(!array_key_exists('parent_id',$params))
		{
			//Пробуем определить верность указанного пути
			$parent_id=0;
			$obCategory=new CCategory();
			foreach($KS_IND_matches[1] as $path)
			{
				if(($path!='')&&($path!=$KS_IND_matches[2]))
				{
					$arFilter=array('text_ident'=>$path,'parent_id'=>$parent_id);
					if($arRow=$obCategory->GetRecord($arFilter))
					{	
						$parent_id=$arRow['id'];
						$sUrl.='/'.$arRow['text_ident'];
						$data['parent']=$arRow;
					}
					else
					{
						throw new CHTTPError("SYSTEM_ELEMENT_NOT_FOUND",404);
					}
				}
			}
		}
		else
		{
			$parent_id=$params['parent_id'];
		}	
		$arElmFilter['parent_id']=$parent_id;
	}
	else
	{
		$arElmFilter=array('id'=>$params['ID']);	
	}
	$obElement=new CElement();
	/* обратились к элементу */
	$arElmFilter['active']=1;
	if($data['main_content'] = $obElement->GetRecord($arElmFilter))
	{
		/* Неплохо бы добавить дату добавления в понятном формате, чтобы юзеры в Смарти не мучились :) */
		$data['main_content']['date'] = date("d.m.Y", $data['main_content']['date_add']);
		
		//Проверка прав доступа
		if($access_level>8) throw new CAccessError("CATSUBCAT_NOT_VIEW_ELEMENTS");
		if($access_level>7)
		{
			if(!in_array($data['main_content']['access_view'],$USER->GetGroups()))
			{
				throw new CAccessError("CATSUBCAT_NOT_VIEW_ELEMENT");
			}
		}
		if($sUrl=='')
		{
			$sUrl='/'.$data['main_content']['text_ident'].'.html';
			$obCategory=new CCategory();
			$parent_id=$data['main_content']['parent_id'];
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
		else
		{
			$sUrl.='/'.$data['main_content']['text_ident'].'.html';
		}
		$smarty->assign('data', $data);
		$smarty->assign('url',$sUrl);
		if($params['addToNavChain']=='Y' && $KS_MODULES->arModules['navigation']['active']==1)
			CNNavChain::get_instance()->Add( $data['main_content']['title'],$sUrl);
		if($params['setPageTitle']=='Y')
		{
			$sTitle=$data['main_content']['seo_title']!=''?$data['main_content']['seo_title']:$data['main_content']['title'];
			$smarty->assign('TITLE',($sTitle!=''?$sTitle:$KS_MODULES->GetConfigVar('catsubcat','title_default','Текстовые страницы')));
			$smarty->assign('DESCRIPTION',$data['main_content']['seo_description']);
			$smarty->assign('KEYWORDS',$data['main_content']['seo_keywords']);
		}
		$sResult=$KS_MODULES->RenderTemplate($smarty,'/catsubcat/CatElement',$params['global_template'],$params['tpl']);
		/* Шаблон найдем, можно увеличить количество показов страницы на единицу */
		if($_COOKIE['cscp'.$data['main_content']['id']]!='1')
		{
			$obElement->Update($data['main_content']['id'],array('views_count'=>intval($data['main_content']['views_count']) + 1));
			setcookie('cscp'.$data['main_content']['id'],1,time()+30000);
		}
		return $sResult;
	}
	else
	{
		throw new CHTTPError("SYSTEM_FILE_NOT_FOUND", 404);
	}
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
?>
