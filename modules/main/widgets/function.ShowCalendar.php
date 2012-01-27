<?php
/**
 * @file function.ShowCalendar.php
 * Функция TableHead осуществляет вывод поля для ввода с календарём
 * Файл проекта kolos-cms.
 *
 * Создан 03.05.2011
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

function smarty_function_ShowCalendar($params,&$smarty)
{
	global $KS_MODULES;
	if(!$KS_MODULES->HasJavaScript('/main/calendar.js',11))
	{
		$KS_MODULES->UseJavaScript('/main/calendar.js',11);
		$KS_MODULES->UseJavaScript("/jquery/jquery.ui.js",12);
		$KS_MODULES->UseJavaScript("/jquery/jquery-ui-timepicker-addon.js",13);
		$KS_MODULES->UseJavaScript("/jquery/jquery-ui-datepicker-ru.js",15);
		$KS_MODULES->UseJavaScript("/jquery/jquery-ui-timepicker-ru.js",15);
		$KS_MODULES->AddHeadString('<script type="text/javascript">dateStrings={
			\'dateFormat\':'.$KS_MODULES->GetText('date_format').',
			\'timeFormat\':'.$KS_MODULES->GetText('time_format').',
			\'dayNames\':'.$KS_MODULES->GetText('days').',
			\'dayNamesMin\':'.$KS_MODULES->GetText('daysMin').',
			\'dayNamesShort\':'.$KS_MODULES->GetText('daysShort').',
			\'monthNames\':'.$KS_MODULES->GetText('monthes').'};</script>');
	}
	if(isset($params['date_only']) && $params['date_only']=='Y')
	{
		$sResult='<div class="date_selector"><input type="text" readonly="readonly" name="'.$params['field'].'" value="'.((isset($params['value']) && $params['value']>0)?date('d.m.Y H:i',$params['value']):'').'" class="date_input" title="'.$params['title'].'"/><img src="'.$KS_MODULES->GetText('images_path').'/calendar/img.gif" title="'.$params['title'].'" align="absmiddle" class="date_button"/></div>';
	}
	else
	{
		$sResult='<div class="date_selector"><input type="text" readonly="readonly" name="'.$params['field'].'" value="'.((isset($params['value']) && $params['value']>0)?date('d.m.Y H:i',$params['value']):'').'" class="date_time_input" title="'.$params['title'].'"/><img src="'.$KS_MODULES->GetText('images_path').'/calendar/img.gif" title="'.$params['title'].'" align="absmiddle" class="date_button"/></div>';
	}
	return $sResult;
}


