<?php
/**
 * Файл построения дерева модуля catsubcat для CMS Lite
 *
 * Файл генерирует содержимое дерева сайта (т.е. путей модуля). Файл должен
 * возвращать массив $arMyTree, по структуре сходный с массивом $arTree
 * \sa /modules/main/pages/main.php
 * В файле доступна переменая $arRow в которой содержаться данные о подключении
 * этого модуля.
 * В переменную $arRow передадутся ВСЕ данные переданные в параметре
 * $arMyRow['ajax_req'] будут в виде ассоциативного массива, а также все данные о
 * модуле полученные из базы данных
 *
 * @filesource .tree.php
 * @author blade39 <blade39@kolosstudio.ru>, Ilya Doroshko <ilya@kolosstudio.ru>, north-e <pushkov@kolosstudio.ru>
 * @since 29.11.2008
 * @version 1.0
 */

/* Обязательно вставляем во все файлы для защиты от взлома */
if (!defined("KS_ENGINE"))
	die("Hacking attempt!");

/* Подключаем необходимые файлы */
include_once MODULES_DIR.'/main/libs/class.Tree.php';
include_once MODULES_DIR.'/catsubcat/libs/functions.php';
include_once MODULES_DIR.'/catsubcat/libs/class.CCategory.php';
include_once MODULES_DIR.'/catsubcat/libs/class.CElement.php';

/* Устанавливаем список таблиц */
$arTables = array(
	'element'=>new CElement(),
	'category'=>new CCategory()
);
$userLevel=$USER->GetLevel('catsubcat');
/* Специальный класс с функциями дерева */
$obCategory=$arTables['category'];
if(!array_key_exists('parent_id',$arRow))
{
	$arRow['parent_id']=0;
}
/* Получаем список элементов */
$arResult = GetAllList(array("orderation" => "asc"), array("parent_id" => $arRow["parent_id"]), array(0, 1000), $arTables);

/* Основные параметры для строки модуля в динамическом дереве */
$modTreeSettings = array
(
	/* Управление страницами */
	"admin_url" => "?module=catsubcat",

	/* Просмотр страниц в пользовательской части */
	//"watch_url" => "/" . $arRow["directory"] . "/",

	/* Иконка корня */
	"ico" => "/uploads/templates/admin/images/icons_tree/catsubcat.gif",

	/* Ссылки на добавление нового раздела и нового элемента */
	"add_cat_url" => "?module=catsubcat&ACTION=new&CSC_catid=" . $arRow["parent_id"],
	"add_elm_url" => "?module=catsubcat&ACTION=new&CSC_catid=" . $arRow["parent_id"],

	"add_cat_text" => "Раздел",
	"add_elm_text" => "Страница"
);
$arSection=$obCategory->GetRecord(array('id'=>$arRow['parent_id']));
if($userLevel>1)
if(
	($userLevel>2)
	||
	!(
		($userLevel==2)&&
		in_array($arSection['access_create'],$USER->GetGroups())
	 )
  )
{
	unset($modTreeSettings["add_cat_url"]);
	unset($modTreeSettings["add_elm_url"]);
}
if($userLevel>0)
{
	unset($modTreeSettings["admin_url"]);
}

/* Обнуляем массив возврата */
$arMyTree = array();

