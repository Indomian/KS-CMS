<?php
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') )
	die("Hacking attempt!");

class Helper {
	private $sPath;
	
	function __construct($sPath){
		$this->sPath=$sPath
	}
	
	function __destruct(){
		$_SESSION['fm_path']=$this->sPath;
	}
	
	public function SetPath($sPath){
		$this->sPath.=$sPath
	}

	public function GetPath(){
		return $this->sPath;
	}

	public function ConcatPath($sDir){
		$this->sPath.='/'.$sDir;
		return $this->sPath;
	}
}