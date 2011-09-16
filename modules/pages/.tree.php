<?php
/**
 * @file .tree.php
 * Файл построения дерева модуля dummy
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @since 31.08.2011
 * @version 2.6
 */

/* Обязательно вставляем во все файлы для защиты от взлома */
if (!defined("KS_ENGINE")) die("Hacking attempt!");

/* Подключаем необходимые файлы */
include_once MODULES_DIR.'/main/libs/class.Tree.php';

/* Основные параметры для строки модуля в динамическом дереве */
$modTreeSettings = array
(
	"admin_url" => "?module=dummy", /* Управление страницами */
	//"watch_url" => "/" . $arRow["directory"] . "/", /* Просмотр страниц в пользовательской части */
	"ico" => "/uploads/templates/admin/images/icons_tree/catsubcat.gif",/* Иконка корня */
	/* Ссылки на добавление нового раздела и нового элемента */
	"add_cat_url" => "?module=dummy",
	"add_elm_url" => "?module=dummy",
	"add_cat_text" => "Раздел",
	"add_elm_text" => "Страница"
);

/* Обнуляем массив возврата */
$arMyTree = array();

$arMyRow = array();
$arMyRow["title"] = "Вложенный";
$arMyRow["path"] = "dummy";
$arMyRow["module"] = "dummy";
$arMyRow['active']=1;
$arMyRow["ico"] = "/uploads/templates/admin/images/icons_tree/folder.gif";
$arMyRow["type"] = "folder";
$arMyRow["admin_url"] = "?module=dummy";
$arMyRow["delete_url"] = "";
$arMyRow["watch_url"] = "";
$arMyTree[]=$arMyRow;