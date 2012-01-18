<?php
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

class Decorator {
	private $obView;
	private $obUrl;
	private $obSmarty;
	private $nResponseType;
	
	function __construct(View $obView, $obSmarty=false, $obUrl=false){
		$this->obView=$obView;
		$this->obSmarty=$obSmarty;
		$this->obUrl=$obUrl;
		$this->nResponseType=1;
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest')
			$this->nResponseType=2;
	}

	function Init(){
		$arData=$this->obView->GetData();
		if($this->nResponseType==2){
			print json_encode($arData);
		} else {
			if(!empty($arData['redirect'])){
				$this->obUrl->Redirect($arData['redirect']);
			}else{
				if(isset($arData['data']))
					$this->obSmarty->assign('fm',$arData['data']);
				return $arData['tpl'];
			}
		}
	}
}