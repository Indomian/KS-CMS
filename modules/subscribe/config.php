<?php

/**
 * Конфигурационный файл модуля "subscribe"
 * Последнее изменение: 02.03.2010, 00:35:59
 */

$MODULE_subscribe_config = array
(
	'format' => "1",
	'encryption' => "cp1251",
	'from' => "fox@kolosstudio.ru",
	'title_default' => "Рассылка сообщений",
	'acceptable_order_fields' => array
	(
		'0' => "name",
		'1' => "date_add",
		'2' => "date_edit",
		'3' => "orderation"
	),
	'acceptable_actions' => array
	(
		'0' => "new",
		'1' => "edit",
		'2' => "delete",
		'3' => "common",
		'4' => "save",
		'5' => "update",
		'6' => "send"
	)
);