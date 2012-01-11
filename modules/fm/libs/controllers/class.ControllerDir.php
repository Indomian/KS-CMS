<?php
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

class ControllerDir implements Controller {
	private $obFileController;
	
	function __construct(Controller $obControllerFile){
		$this->obControllerFile=$obControllerFile;
	}
	
	public function Open(){
		
	}
	
	public function Edit(){
		
	}
	
	public function Delete(){
	
	}
	
	public function Copy(){
	
	}
	
	public function Cut(){
	
	}
	
	public function Paste(){
	
	}
	
	public function Upload(){
	
	}
	
	public function Download(){
	
	}
}
