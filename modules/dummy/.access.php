<?php
/**
 * Файл описания уровней доступа к модулю
 * @file .access.php
 * @author BlaDe39 <blade39@kolosstudio.ru>
 * @version 2.6
 * @since 31.08.2011
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

$arLevels=array(
	0=>$this->GetText('access_full'),
	10=>$this->GetText('access_denied'),
);

