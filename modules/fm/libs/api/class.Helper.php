<?php
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') )
	die("Hacking attempt!");

class Helper {
	private $sPath;
	static private $obInstance;

	static function Instance(){
		if(!self::$obInstance)
			self::$obInstance=new self();
		return self::$obInstance;
	}
	
	function __construct(){
		$sPath=(!empty($_GET['fm_path'])) ? $_GET['fm_path'] : UPLOADS_DIR;
		$this->sPath=$sPath;
	}
	
	function __destruct(){
		$_SESSION['fm_path']=$this->sPath;
	}
	
	public function SetPath($sPath){
		$this->sPath.=$sPath;
	}

	public function GetPath(){
		return $this->sPath;
	}

	public function ConcatPath($sDir){
		$this->sPath.='/'.$sDir;
		return $this->sPath;
	}

	public function GetPathInfo($sPath,$sParam=false){
		if(!isset($this->arPathes[$sPath]))
			$this->arPathes[$sPath]=pathinfo($sPath);
		if(!$sParam)
			return $this->arPathes[$sPath];
		else
			return $this->arPathes[$sPath][$sParam];
	}

	public function GetPerm($sPath){
		$sPerms=substr(decoct(fileperms($sPath)),2,6);
		if(strlen($sPerms)=='3')
			$sPerms = '0' . $sPerms;
		return $sPerms;
	}
}