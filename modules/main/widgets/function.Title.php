<?php
/**
 * \file function.Title.php
 * Функция Title осуществляет вывод заголовка поля в административном разделе с дополнительной 
 * всплывающей подсказкой, если подсказка не найдена в конфигурационном файле, то она не выводиться
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

function smarty_function_Title($params,&$smarty)
{
	$hint=$smarty->get_config_vars('field_'.$params['field'].'_hint');
	if($params['title']=='')
	{
		$field=$smarty->get_config_vars('field_'.$params['field']);
	}
	else
	{
		$field=$params['title'];
	}
	if(strlen($hint)>0)
	{
		$sResult='<span id="hint_'.$params['field'].'" ' .
			'onmouseover="floatMessage.showMessage(document.getElementById(\'hint_'.$params['field'].'\'), \'' .
			$hint.'\', '.($params['width']>0?$params['width']:250).');" style="cursor: pointer;">'.
			$field.'</span>';
	}
	elseif(strlen($field)>0)
	{
		$sResult=$field;
	}
	else
	{
		$sResult=$params['field'];
	}
	return $sResult;
}
?>
