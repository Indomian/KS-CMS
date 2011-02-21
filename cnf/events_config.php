<?php

/**
			 * Конфигурационный файл обработчиков событий
	 		* 
	 		* В этом файле должен быть определён конфигурационный массив обработчиков событий ,
	 		* структура которого описана в классе CEventsHandler
	 		* 
	 		* @filesource events_config.php
	 		* @author north-e <pushkov@kolosstudio.ru>
	 		* @version 0.1 
	 		* @since 03.03.2009
	 		* Файл сгенерирован автоматически
	 		*/
$KS_EVENTS = array
(
'main'=>array
(
'onInit'=>array
(
array('hFile'=>'onInit.php','hFunc'=>'mainOnInit'),

),
'onUserSessionUpdate'=>array
(
array('hFile'=>'statistics.php','hFunc'=>array('CStatistics','onUserSessionUpdate')),

),
'onUserObjectInit'=>array
(
array('hFile'=>'statistics.php','hFunc'=>array('CStatistics','onUserObjectInit')),

),
'onLogin'=>array
(
array('hFile'=>'statistics.php','hFunc'=>array('CStatistics','onLogin')),

),
'onBeforeLogout'=>array
(
array('hFile'=>'statistics.php','hFunc'=>array('CStatistics','onBeforeLogout')),

),

),
);?>