if($arResult["TOTAL"] != 0)
{
	$catsubcat_items = array();
	$current_type = 'cat';
	$main_page = false;
	foreach($arResult["ITEMS"] as $arItem)
	{
		if ($arItem['TYPE'] != $current_type)
			if ($main_page)
				$catsubcat_items[] = $main_page;

		$current_type = $arItem['TYPE'];

		if (intval($arItem["id"]) == 0 && (intval($arItem["parent_id"])) == 0)
			$main_page = $arItem;
		else
			$catsubcat_items[] = $arItem;
	}
	//Решения бага с исчезновение главной страницы при отсутствии страниц в корне сайта
	if(($current_type=='cat')&&is_array($main_page))
	{
		$catsubcat_items[]=$main_page;
	}

	$obCategory=new CCategory();
	$obPathTree=$obCategory->GetParents($arRow['parent_id']);
	//Получаем адрес модуля
	$module_url_ident = $this->arModules['catsubcat']["URL_ident"];
	if ((strlen($module_url_ident)>0) && ($module_url_ident != "default"))
		$root_path = '/'.$module_url_ident.'/';
	else
		$root_path = '/';
	$full_path=$obPathTree->GetFullPath($root_path);


	foreach($catsubcat_items as $arItem)
	{
		if(
			($userLevel<8)
			||
			(
				($userLevel<10)
				&&
				in_array($arItem['access_view'],$USER->GetGroups())
			)
		)
		{
			$arMyRow = array();
			$arMyRow["title"] = $arItem["title"];
			$arMyRow["path"] = $arItem["text_ident"];
			$arMyRow["module"] = "catsubcat";
			$arMyRow['active']=$arItem['active'];
			if ($arItem["TYPE"] == "cat")
			{
				/* Иконка */
				if (intval($arItem["id"]) > 0)
					$arMyRow["ico"] = "/uploads/templates/admin/images/icons_tree/folder.gif";
				else
					$arMyRow["ico"] = "/uploads/templates/admin/images/icons_tree/page_csc.gif";

				/* Тип - раздел */
				$arMyRow["type"] = "folder";

				/* Ссылка на редактирование раздела */
				if($userLevel<4)
				{
					if(
						(
							($userLevel==3) &&
							in_array($arItem['access_edit'],$USER->GetGroups())
						)
						||
						(
							$userLevel<3
						)
					)
					{
						/* Ссылка на редактирование раздела */
						$arMyRow["admin_url"] = "?module=catsubcat&ACTION=edit&CSC_catid=" . $arItem["id"] . "&type=cat";
						/* Ссылка на удаление раздела (смотрим, чтобы сюда не попал родительский) */
						if ($arItem["id"] > 0)
							$arMyRow["delete_url"] = "&ACTION=delete&CSC_catid=" . $arItem["id"] . "&type=cat";
					}
				}

				/* Ссылка просмотра раздела в пользовательской части */
				$arMyRow["watch_url"] = $full_path.($arItem['text_ident']!=''?$arItem["text_ident"] . "/":'');

				/* Ссылка для разворачивания вложенных пунктов меню, обязательно в следущем формате:
				 * <переменнная>=<значение>|<переменная 2>=<значение> */
				if (!($arItem["id"] == 0 && $arItem["parent_id"] == 0))
					$arMyRow["ajax_req"] = base64_encode("parent_id=" . $arItem["id"] . "|module=catsubcat");
			}
			else
			{
				/* Иконка */
				$arMyRow["ico"] = "/uploads/templates/admin/images/icons_tree/page_csc.gif";

				/* Тип - элемент */
				$arMyRow["type"] = "file";
				/* Ссылка на редактирование раздела */
				if($userLevel<4)
				{
					if(
						(
							($userLevel==3) &&
							in_array($arItem['access_edit'],$USER->GetGroups())
						)
						||
						(
							$userLevel<3
						)
					)
					{
						/* Ссылка на редактирование элемента */
						$arMyRow["admin_url"] = "?module=catsubcat&ACTION=edit&CSC_id=" . $arItem["id"] . "&CSC_catid=" . $arItem["parent_id"] . "&type=elm";
						/* Ссылка на удаление элемента */
						$arMyRow["delete_url"] = "&ACTION=delete&CSC_id=" . $arItem["id"] . "&type=elm";
					}
				}

				/* Ссылка просмотра элемента в пользовательской части */
				$arMyRow["watch_url"] = $full_path.$arItem['text_ident'].'.html';
			}

			$arMyTree[]=$arMyRow;
		}
	}
}
?>