<?php
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}
include_once MODULES_DIR.'/guestbook2/libs/class.CGB2Api.php';

/**
 * Виджет отображения списка категорий сообщений гостевой книги
 * @filesource function.GB2Categories.php
 * @author BlaDe39 <blade39@kolosstudio.ru>
 * @version 2.5.4
 * @since 08.09.2010
 *
 */
function smarty_function_GB2Categories($params, &$subsmarty)
{
	/* Необходимые глобальные объекты и переменные */
	global $KS_IND_matches, $global_template,$USER,$KS_MODULES;
	try
	{
		/* Проверка прав доступа пользователя к анонсам */
		if ($USER->GetLevel('guestbook2') > KS_ACCESS_GB2_VIEW)
			throw new CAccessError("GB2_ACCESS_VIEW");

		/* Создаём объект для работы с категориями */
		$obGB2API=CGB2API::get_instance();

		if(IsTextIdent($KS_IND_matches[1][2]))
		{
			$sCurrent=$KS_IND_matches[1][2];
		}

		//Определяем сортировку
		if(in_array($params['sort'],$obGB2API->GetCategoryFields())) $sSort=$params['sort']; else $sSort='orderation';
		if($params['dir']=='asc') $sOrder='asc'; else $sOrder='desc';
		$arOrder[$sSort]=$sOrder;

		$arResultFilter=array();

		/* Выбираем разделы, для которых указанный является родительским */
		$arFilter = array("active" => 1);
		if($arList = $obGB2API->obCategories->GetList($arOrder,$arFilter))
		{
			$path=$KS_MODULES->GetSitePath('guestbook2');
			foreach($arList as $key=>$arRow)
			{
				$arList[$key]['url']=$path.$arRow['text_ident'].'/';
				if($arRow['text_ident']==$sCurrent)
				{
					$arResultFilter['category_id']=$arRow['id'];
					if($KS_MODULES->IsActive('navigation'))
						CNNavChain::get_instance()->Add($arRow['title'],$path.$arRow['text_ident'].'/');
					if($params['set_title']=='Y')
					{
						$subsmarty->assign('TITLE',$arRow['title']);
					}
				}
			}
			/* Отправляем данные в Смарти */
			$subsmarty->assign("list", $arList);
			$sResult=$KS_MODULES->RenderTemplate($subsmarty,'/guestbook2/GB2Categories',$global_template,$params['tpl']);
		}
		if($params['assign']!='')
		{
			$subsmarty->assign($params['assign'],$arResultFilter);
		}
		return $sResult;
	}
	catch (CAccessError $e)
	{
		return $e;
	}
	catch (CError $e)
	{
		return $e;
	}
}

/**
 * Функция, определяющая возможные настраиваемые параметры виджета
 */
function widget_params_GB2Categories()
{
	$arSort=array(
		'title'=>'Название',
		'text_ident'=>'Текстовый идентификатор',
		'orderation'=>'Порядок сортировки'
	);
	$arFields = array
	(
		"sort" => array
		(
			"title" => "Сортировать по",
			"type" => "select",
			"value" => $arSort
		),
		'dir'=>array(
			"title" => "Направление сортировки",
			"type" => "select",
			"value" => array('asc'=>'По возрастанию','desc'=>'По убыванию')
		),
		'assign'=>array(
			'title'=>'Сохранить значение фильтра в переменную',
			'type'=>'text',
			'default'=>''
		),
	);

	return array
	(
		"fields" => $arFields
	);
}

?>