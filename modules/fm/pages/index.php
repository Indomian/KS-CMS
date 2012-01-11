<?php
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

class CfmAIindex extends CModuleAdmin {
	function __construct($module='fm',&$smarty,&$parent){
		parent::__construct($module,$smarty,$parent);
	}
	function Run(){
		
	}
}
