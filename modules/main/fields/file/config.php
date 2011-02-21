<?php
/**
 * \file config.php
 * Файл для конфигурации поля типа файл, выводит параметры для настройки длины поля
 * Файл проекта kolos-cms.
 * 
 * Создан 16.09.2009
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 1.0
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}
$max_size=ini_get('upload_max_filesize');
$t=strtolower(substr($max_size,-1));
$max_size=(int)$max_size;
$m = array("b"=>1, "k"=>1024, "m"=>1048576, "g"=>1073741824);
$size=$params['field']['option_1'];
$a = array("b", "k", "m", "g");
if(!in_array($t,$a))
{
  $t='b';
}
$pos = 0;
while($size >= 1024)
{
	$size /= 1024;
	$pos++;
}
$type=$a[$pos];
$size = round($size,2);
if(($size*$m[$type])>($max_size*$m[$t])) {$size=$max_size; $type=$t;}
$sResult='<table class="layout"><tr><td width="30%">Ограничить размер файла (0 - не ограничивать или предельный размер в настройках ПХП ('.$max_size.$t.')): </td>' .
		'<td width="70%"><input type="text" class="form_input" ' .
		'name="'.$params['prefix'].'option_1" ' .
		'value="'.$size.'" ' .
		'id="'.$params['prefix'].'option_1" /><select name="numberSize" id="sizeType"><option value="1">Байт</option><option value="k"'.($type=='k'?' selected="selected"':'').'>КБайт</option><option value="m"'.($type=='m'?' selected="selected"':'').'>МБайт</option></select></td></tr>' .
		'<tr><td>Допустимые расширения файлов (каждое с новой строки): </td><td>' .
		'<textarea class="form_input" style="width:100px;"' .
		'name="'.$params['prefix'].'option_2" '.
		'id="'.$params['prefix'].'option_2">'.$params['field']['option_2'].'</textarea><select name="fileType" id="fileType">' .
				'<option value=""></option>'.
				'<option value="jpg\npng\ngif\nbmp\njpeg">Картинки</option>' .
				'<option value="zip\nrar\n7zp\ntar.gz\ncab">Архивы</option>' .
				'<option value="doc\notd\ntxt\nxls">Документы</option>' .
				'</select></td></tr>' .
		'</table>'.
		'<script type="text/javascript">$(document).ready(function(){' .
			'$("#fileType").change(' .
				'function(event){' .
					'$("#'.$params['prefix'].'option_2").get(0).value=this.value.replace(/\\\\n/gi,"\n")' .
					'});'.
			'$("#'.$params['prefix'].'option_1").change(' .
				'function(event){' .
					'checkSize();'.
					'});'.
			'$("#sizeType").change(' .
				'function(event){' .
					'checkSize();'.
					'});'.
					'function checkSize(){'.
					'mSize='.$max_size*$m[$t].';'.

					'switch($("#sizeType").get(0).value){'.
					  'case "1": mSize=mSize/'.$m['b'].'; break;'.
					  'case "k": mSize=mSize/'.$m['k'].'; break;'.
					  'case "m": mSize=mSize/'.$m['m'].'; break;'.
					  'case "g": mSize=mSize/'.$m['g'].'; break;'.
					'}'.
					'if(($("#'.$params['prefix'].'option_1").get(0).value>mSize))'.
					'{'.
					  '$("#'.$params['prefix'].'option_1").get(0).value='.$max_size.';'.
					  't="'.$t.'";'.
					  'switch(t){'.
					    'case "b": $("#sizeType").get(0).selectedIndex=0; break;'.
					    'case "k": $("#sizeType").get(0).selectedIndex=1; break;'.
					    'case "m": $("#sizeType").get(0).selectedIndex=2; break;'.
					    'case "g": $("#sizeType").get(0).selectedIndex=3; break;'.
					  '}'.
					  'alert("Вы превысили ограничение ПХП ('.$max_size.$t.')")'.
					'}'.
				    '}'.
				'});'.
			
		'</script>';
?>
