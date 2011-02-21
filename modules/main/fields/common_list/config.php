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

$arLists=array();
if(file_exists(MODULES_DIR.'/main/fields/common_list/values.php'))
	include_once(MODULES_DIR.'/main/fields/common_list/values.php');

//Сохраняем имя всех параметров	
$sFieldName=$params['prefix'].'ext_'.$params['field']['title'];
//Определяем значение
$arDefaultValue=explode('>',$params['field']['default']);

$sResult='<table class="layout"><tr><td width="30%">Количество одновременных значений (1 - выпадающий список, больше - список множественного выбора): </td>' .
		'<td width="70%"><input type="text" class="form_input" ' .
		'name="'.$params['prefix'].'option_1" ' .
		'value="'.intval($params['field']['option_1']).'" ' .
		'id="'.$params['prefix'].'option_1" /></td></tr>' .
		'<tr><td>Название списка значений: </td>' .
		'<td>' .
			'<input type="text" name="'.$params['prefix'].'option_2" id="'.$params['prefix'].'option_2" value="'.$params['field']['option_2'].'"/>'.
			'&lt;&lt;'.
			'<select class="form_input" name="list_name" id="list_name">'.
				'<option value="">Создать новый</option>';
				foreach($arLists as $key=>$arValue)
				{
					$sResult.='<option value="'.$key.'">'.$key.'</option>';
					if ($key==$params['field']['option_2'])
					{
						foreach($arValue as $skey=>$value)
						{
							if(is_array($value))
							{	
								foreach($value as $sskey=>$svalue)
								{
									$sValues.=$skey.':'.$sskey.'='.$svalue."\n";
								}
							}
							else
							{
								$sValues.=$skey.'='.$value."\n";
							}
						}
						$arValues=$arValue;
					}
					if(is_array($arValue))
					{
						foreach($arValue as $skey=>$svalue)
						{
							if(is_array($svalue))
							{	
								foreach($svalue as $sskey=>$ssvalue)
								{
									$arSValues[$key].=$skey.':'.$sskey.'='.$ssvalue."\n";
								}
							}
							else
							{
								$arSValues[$key].=$skey.'='.$svalue."\n";
							}
						}
					}
				}
  $sResult.='</select>';
  if(count($arSValues)>0)
  {
  	foreach($arSValues as $key=>$value)
  	{	
  		$sResult.='<span id="arval_'.$key.'" style="display:none;">'.$value.'</span>';
  	}
  }
  $sResult.='</td></tr>'.
		'<tr><td>Список значений (новое значение с новой строки, если требуется задать значение и подпись к нему необходимо соблюдать следующий синтаксис &lt;значение&gt;=&lt;подпись&gt;): </td>' .
		'<td><textarea class="form_input" style="width:100%;height:200px;" ' .
		'name="values"'.
		'id="values">'.$sValues.'</textarea></td></tr>' .
		'<tr><td>Значение по умолчанию: </td><td>' .
		'<fieldset id="common'.$sFieldName.'">' .
		'<legend>Значения</legend>';
if($params['field']['option_1']>1)
{
	$sType='checkbox';
}
else
{
	$sType='radio';
}
foreach($arValues as $key=>$item)
{
	if(is_array($item))
	{
		$sResult.='<fieldset id="set_'.$key.'"><legend>'.$key.'</legend>';
		foreach($item as $skey=>$sitem)
		{
			$sResult.='<label><input type="'.$sType.'" name="'.$sFieldName.'[]" value="'.$key.':'.$skey.'"'.(in_array($key.':'.$skey,$arDefaultValue)?' checked="checked"':'').'> '.$sitem.'</label><br/>';
		}
		$sResult.='</fieldset>';
	}
	else
	{
		$sResult.='<label><input type="'.$sType.'" name="'.$sFieldName.'[]" value="'.$key.'"'.(in_array($key,$arDefaultValue)?' checked="checked"':'').'> '.$item.'</label><br/>';	
	}
}
$sResult.='</fieldset></td></tr>' .
		'</table>'.
		'<script type="text/javascript">$(document).ready(function(){' .
			'$("#values").keyup(function(event){' .
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
			'$("#list_name").change(function(event){
				$("#'.$params['prefix'].'option_2").val(this.value);
				$("#values").val($("#arval_"+this.value).html());' .
				'$("#values").change();'.
			'});'.
			'$("#values").change(function(event){' .
					'try' .
					'{' .
						'var arData=this.value.split("\n");'.
						'var val=parseInt($("#'.$params['prefix'].'option_1").val());'.
						'var sType="radio";'.
						'if(val>0)' .
						'{'.
							'if(val==1)' .
							'{'.
								'sType="radio";'.
							'}'.
							'else'.
							'{'.
								'sType="checkbox";'.
							'}'.
						'}' .
						'if(arData && arData.length>0)' .
						'{'.
							'var obSelect=$("#common'.$sFieldName.'");'.
							'obSelect.empty();'.
							'for(var i=0;i<arData.length;i++)' .
							'{'.
								'var row=arData[i].split("=");'.
								'if(row && row.length>1)'.
								'{'.
									'var subrow=row[0].split(":");' .
									'if(subrow && subrow.length>1)' .
									'{' .
										'var group=$("#set_"+subrow[0].replace(" ","_"));' .
										'if(group.length>0)' .
										'{' .
											'group.append("<label><input type=\""+sType+"\" name=\"'.$sFieldName.'[]\" value=\""+row[0]+":"+subrow[1]+"\">"+row[1]+"</label><br/>");'.
										'}'.
										'else' .
										'{' .
											'obSelect.append("<fieldset id=\"set_"+subrow[0].replace(" ","_")+"\"><legend>"+subrow[0]+"</legend></fieldset>");' .
											'$("#set_"+subrow[0].replace(" ","_")).append("<label><input type=\""+sType+"\" name=\"'.$sFieldName.'[]\" value=\""+row[0]+":"+subrow[1]+"\">"+row[1]+"</label><br/>");'.
										'}'.									
									'}' .
									'else' .
									'{'.
										'obSelect.append("<label><input type=\""+sType+"\" name=\"'.$sFieldName.'[]\" value=\""+row[0]+"\">"+row[1]+"</label><br/>");'.
									'}'.
								'}'.
								'else '.
									'obSelect.append("<label><input type=\""+sType+"\" name=\"'.$sFieldName.'\" value=\""+arData[i]+"\">"+arData[i]+"</label><br/>");'.
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
