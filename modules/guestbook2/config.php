<?php

/**
 * Конфигурационный файл модуля "guestbook2"
 * Последнее изменение: 13.09.2010, 12:04:24
 */

$MODULE_guestbook2_config = array
(
	'use_captcha' => "1",
	'restricted_guest_names' => array
	(
		'0' => "admin",
		'1' => "administrator",
		'2' => "test",
		'3' => "администратор",
		'4' => "админ"
	),
	'use_tags' => "0",
	'no_empty_category' => "1",
	'int_htmleditor' => "1"
);

$MODULE_guestbook2_db_config = array
(
	'posts' => "gb2_posts",
	'answers' => "gb2_answers",
	'categories' => "gb2_categories"
);

?>