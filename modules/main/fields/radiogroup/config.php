<?php
/**
 * \file config.php
 * Файл для конфигурации поля типа группа выбора, выводит параметры для настройки длины поля
 * Файл проекта kolos-cms.
 * 
 * Создан 16.09.2009
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 1.0
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

$sResult='<table class="layout"><tr><td width="30%">Количество одновременных значений (1 - радио кнопки, больше - кнопки выбора): </td>' .
		'<td width="70%"><input type="text" class="form_input" ' .
		'name="'.$params['prefix'].'option_1" ' .
		'value="'.intval($params['field']['option_1']).'" ' .
		'id="'.$params['prefix'].'option_1" /></td></tr>' .
		'<tr><td>Список значений (новое значение с новой строки, если требуется задать значение и подпись к нему необходимо соблюдать следующий синтаксис &lt;значение&gt;=&lt;подпись&gt;): </td><td><textarea class="form_input" ' .
		'name="'.$params['prefix'].'option_2" '.
		'id="'.$params['prefix'].'option_2">'.$params['field']['option_2'].'</textarea></td></tr>' .
		'<tr><td>Значение по умолчанию: </td><td id="listValues">';
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
$sResult.='</td></tr>' .
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
						'var list=$("input[name=\''.$params['prefix'].'ext_'.$params['field']['title'].'[]\']");'.
						'for(var i=0;i<list.length;i++)'.
						'{'.
							'list.eq(i).replaceWith("<input type=\"radio\" name=\"'.$params['prefix'].'ext_'.$params['field']['title'].'[]\" value=\""+list.eq(i).val()+"\">");'.
						'}'.
					'}'.
					'else'.
					'{'.
						'var list=$("input[name=\''.$params['prefix'].'ext_'.$params['field']['title'].'[]\']");'.
						'for(var i=0;i<list.length;i++)'.
						'{'.
							'list.eq(i).replaceWith("<input type=\"checkbox\" name=\"'.$params['prefix'].'ext_'.$params['field']['title'].'[]\" value=\""+list.eq(i).val()+"\">");'.
						'}'.
					'}'.
				'}' .
			'});'.
			'$("#'.$params['prefix'].'option_2").change(function(event){' .
					'try' .
					'{' .
						'var arData=$("#'.$params['prefix'].'option_2").val().split("\n");'.
						'if(arData && arData.length>0)' .
						'{'.
							'var obSelect=$("#listValues");'.
							'var type="checkbox";'.
							'obSelect.empty();'.
							'if($("#'.$params['prefix'].'option_1").val()==1) type="radio";'.
							'for(var i=0;i<arData.length;i++)' .
							'{'.
								'var row=arData[i].split("=");'.
								'if(row && row.length>1)'.
								'{'.
									'obSelect.append("<label><input type=\""+type+"\" name=\"'.$params['prefix'].'ext_'.$params['field']['title'].'[]\" value=\""+row.shift()+"\"> "+row.join("=")+"</label>");'.
								'}'.
								'else '.
									'obSelect.append("<label><input type=\""+type+"\" name=\"'.$params['prefix'].'ext_'.$params['field']['title'].'[]\" value=\""+arData[i]+"\"> "+arData[i]+"</label>");'.
								'obSelect.append("<br/>");'.
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
