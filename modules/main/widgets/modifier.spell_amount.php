<?php
/**
 * @file modifier.spell_amount.php
 * Генерирует окончание слова количества элементов
 * Файл проекта kolos-cms.
 *
 * Создан 28.03.2011
 *
 * @author blade39 <blade39@kolosstudio.ru>
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

function smarty_modifier_spell_amount($val,$sEnds=false)
{
	$arEnds=array(
		'1'=>'',
		'2-5'=>'а',
		'def'=>'ов'
	);
	if(is_string($sEnds))
	{
		$arEndsTmp=explode(',',$sEnds);
		$arEnds=array(
			'1'=>$arEndsTmp[0],
			'2-5'=>$arEndsTmp[1],
			'def'=>$arEndsTmp[2]
		);
	}
	if($val>1000000) $val=$val%1000000;
	if($val>100000) $val=$val%100000;
	if($val>10000) $val=$val%10000;
	if($val>1000) $val=$val%1000;
	if($val>100) $val=$val%100;
	if($val==0) return $arEnds['def'];
	if($val==1) return $arEnds['1'];
	if($val<20)
	{
		if($val<5) return $arEnds['2-5'];
		else return $arEnds['def'];
	}
	else
	{
		$minor=$val%10;
		if($minor==1) return $arEnds['1'];
		if($minor==0) return $arEnds['def'];
		if($minor<5) return $arEnds['2-5'];
	}
	return $arEnds['def'];
}


