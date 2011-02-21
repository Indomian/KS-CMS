<?php
$monthes="['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь']";
$days="['Воскресенье','Понедельник','Вторник','Среда','Четверг','Пятница','Суббота']";
$daysShort="['Вос','Пон','Втр','Срд','Чтв','Пят','Суб']";
$daysMin="['Вс','Пн','Вт','Ср','Чт','Пт','Сб']";
$date_format="'dd.mm.yy'";
$time_format="'hh:ii'";

$sResult="<input type=\"text\" " .
		"class=\"form_input\" " .
		"readonly=\"readonly\" " .
		"id=\"".$params['prefix']."ext_".$params['field']['title']."\" " .
		"name=\"".$params['prefix']."ext_".$params['field']['title']."\" " .
		"value=\"".($params['value']!=0?strftime('%d.%m.%Y %H:%M',$params['value']):'')."\" " .
		">";
$sResult.='<img src="/uploads/templates/admin/images/calendar/img.gif" ' .
		'id="'.$params['prefix']."ext_".$params['field']['title'].'_btn" ' .
		'style="border: 0pt none ; cursor: pointer;" ' .
		'title="Выбор даты спомощью календаря" align="absmiddle">';
$sResult.='<script type="text/javascript">' .
	'$(document).bind("InitCalendar",function(){' .
		'$("#'.$params['prefix']."ext_".$params['field']['title'].'").datetimepicker(' .
				'{' .
					'dateFormat:'.$date_format.',' .
					'timeFormat:'.$time_format.',' .
					'dayNames:'.$days.',' .
					'dayNamesMin:'.$daysMin.',' .
					'dayNamesShort:'.$daysShort.',' .
					'monthNames:'.$monthes.'' .
				'});' .
		'$("#'.$params['prefix']."ext_".$params['field']['title'].'_btn").click(function(){' .
				'$("#'.$params['prefix']."ext_".$params['field']['title'].'").datetimepicker(\'show\')});
	});
	$(document).ready(function(){$(document).trigger("InitCalendar");});
</script>';
?>