<?php
/**
 * \file class.CSitemap.php
 * Сюда сделать описание файла
 * Файл проекта kolos-cms.
 *
 * Создан 12.12.2009
 *
 * \author blade39
 * \version
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

/**
 * Класс для работы с картой сайта
 */
class CSitemap extends CBaseObject
{
	function GetModuleMap($module,$level=0,$max_level=5,$ajax_request='')
	{
		global $KS_MODULES;
		if($level>$max_level) return false;
		/* Получаем строку запроса ajax */
		$sReq = base64_decode($ajax_request);
		/* Массив запроса */
		$arParams = explode("|", $sReq);
		/* Формуруем массив переменных, переданных через запрос */
		$arRow = array();
		foreach($arParams as $item)
		{
			$arRos = explode("=", $item);
			if(count($arRos)==2)
				$arRow[$arRos[0]] = $arRos[1];
			else
				$arRow[$item]='';
		}
		$arTree=array();
		if($arData=$KS_MODULES->IncludeTreeFile($module,$arRow))
		{
			$arResult=array();
			foreach($arData['tree'] as $key=>$arItem)
			{
				$arItem['level']=$level;
				$arResult[]=$arItem;
				if($arItem['type']=='folder' && isset($arItem['ajax_req']))
				{
					if($arSubData=$this->GetModuleMap($module,$level+1,$max_level,$arItem['ajax_req']))
					{
						foreach($arSubData as $key=>$value)
						{
							//$value['level']=$level+1;
							$arResult[]=$value;
						}
					}
				}
			}
			return $arResult;
		}
		return false;
	}
}
?>
