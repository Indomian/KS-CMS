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
 
if($params['field']['option_1']>1) $sType='checkbox';else $sType='radio';
$arVal=explode('>',$params['value']);
$arValues=explode("\n",$params['field']['option_2']);
if(is_array($arValues)&&count($arValues)>0)
{
	foreach($arValues as $sItem)
	{
		$sItem=trim($sItem);
		$arItem=explode('=',$sItem);
		if(is_array($arItem)&&count($arItem)>1)
		{
			$value=array_shift($arItem);
			$sResult.='<label><input type="'.$sType.'" name="'.$params['prefix'].'ext_'.$params['field']['title'].'[]" value="'.$value.'"'.(in_array($value,$arVal)?' checked="checked"':'').'/> '.join('=',$arItem).'</label><br/>';
		}
		else
		{
			$sResult.='<label><input type="'.$sType.'" name="'.$params['prefix'].'ext_'.$params['field']['title'].'[]" value="'.$sItem.'"'.(in_array($sItem,$arVal)?' checked="checked"':'').'/> '.$sItem.'</label><br/>';
		}
	}
}
?>