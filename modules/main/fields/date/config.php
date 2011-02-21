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
				'<td width="30%">' .
					'Формат отображения времени:<br/>' .
					'<small><a href="http://ru2.php.net/manual/en/function.strftime.php" target="_blank">Формат</a>'.
				'</td>'.
				'<td width="70%">' .
					'<input type="text" class="form_input" ' .
						'name="'.$params['prefix'].'option_1" ' .
						'value="'.$params['field']['option_1'].'" ' .
						'id="'.$params['prefix'].'option_1" />&lt;&lt;' .
					'<select id="'.$params['prefix'].'option_1_tpl">' .
						'<option value="">Ручной ввод</option>'.		
						'<option value="%d.%m.%Y %H:%M">ДД.ММ.ГГГГ ЧЧ:ММ</option>'.
					'</select>' .
				'</td>' .
			'</tr>' .
			'<tr>' .
				'<td>Значение по умолчанию: </td>' .
				'<td><input type="text" class="form_input" ' .
					'name="'.$params['prefix'].'ext_'.$params['field']['title'].'" '.
					'value="'.$params['field']['default'].'" ' .
					'id="'.$params['prefix'].'ext_'.$params['field']['title'].'" />' .
				'</td>' .
			'</tr>' .
		'</table>'.
		'<script type="text/javascript">$(document).ready(function(){' .
			'$("#'.$params['prefix'].'option_1_tpl").change(function(event){' .
				'$("#'.$params['prefix'].'option_1").val(this.value);'.
			'});' .
			'});'."\n".
		'</script>';
?>
