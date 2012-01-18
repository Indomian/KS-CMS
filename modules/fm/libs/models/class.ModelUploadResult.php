<?php
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

class ModelUploadResult implements Model {
	private $obUrl;
	function __construct(){
		$this->obUrl=CURLParser::get_instance();
	}
	function View(){
		return new ViewUploadResult($this->GetData());
	}
	function GetData(){
		$arClearArray=array('a','t');
		return array(
			'redirect'=>'/admin.php?'.$this->obUrl->GetUrl($arClearArray)
		);
	}
}