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
		$KS_MODULES->AddHeadString('<script type="text/javascript">var dateStrings={
			\'dateFormat\':'.$KS_MODULES->GetText('date_format').',
			\'timeFormat\':'.$KS_MODULES->GetText('time_format').',
			\'dayNames\':'.$KS_MODULES->GetText('days').',
			\'dayNamesMin\':'.$KS_MODULES->GetText('daysMin').',
			\'dayNamesShort\':'.$KS_MODULES->GetText('daysShort').',
			\'monthNames\':'.$KS_MODULES->GetText('monthes').',
			\'buttonImage\':\''.$KS_MODULES->GetText('images_path').'/calendar/calendar.png\',
			\'showButtonPanel\':true,
			\'showOn\':\'both\'};</script>');
	}
	if(isset($params['date_only']) && $params['date_only']=='Y')
	{
		if(isset($params['value']) && is_numeric($params['value']) && $params['value']>0)
			$params['value']=date('d.m.Y',$params['value']);
		elseif(preg_match('#^\d\d\.\d\d\.\d\d\d\d$#',$params['value']))
			$params['value']=trim($params['value']);
		else
			$params['value']='';
		$sClassName='date_input';
	}
	else
	{
		if(isset($params['value']) && is_numeric($params['value']) && $params['value']>0)
			$params['value']=date('d.m.Y H:i',$params['value']);
		elseif(preg_match('#^\d\d\.\d\d\.\d\d\d\d \d\d:\d\d$#',$params['value']))
			$params['value']=trim($params['value']);
		else
			$params['value']='';
		$sClassName='date_time_input';
	}
	return '<div class="date_selector"><input type="text" name="'.$params['field'].'" value="'.$params['value'].'" class="'.$sClassName.'" title="'.$params['title'].'"/></div>';
}


