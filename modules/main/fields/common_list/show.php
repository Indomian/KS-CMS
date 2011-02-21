<?php
/**
 * Файл выполняет вывод дополнительного поля типа list.
 * Входные данные и доступные переменные
 * $params - массив в котором даны параметры выводимого поля, массив обладает следующими ключами:
 * 		value - значение хранящееся в базе данных, может быть пустым для новой записи
 * 		field - массив описывающий параметры поля, обладает следующими полями
 * 			title - текстовый идентификатор поля
 * 			description - подпись для поля ввода
 * 			default - значение по умолчанию в формате хранения поля
 * 			option_1 - количество доступных одновременных выборов для данного поля
 * 			option_2 - список значений выпадающего списка, каждое новое значение с новой строки (т.е. можно разбить строку
 * 						в массив по \n). Если необходима пара значение-подпись, то она реализуется в формате <value>=<title>.
 * 		
 * Выходные данные
 * $sResult - строковое значение содержащее html код посредством которого происходит отображение
 * 		поля на сайте.
 */
$arLists=array();
if(file_exists(MODULES_DIR.'/main/fields/common_list/values.php'))
	include(MODULES_DIR.'/main/fields/common_list/values.php');
//global $arLists;
//Сохраняем имя всех параметров	
$sFieldName=$params['prefix'].'ext_'.$params['field']['title'];
//Определяем значение
$arDefaultValue=explode('>',$params['value']);
$arValues=$arLists[$params['field']['option_2']];

$sResult='<fieldset><legend>Значения</legend><input type="hidden" name="'.$sFieldName.'[]" value=""/>';
if($params['field']['option_1']>1)
{
	$sType='checkbox';
}
else
{
	$sType='radio';
}
if(is_array($arValues)&&count($arValues)>0)
{
	foreach($arValues as $key=>$item)
	{
		if(is_array($item))
		{
			$sResult.='<fieldset id="set_'.$key.'"><legend>'.$key.'</legend>';
			foreach($item as $skey=>$sitem)
			{
				$sResult.='<label><input type="'.$sType.'" name="'.$sFieldName.'[]" value="'.$key.':'.$skey.'"'.(in_array($key.':'.$skey,$arDefaultValue)?' checked="checked"':'').'> '.$sitem.'</label><br/>';
			}
			$sResult.='</fieldset>';
		}
		else
		{
			$sResult.='<label><input type="'.$sType.'" name="'.$sFieldName.'[]" value="'.$key.'"'.(in_array($key,$arDefaultValue)?' checked="checked"':'').'> '.$item.'</label><br/>';	
		}
	}
}
$sResult.='</fieldset>';
?>