<?php

/**
 * Файл с описанием виджетов и параметром, указывающим на корректность виджетов
 * 
 * @filesource .widgets.php
 * @author North-E <pushkov@kolosstudio.ru>
 * 
 * @version 1.0
 * @since 03.06.2009
 */

$arWidgets = array
(
	"RatingVote" => array
	(
		"name" => "Оценка материалов",
		"descr" => "Виджет выводит полоску для оценки материалов сайта",
		"has_widget" => 1
	),
	'TextParser'=> array
	(
		'name'=>'Обработка текста',
		'descr'=>'Виджет производит предварительную обработку текста перед выводом',
		'has_widget'=>1
	)
);
?>