<?php
/**
 * \file function.Highlight.php
 * Функция выполняет вывод строки для подсветки ячейки, цвет меняется только в том случае, если
 * включен режим подсветки, а также дата редактирования элемента имеет достаточно небольшое значение
 * Файл проекта kolos-cms.
 * 
 * Создан 02.07.2009
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 1.0
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

/**
 * Доступные ключи массива $params - date - дата изменения записи в формате unix_timestamp;
 */
function smarty_function_Highlight($params,&$smarty)
{
	global $KS_MODULES;
	static $_highlight_hsv;
	if($params['assign']!='') $smarty->assign($params['assign'],'');
	if($KS_MODULES->GetConfigVar('main','highlight_new_elements','my')=='no') return '';
	if(!array_key_exists('date',$params)) return '';
	if($KS_MODULES->GetConfigVar('main','highlight_time',300)<1) return '';
	if(!$_highlight_hsv) $_highlight_hsv=rgb2hsv($KS_MODULES->GetConfigVar('main','highlight_color','fae36f'));
	$diff=time()-intval($params['date']);
	if($diff>$KS_MODULES->GetConfigVar('main','highlight_time',300)) return '';
	$newhsv=$_highlight_hsv;
	$k=$diff/$KS_MODULES->GetConfigVar('main','highlight_time',300);
	$newhsv[1]-=$newhsv[1]*$k;
	$arResult=hsv2rgb($newhsv);
	/*if(array_key_exists('i',$params))
	{
		if($params['i']%2>0)
		{
			$backcolor=rgb2hsv($KS_MODULES->GetConfigVar('main','highlight_odd_row_color'));
			$backcolor[1]-=$backcolor[1]*(1-$k);
			$backcolor=hsv2rgb($backcolor);
			foreach($arResult as $key=>$value)
			{
				$arResult[$key]=($value+$backcolor[$key])/2;
			}
		}
	}*/
	$sResult=' style="border-bottom:1px solid white;background-color:#'.dechex(round($arResult['r']*65536)).dechex(round($arResult['g']*65536)).dechex(round($arResult['b']*65536)).'" _ksHighlight="1" ';
	if($params['assign']!='') $smarty->assign($params['assign'],$sResult);
	return $sResult;
}
?>
