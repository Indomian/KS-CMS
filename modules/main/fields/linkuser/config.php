<?php
/**
 * \file config.php
 * Файл для конфигурации поля типа Текст, выводит параметры для настройки длины поля
 * Файл проекта kolos-cms.
 * 
 * Создан 16.09.2009
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 1.0
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

$sResult='<table class="layout">' .
		'<tr>' .
			'<td>Значение по умолчанию: </td>' .
			'<td>';
$value=$params['field']['default'];
$myData=explode('|',$value);
$rand=rand(100000,999999);
$sResult.="<input type=\"text\" name=\"id".$rand."\" id=\"id".$rand."\" value=\"".$myData[1]."\">[<span id=\"module".$rand."\">".$myData[0]."</span>]\n";
$sResult.='<input type="hidden" name="'.$params['prefix']."ext_".$params['field']['title'].'" id="'.$params['prefix']."ext_".$params['field']['title'].'" value="'.$params['value'].'" style="width:100%"/>';
$sResult.='<input type="button" id="select'.$rand.'" name="select" value="..."></td></tr></table>';
$sResult.='<script type="text/javascript">' ."\n".
		'function showForm'.$rand.'(e,data){'."\n".
			'var obData=$(":checkbox[name=\'sel\[elm\]\[\]\']",data);'."\n".
			'for(var i=0;i<obData.length;i++)'."\n".
			'{'."\n".
				'obData.eq(i).replaceWith($(\'<input type="button" name="\'+obData.eq(i).attr(\'value\')+\'" kstitle="\'+obData.eq(i).next("input[type=hidden]").eq(0).val()+\'" value="Выбрать"/>\').click(' ."\n".
				'function(event){'."\n".
					'$("#id'.$rand.'").val($(this).attr("kstitle"));' ."\n".
					'$("#module'.$rand.'").html($(this).attr("name"));' ."\n".
					'$("#'.$params['prefix']."ext_".$params['field']['title'].'").val(this.name);
					kstb_remove();
					})' ."\n".
				');' ."\n".
			'}' ."\n".
			'$("#navChain>:first-child",data).remove();'."\n".
			'$(document).trigger("InitCalendar");'."\n".
			'if($("#'.$params['prefix'].'option_1").val()=="blog") $("#navChain>:first-child",data).remove();'.
			'$(document).trigger("InitTiny");'.	"\n".
		'}'.
		'$(document).ready(function(){' .
		'$("input#select'.$rand.'").click(function(event){'."\n".
			'kstb_show("Выбрать пользователя","/admin.php?module=main&modpage=users&mode=small&width=800&height=480",null,showForm'.$rand.');'.
		'})});'.
	'</script>';
?>
