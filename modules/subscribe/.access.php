<?php
/**
 * Файл описания уровней доступа к модулю subscribe
 * @file modules/subscribe/.access.php
 * @author Konstantin Kuznetsov <lopikun@gmail.com>, BlaDe39 <blade39@kolosstudio.ru>
 * @version 2.6
 * @since 16.09.2011
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

$arLevels=array(
	0=>$this->GetText('access_full'),
	1 => $this->GetText('access_subscribers'),
	5 => $this->GetText('access_releases'),
	8 => $this->GetText('access_subscribe'),
	9 => $this->GetText('access_view'),
	10=>$this->GetText('access_denied'),
);