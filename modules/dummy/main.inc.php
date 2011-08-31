<?php
/**
 * @file dummy/main.inc.php
 * Главный файл модуля dummy
 *
 * Доступные переменные:
 * $arModule - массив описывающий модуль
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 * @since 31.08.2011
 */

/* Защита от взлома */
if (!defined("KS_ENGINE")) die("Hacking attempt!");

/* Глобальные переменные */
global $USER, $global_template, $smarty;

/* Идентификатор модуля */
$module = $arModule['directory'];
$access_level=$USER->GetLevel($module);
/* Проверка прав доступа пользователя */
if ($access_level >= 10) throw new CAccessError("SYSTEM_NOT_ACCESS_MODULE");

/* Установка директории с плагинами модуля для Смарти */
$smarty->plugins_dir[] = MODULES_DIR . "/" . $module . "/widgets/";

/* Путь к корню модуля */
$root_path = $this->GetSitePath($module);

if ($root_path != "/")
{
	/* Добавляем элемент навигационной цепочки */
	if ($this->GetConfigVar("dummy", "show_nav_chain",'1'))
		CNNavChain::get_instance()->Add($arModule['name'],$root_path);
	$sUrl = $root_path;
}
else
{
	/* Модуль является модулем по умолчанию */
	$sUrl = "/";
}

/* Определение виджета для подключения в качестве контента страницы */
if($this->IsPage() && $this->CurrentTextIdent()!='index')
{
	/* Элемент каталога */
	$res=$this->IncludeWidget($module,'dummy',$module_parameters);
}
else
{
	/* Категория */
	$module_parameters['type'] = 'category';
	$res=$this->IncludeWidget($module,'dummy',$module_parameters);
}
/* Возвращаем результат работы */
$output['main_content'] = $res;

