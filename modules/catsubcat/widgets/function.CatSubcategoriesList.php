<?php

/**
 * Виджет отображения списка вложенных разделов
 *
 * Выводит список вложенных разделов для заданного раздела
 *
 * @filesource function.CatSubcategoriesList.php
 * @author North-E <pushkov@kolosstudio.ru>
 * @version 1.0
 * @since 01.06.2009
 *
 * 1. Добавлена выборка из вложенных разделов.
 * 2. Добавлена постраничная навигация
 */

/**
 * Функция, возвращающая шаблон списка вложенных разделов
 *
 * @param array $params Массив входных параметров для виджета
 * @param object $subsmarty Ссылка на объект Смарти
 * @return string
 */
function smarty_function_CatSubcategoriesList($params, &$subsmarty)
{
	/* Необходимые глобальные объекты и переменные */
	global $KS_URL, $USER, $KS_MODULES;

	try
	{
		/* Проверка прав доступа пользователя к анонсам */
		if ($USER->GetLevel('catsubcat') > 8)
			throw new CAccessError("CATSUBCAT_NOT_VIEW_ANNOUNCE");

		/* Подключаем необходимые библиотеки */
		$module_directory = MODULES_DIR . "/catsubcat/";
		include_once($module_directory . "libs/class.CCategoryEdit.php");

		/* Создаём объект для работы с разделами */
		$obCategory = new CCategory();

		$parent_id = intval($params["parent_id"]);
		$orderby = $params["orderby"];

		if ($parent_id >= 0)
		{
			/* Определяем принцип сортировки */
			if ($orderby != "random")
				$arOrder = array($orderby => "desc");
			else
				$arOrder = array("RAND()" => "desc");

			/* Выбираем разделы, для которых указанный является родительским */
			$arFilter = array("parent_id" => $parent_id, "active" => 1, '?!parent_id'=>'id');

			/* Получаем список вложенных разделов */
			$shift=0;
			if($params['shift'])
			{
				$shift=$params['shift'];
			}
			if($params['count'])
				$subcategories = $obCategory->GetList($arOrder, $arFilter,array($shift,$params['count']));
			else
				$subcategories = $obCategory->GetList($arOrder, $arFilter);
			$arCache=array();
			if (is_array($subcategories))
				if (count($subcategories) > 0)
					foreach ($subcategories as $subcategory_key => $subcategory)
					{
						/* Дата добавления в отформатированном виде */
						$subcategories[$subcategory_key]['date'] = date("d.m.Y", $subcategory['date_add']);
						if(!array_key_exists($subcategory_key,$arCache))
						{
							/* Определяем полный путь к подразделу */
							$arCache[$subcategory_key]=$obCategory->GetFullPath($subcategory['parent_id']);
						}
						$subcategories[$subcategory_key]['full_path'] =
							$arCache[$subcategory_key].$subcategory['text_ident'].'/';
					}

			/* Отправляем данные в Смарти */
			$subsmarty->assign("subcategories", $subcategories);
		}
		else
			$subsmarty->assign("subcategories_error", "CATSUBCAT_ID_ERROR");

		/* Определение глобального шаблона */
		$sTemplate = (strlen($params['global_template']) ==0) ? $global_template : $params['global_template'];

		/* Определение второй части пути к локальному шаблону */
		$sLocalTemplate = '/catsubcat/CatSubcategoriesList' . (isset($params['tpl']) ? $params['tpl'] : '') . '.tpl';

		/* Проверяем наличие шаблона */
		if($subsmarty->template_exists($sTemplate . $sLocalTemplate))
			return $subsmarty->fetch($sTemplate . $sLocalTemplate);
		elseif($subsmarty->template_exists('.default' . $sLocalTemplate))
			return $subsmarty->fetch('.default' . $sLocalTemplate);
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
function widget_params_CatSubcategoriesList()
{
	/* Подключение необходимой библиотеки */
	include_once(MODULES_DIR . "/catsubcat/libs/class.CCategoryEdit.php");

	/* Создание объекта для работы с разделами текстовых страниц */
	$obCategory = new CCategory();

	/* Строим дерево всех разделов текстовых страниц для вывода их в качестве списка */
	$expanded_tree = $obCategory->GetExpandedTree();

	/* Формируем данные для списка дерева разделов */
	$parent_select = array();
	if (is_array($expanded_tree) && count($expanded_tree))
		foreach ($expanded_tree as $leaf)
		{
			$spaces = "";
			for ($q = 0; $q < $leaf["level"] * 4; $q++)
				$spaces .= "&nbsp;";
			$parent_select[$leaf["id"]] = $spaces . $leaf["title"];
		}

	/* Массив с возможными значениями количества анонсов в блоке */
	$announces_values = array();
	for ($i = 1; $i <= 10; $i++)
		$announces_values[$i] = $i;

	$arFields = array
	(
		"parent_id" => array
		(
			"title" => "Идентификатор раздела, для которого строится список вложенных разделов",
			"type" => "select",
			"value" => $parent_select
		),
		"orderby" => array
		(
			"title" => "Принцип сортировки",
			"type" => "select",
			"value" => array("date_add" => "По дате добавления", "views_count" => "По количеству просмотров", "random" => "В случайном порядке")
		),
		"count" => array
		(
			"title" => "Количество записей на страницу",
			"type" => "select",
			"value" => $announces_values,
			"default_value" => 3
		),
		"shift"=>array(
			'title'=>'Начинать с записи №',
			'type'=>'text',
			'value'=>'0',
			'default_value'=>'0',
		)
	);

	return array
	(
		"fields" => $arFields
	);
}

?>