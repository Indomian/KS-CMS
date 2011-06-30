<?php
/**
 * \file function.sitemap.php
 * Виджет выводит карту сайта по настройкам модуля
 * Файл проекта CMS-local.
 *
 * Создан 24.11.2008
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 0.1
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/sitemap/libs/class.CSitemap.php';
include_once MODULES_DIR.'/main/libs/class.CPHPCache.php';

/**
 * Функция производит вывод комментариев для определенного модуля.
 * \param $params массив параметров.
 * \param $subsmarty - указатель на объект смарти.
 * Параметры могут быть следующими:
 * 	themeId - номер темы в которой хранятся комментарии
 * 	count - количество выводимых элементов
 */
function smarty_function_sitemap($params,&$subsmarty)
{
	global $USER,$ks_db,$KS_MODULES,$KS_URL;
	$cacheId='sitemap'.join('_',$USER->GetGroups());
	$obCache=new CPHPCache($cacheId,$KS_MODULES->GetConfigVar('sitemap','cacheTime'),'sitemap');
	if(!$obCache->Alive())
	{
		$arModules=$KS_MODULES->GetConfigVar('sitemap','modules');
		$arItems = $KS_MODULES->GetList(array("URL_ident"=>'asc'), array("active" => 1,'->directory'=>array_keys($arModules)));
		/* Формирование дерева сайта */
		$arTree = array();
		/* Список элементов уровня */
		$arTree["list"] = array();
		/* Опции уровня */
		$arTree["ui"] = array();
		$obTree=new CSitemap();
		foreach($arItems as $key=>$arModule)
		{
			if($USER->GetLevel($arModule['directory'])<10)
			{
				if ($arModule["URL_ident"] == "default")
				{
					$arTree['list']=array_merge($arTree['list'],$obTree->GetModuleMap($arModule['directory'],0,$arModules[$arModule['directory']]));
				}
				elseif($arModule["URL_ident"] != "")
				{
					/* Остальные модули представляем свёрнутыми */
					$arData=$KS_MODULES->IncludeTreeFile($arModule['directory']);
					$arModRow = array();
					$arModRow["title"] = $arModule["name"];
					$arModRow["path"] = $arModule["URL_ident"];
					$arModRow["type"] = "folder";
					$arModRow["module"] = $arModule["directory"];
					$arModRow["ico"] = $arData['settings']["ico"];
					$arModRow["watch_url"] = isset($arData['settings']["watch_url"]) ? $arData['settings']["watch_url"] : '/'.$arModule['URL_ident'].'/';
					$arModRow["ajax_req"] = base64_encode("parent_id=0|module=" . $arModule["directory"]);
					$arModRow['active']=1;
					$arTree["list"][] = $arModRow;
					if($arSubItems=$obTree->GetModuleMap($arModule['directory'],1,$arModules[$arModule['directory']],$arModRow['ajax_req']))
						$arTree['list']=array_merge($arTree['list'],$arSubItems);
				}
			}
		}
		$obCache->SaveToCache($arTree);
	}
	else
	{
		$arTree=$obCache->GetData();
	}
	$subsmarty->assign('data',$arTree);
	//Код для генерации пути к шаблону или вывод ошибки об отсутсвтии шаблона
	return $KS_MODULES->RenderTemplate($subsmarty,'/sitemap/sitemap',$params['global_template'],$params['tpl']);
}

/**
 * Функция для настройки параметров виджета
 */
function widget_params_sitemap()
{
	$arFields = array
	(
	);

	return array
	(
		'fields' => $arFields
	);
}
?>