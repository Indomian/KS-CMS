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

$sResult='<table class="layout"><tr><td width="30%">Выражение для проверки поля:</td>'.
		'<td width="70%"><input type="text" class="form_input" ' .
		'name="'.$params['prefix'].'option_1" ' .
		'value="'.$params['field']['option_1'].'" ' .
		'id="'.$params['prefix'].'option_1" />&lt;&lt;' .
		'<select id="'.$params['prefix'].'option_1_tpl">' .
				'<option value=".*">Любой текст</option>'.
				'<option value="^[a-z\.\-\_0-9]+@[a-z\.\-\_0-9]+\.[a-z]+$">Email</option>' .
				'<option value="^[0-9]+$">Целое число</option>' .
				'<option value="^[0-9]+\.[0-9]+">Десятичная дробь (европейская запись)</option>' .
				'<option value="^[0-9]+\,[0-9]+">Десятичная дробь (русская запись)</option>' .
		'</select></td></tr>' .
		'<tr><td>Значение по умолчанию: </td><td><input type="text" class="form_input" ' .
		'name="'.$params['prefix'].'ext_'.$params['field']['title'].'" '.
		'value="'.$params['field']['default'].'" ' .
		'id="'.$params['prefix'].'ext_'.$params['field']['title'].'" /></td></tr>' .
		'</table>'.
		'<script type="text/javascript">$(document).ready(function(){' .
			'$("#'.$params['prefix'].'option_1").keyup(function(event){' .
			'});' .
			'$("#'.$params['prefix'].'option_1_tpl").change(function(event){' .
				'$("#'.$params['prefix'].'option_1").val(this.value);'.
			'});' .
			'$("#'.$params['prefix'].'ext_'.$params['field']['title'].'").keyup(' ."\n".
				'function(event){' ."\n".
					'var regexp=new RegExp($("#'.$params['prefix'].'option_1").val());' ."\n".
					'regexp.ignoreCase=true;'."\n".
					'if(!regexp.test(this.value)) $(this).addClass("invalid"); else $(this).removeClass("invalid");'."\n".
					'return true;});});'."\n".
		'</script>';
?>
