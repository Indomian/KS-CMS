<?php
/**
 * \file savedef.php
 * Файл для сохранения настроек поля текст по умолчанию
 * Файл проекта kolos-cms.
 * 
 * Создан 16.09.2009
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 1.0
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

$sType='char(255)';
if(is_array($value))
{
	if(count($value)<=$arField['option_1'])
	{
		$value=str_replace('>','&gt;',$value);
		$sValue=join('>',$value);
	}
	else
	{
		throw new CError("MAIN_MANY_VALUES_FOR_FIELD", 0, $arField['option_1']);
	}
}
else
{
	$sValue=$value;
}
?>
