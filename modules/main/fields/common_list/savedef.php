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
	if($value!='')
	{
		$sValue=$value;
	}
	else
	{
		$sValue='';
	}
}

$arLists=array();
if(file_exists(MODULES_DIR.'/main/fields/common_list/values.php'))
	include_once(MODULES_DIR.'/main/fields/common_list/values.php');
if(strlen($_POST['CM_option_2'])>0)
{
	$arValues=explode("\n",$_POST['values']);
	if(count($arValues)>0)
	{
		$arRes=array();
		foreach($arValues as $key=>$value)
		{
			$arVal=explode('=',$value);
			if(count($arVal)>1)
			{
				if(strpos($arVal[0],':')>0)
				{
					$arKeys=explode(':',$arVal[0]);
					$arRes[$arKeys[0]][$arKeys[1]]=trim($arVal[1]);	
				}
				else
				{
					$arRes[$arVal[0]]=trim($arVal[1]);
				}
			}
			else
			{
				$arRes[]=trim($value);
			}
		}
		$arLists[$_POST['CM_option_2']]=$arRes;
	}
}
$arLists=ClearArray($arLists);
SaveToFile(MODULES_DIR.'/main/fields/common_list/values.php','$arLists',$arLists);
?>
