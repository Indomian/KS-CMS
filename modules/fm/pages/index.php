<?php
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

class CfmAIindex extends CModuleAdmin {
	private $obBase;
	function __construct($module='fm',&$smarty,&$parent){
		parent::__construct($module,$smarty,$parent);
		$this->obBase=ControllerBase::Instance();
	}
	function Run(){
		$obData=$this->obBase->Run();
		$obDecorator=new Decorator($obData, $this->smarty, $this->obUrl);
		return $obDecorator->Init();
	}
}
