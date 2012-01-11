<?php
/**
 * Файл описания уровней доступа к модулю
 * @file .access.php
 * @author BlaDe39 <blade39@kolosstudio.ru>, Dmirty Konev <d.konev@kolosstudio.ru>
 * @version 2.6
 * @since 11.01.12
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

$arLevels=array(
	0=>$this->GetText('access_full'),
	5=>$this->GetText('access_view'),
	10=>$this->GetText('access_denied'),
);

