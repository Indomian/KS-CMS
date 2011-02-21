<?php

/**
 * Основная функция вывода Виджетов. Принимает в качестве парметров имя виджета (модуля), который необходимо
 * подключить, в зависимости от параметров может не выводить ничего а просто проинициализировать модуль.
 * вызов в шаблоне описывается в следующем виде:
 * {widget name=catsubcat mode=element id=14} что означает вывести элемент номер 14 из модуля Текстовые страницы.
 * Список стандартных параметров:
 * name - задает имя подключаемого модуля;
 * mode - функция, которую необходимо выполнить, обычно связанно с самим модулем, если не указана выполняется
 * 		  функция заданная в модуле по умолчанию.
 * noprint	-	флаг (true|false) указывающий на необходимость разрешение/запрета вывода работы функции на экран.
 */

include_once MODULES_DIR.'/main/libs/class.CHTMLCache.php';

function smarty_function_widget($params, &$smarty)
{
	/* подключение модуля CMS */
	global $KS_MODULES;
	/* Запоминаем ранее установленные переменные других виджетов */
	$arOldVars=$smarty->get_template_vars();
	$params['is_widget'] = 1;
	try
	{
		if($params['html_cache']>0)
		{
			$params['html_cache']=intval($params['html_cache']);
			$cacheId=md5(join('',$params));
			$obCache=new CHTMLCache($cacheId,$params['html_cache'],$params['name']);
			if($obCache->Alive())
			{
				$sValue=$obCache->GetData();
			}
			else
			{
				$sValue = $KS_MODULES->IncludeWidget($params['name'],$params['action'], $params);
				$obCache->SaveToCache($sValue);
			}
		}
		else
		{
			$sValue = $KS_MODULES->IncludeWidget($params['name'],$params['action'], $params);
		}
		//Добавлена обработка глобальных переменных шаблона
		$arNewVars=$smarty->get_template_vars();
		if($arNewVars['TITLE']!=$arOldVars['TITLE']) $arOldVars['TITLE']=$arNewVars['TITLE'];
		if($arNewVars['SEO_TITLE']!=$arOldVars['SEO_TITLE']) $arOldVars['SEO_TITLE']=$arNewVars['SEO_TITLE'];
		if($arNewVars['SEO_KEYWORDS']!=$arOldVars['SEO_KEYWORDS']) $arOldVars['SEO_KEYWORDS']=$arNewVars['SEO_KEYWORDS'];
		if($arNewVars['SEO_DESCRIPTION']!=$arOldVars['SEO_DESCRIPTION']) $arOldVars['SEO_DESCRIPTION']=$arNewVars['SEO_DESCRIPTION'];
		if($arNewVars['KEYWORDS']!=$arOldVars['KEYWORDS']) $arOldVars['KEYWORDS']=$arNewVars['KEYWORDS'];
		if($arNewVars['DESCRIPTION']!=$arOldVars['DESCRIPTION']) $arOldVars['DESCRIPTION']=$arNewVars['DESCRIPTION'];

		if($params['noprint'] != true)
		{
			$smarty->assign($arOldVars);
			return $sValue;
		}
		else
		{
			$smarty->assign($arOldVars);
			return '<!-- widget '.$params['name'].' -->';
		}
	}
	catch (CError $e)
	{
		$smarty->assign($arOldVars);
		if($params['noErrors']=='Y')
			if($params['default']!='') return $params['default'];
			else return '';
		return $e;
	}
}

?>
