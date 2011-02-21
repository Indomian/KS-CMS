<?php
/**
 * \file config.php
 * Файл для конфигурации поля типа список, выводит параметры для настройки длины поля
 * Файл проекта kolos-cms.
 * 
 * Создан 16.09.2009
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 1.0
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

$sResult='<table class="layout"><tr><td width="30%">Количество одновременных значений (1 - выпадающий список, больше - список множественного выбора): </td>' .
		'<td width="70%"><input type="text" class="form_input" ' .
		'name="'.$params['prefix'].'option_1" ' .
		'value="'.intval($params['field']['option_1']).'" ' .
		'id="'.$params['prefix'].'option_1" /></td></tr>' .
		'<tr><td>Список значений (новое значение с новой строки, если требуется задать значение и подпись к нему необходимо соблюдать следующий синтаксис &lt;значение&gt;=&lt;подпись&gt;): </td><td><textarea class="form_input" ' .
		'name="'.$params['prefix'].'option_2" '.
		'id="'.$params['prefix'].'option_2">'.$params['field']['option_2'].'</textarea></td></tr>' .
		'<tr><td>Значение по умолчанию: </td><td><select class="form_input" ' .
		'name="'.$params['prefix'].'ext_'.$params['field']['title'].'" '.
		'value="'.$params['field']['default'].'" ';
if($params['field']['option_1']>1)
{
	$sResult.='multiple="multiple" size="3" ';
}
$sResult.='id="'.$params['prefix'].'ext_'.$params['field']['title'].'" />';
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
			$sResult.='<option value="'.$value.'"'.(in_array($value,$arVal)?' selected="selected"':'').'>'.join('=',$arItem).'</option>';
		}
		else
		{
			$sResult.='<option value="'.$sItem.'"'.(in_array($sItem,$arVal)?' selected="selected"':'').'>'.$sItem.'</option>';
		}
	}
}		
$sResult.='</select></td></tr>' .
		'</table>'.
		'<script type="text/javascript">$(document).ready(function(){' .
			'$("#'.$params['prefix'].'option_2").keyup(function(event){' .
					'this.lastInput++;'.
					'if(this.lastInput>5)'.
					'{' .
						'this.lastInput=0;' .
						'$("#'.$params['prefix'].'option_2").change();'.
					'}'.
			'});' .
			'$("#'.$params['prefix'].'option_1").keyup(function(event){'.
				'var val=parseInt(this.value);' .
				'if(val>0)' .
				'{'.
					'if(val==1)' .
					'{'.
						'$("#'.$params['prefix'].'ext_'.$params['field']['title'].'").attr("multiple",false).attr("size","1");'.
					'}'.
					'else'.
					'{'.
						'$("#'.$params['prefix'].'ext_'.$params['field']['title'].'").attr("multiple",true).attr("size","3");'.
					'}'.
				'}' .
			'});'.
			'$("#'.$params['prefix'].'option_2").change(function(event){' .
					'try' .
					'{' .
						'var arData=this.value.split("\n");'.
						'if(arData && arData.length>0)' .
						'{'.
							'var obSelect=$("#'.$params['prefix'].'ext_'.$params['field']['title'].'");'.
							'obSelect.empty();'.
							'for(var i=0;i<arData.length;i++)' .
							'{'.
								'var row=arData[i].split("=");'.
								'if(row && row.length>1)'.
								'{'.
									'obSelect.append("<option value=\""+row.shift()+"\">"+row.join("=")+"</option>");'.
								'}'.
								'else '.
									'obSelect.append("<option>"+arData[i]+"</option>");'.
							'}'.
						'}'.
					'}' .
					'catch(e)' .
					'{' .
						'alert(e);'.
					'}'.
			'});});'.
		'</script>';
?>
