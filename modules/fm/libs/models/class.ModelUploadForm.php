<?php
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

class ModelUploadForm implements Model {
	function __construct($sData=false){
	}
	function View(){
		return new ViewUploadForm(array());
	}
}