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

$sResult='<table class="layout"><tr><td width="30%">Ограничить длину поля по длине (0 - не ограничивать): </td>' .
		'<td width="70%"><input type="text" class="form_input" ' .
		'name="'.$params['prefix'].'option_1" ' .
		'value="'.intval($params['field']['option_1']).'" ' .
		'id="'.$params['prefix'].'option_1" /></td></tr>' .
		'<tr><td>Значение по умолчанию: </td><td><input type="text" class="form_input" ' .
		'name="'.$params['prefix'].'ext_'.$params['field']['title'].'" '.
		'value="'.$params['field']['default'].'" ' .
		'id="'.$params['prefix'].'ext_'.$params['field']['title'].'" /></td></tr>' .
		'<tr><td colspan="2"><label>Использовать визуальный редактор: <input type="checkbox" ' .
		'name="'.$params['prefix'].'option_2" ' .
		'id="'.$params['prefix'].'option_2" ' .
		'value="1" '.
		($params['field']['option_1']==0||$params['field']['option_1']>256?'':'disabled="disabled" ').
		($params['field']['option_2']==1?'checked="checked"':'').'/></td></tr></table>'.
		'<script type="text/javascript">$(document).ready(function(){' .
			'$("#'.$params['prefix'].'option_1").keyup(function(event){' .
					'(/^[0-9]+$/i.test(this.value))?$(this).removeClass("invalid"):$(this).addClass("invalid");' .
					'if(parseInt(this.value)>256||parseInt(this.value)==0) $("#'.$params['prefix'].'option_2").attr("disabled",false).val(1); else $("#'.$params['prefix'].'option_2").attr("disabled",true).val(0);'.
			'});' .
			'$("#'.$params['prefix'].'ext_'.$params['field']['title'].'").keydown(' .
				'function(event){' .
					'if (event.which<30) return true;'.
					'var limit=$("#'.$params['prefix'].'option_1");' .
					'if(limit.val()>0){' .
						'if(this.value.length+1>limit.val()) return false;' .
					'}' .
					'return true;});});'.
		'</script>';
?>
