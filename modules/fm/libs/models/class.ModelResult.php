<?php
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

class ModelResult implements Model {
	private $obUrl;
	private $arMessage;
	function __construct($arMessage=false){
		$this->obUrl=CURLParser::get_instance();
		$this->arMessage=$arMessage;
	}
	function View(){
		return new ViewResult($this->GetData());
	}
	function GetData(){
		$arClearArray=array('a','t','fm_file');
		$arData=array(
			'redirect'=>'/admin.php?'.((isset($_REQUEST['apply'])) ? $this->obUrl->GetUrl() : $this->obUrl->GetUrl($arClearArray))
		);
		if($this->arMessage){
			if(isset($this->arMessage['text'])){
				if(empty($this->arMessage['code']))
					$this->arMessage['code']=1;
				$arData['message']=array(
					'text'=>$this->arMessage['text'],
					'code'=>$this->arMessage['code']
				);
			}
		}
		return $arData;
	}
}