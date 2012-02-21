<?php
/**
 * Главный файл модуля текстовых страниц
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 * @since 25.03.2008
 */

/* Защита от взлома */
if (!defined("KS_ENGINE"))
	die("Hacking attempt!");

/* Глобальные переменные */
global $USER, $global_template, $smarty;

/* Идентификатор модуля */
$module = "catsubcat";
$access_level=$USER->GetLevel($module);
/* Проверка прав доступа пользователя */
if ($access_level >= 10)
	throw new CAccessError("SYSTEM_NOT_ACCESS_MODULE");

/* Установка директории с плагинами модуля для Смарти */
$smarty->plugins_dir[] = MODULES_DIR . "/" . $module . "/widgets/";

/* Чтение конфигурации модуля */
$module_config = $this->GetConfigArray($module);

/* Путь к корню модуля */
$root_path = $this->GetSitePath($module);

if ($root_path != "/")
{
	/* Добавляем элемент навигационной цепочки */
	if ($this->GetConfigVar("catsubcat", "show_nav_chain",'1'))
		CNNavChain::get_instance()->Add( $this->arModules[$module]['name'],$root_path);

	$sUrl = $root_path;
	$iBase = 2;
}
else
{
	/* Модуль является модулем по умолчанию */
	$sUrl = "/";
	$iBase = 1;
}

/* Устанавливать заголовок страницы или нет */
$module_parameters['setPageTitle'] = $this->GetConfigVar($module, "set_title",1) ==1 ? "Y" : "N";

/* Родительский раздел */
$module_parameters['parent_id'] = 0;

/* Формирование навигационной цепочки */
$arDirs=$this->GetPathDirs(0);
if (count($arDirs) > $iBase)
{
	/* Объект для работы с категориями */
	$obCategory = new CCategory();
	$arFilter = array('parent_id' => 0);

	for ($i = $iBase; $i < count($arDirs); $i++)
	{
		$arFilter['text_ident'] = $arDirs[$i];
		$arCategory = $obCategory->GetRecord($arFilter);
		if($arCategory)
		{
			$arFilter['parent_id'] = $arCategory['id'];
			$sUrl .= $arCategory['text_ident'] . "/";
			if ($module_config['show_nav_chain'] == "1" && $this->IsActive("navigation"))
				CNNavChain::get_instance()->Add( $arCategory['title'],$sUrl);
			$module_parameters['parent_id'] = $arCategory['id'];
		}
		else
			throw new CHTTPError("SYSTEM_SECTION_NOT_FOUND", 404);
	}
}
/* Определение виджета для подключения в качестве контента страницы */
if($this->IsPage() && $this->CurrentTextIdent()!='index')
{
	/* Элемент каталога */
	$module_parameters['text_ident'] = $this->CurrentTextIdent();
	$module_parameters['setPageTitle']=$this->GetConfigVar('catsubcat','set_title',1)==1?'Y':'N';
	$res=$this->IncludeWidget($module,'CatElement',$module_parameters);
}
elseif(array_key_exists('type',$_GET) && $_GET['type'] == "rss")
{
	/* RSS */
	$module_parameters['tpl'] = "RSS";
	$module_parameters['sort_by'] = $this->GetConfigVar($module, "sort_by",'id');
	$module_parameters['sort_dir'] = $this->GetConfigVar($module, "sort_dir",'asc');
	$module_parameters['announces_count'] = $this->GetConfigVar($module, "count",'10');
	$module_parameters['parent_id'] = $module_parameters['parent_id'];
	$module_parameters['select_from_children'] = $this->GetConfigVar($module, "select_from_children",'y');

	$res=$this->IncludeWidget($module,'CatAnnounce',$module_parameters);
	$output['include_global_template'] = 0;
}
else
{
	/* Категория */
	$module_parameters['ID'] = $module_parameters['parent_id'];
	$res=$this->IncludeWidget($module,'CatCategory',$module_parameters);
}
/* Возвращаем результат работы */
$output['main_content'] = $res;

