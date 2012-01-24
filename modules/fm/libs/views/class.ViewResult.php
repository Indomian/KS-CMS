<?php
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

class ViewResult implements View {
	private $arData;

	function __construct(array $arData){
		$this->arData=$arData;
	}

	function GetData(){
		return $this->arData;
	}
}