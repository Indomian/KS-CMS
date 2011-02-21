<?php
/**
 * \file function.MainHeadStrings.php
 * Виджет выводит заголовочные строки собранные в процессе обработки страницы
 * Файл проекта kolos-cms.
 *
 * Создан 27.05.2009
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 1.0
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

function smarty_function_MainHeadStrings($params,&$smarty)
{
	global $KS_MODULES;
	if(!is_object($KS_MODULES)) throw new CError("SYSTEM_NOT_FOUND_MANAGERS_MODULES");
	if(IS_ADMIN)
	{
		$result=$KS_MODULES->GetHeader();//join("\n",$KS_MODULES->arHeads);
	}
	else
	{
		$result='#HEAD_STRINGS#';
	}
	return $result;
}
?>
