<?php

/**
 * Файл, содержащий массивы стоп-символов и стоп-слов,
 * используемых при канонизации текстов классом CShingles
 * 
 * @author North-E <pushkov@kolosstudio.ru>
 * @version 1.0
 * @since 31.07.2009
 */

/* Массив стоп-символов */
$stop_symbols = array
(
	".", ",", "-", "_", "\"", "\/", "@", "~", "\$", "!", "?", "=", "*",
	";", ":", "'", "\n", "\t", "%", "№", "#", "&", "*", "(", ")",
	"<", ">", "[", "]", "{", "}", "|", "^", "+"
);

/* Массив стоп-слов */
$stop_words = array
(
	/* русские */
	"в", "на", "под", "над", "из", "к", "и", "а", "но", "не", "с", "для",
	"он", "она", "оно", "они", "им", "их", "ним", "них", "я", "ты", "вы",
	"это", "как", "так", "то", "те", "ну", "же", "что", "кто", "о", "об",
	"со", "во", "ли", "вот", "до", "за", "от", "ему", "ей", "него", "нее",
	"хотя", "бы", "либо", "кем", "чем", "какой", "какая", "который", "которая",
	"какие", "которые", "которого", "которой", "которых", "которому", "которым",
	
	/* английские */
	"i", "you", "me", "he", "she", "him", "her", "it", "that", "what",
	"how", "there", "is", "was", "do", "did", "does", "not", "those",
	"these", "this", "were", "who", "whose", "though"
);

?>