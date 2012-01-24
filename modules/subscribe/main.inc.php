<?php
/**
 * Файл выполняет обработку запросов к модулю и выводит страницу оформления подписки
 * 
 * @since 24.01.2012
 *
 * @author BlaDe39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

if($this->CheckPath('/'))
	$output['main_content']=$this->IncludeWidget('subscribe','Subscribe',$module_parameters);
else
	throw new CHTTPError('SYSTEM_PAGE_NOT_FOUND',404);

