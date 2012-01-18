<?php
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') )
	die("Hacking attempt!");

class Helper {
	private $sPath;
	private $sCurrentPath;
	private $obModules;
	private $obFiles;
	static private $obInstance;

	static function Instance(){
		if(!self::$obInstance)
			self::$obInstance=new self();
		return self::$obInstance;
	}
	
	function __construct(){
		global $KS_MODULES, $KS_FS;
		$this->obFiles=$KS_FS;
		$this->obModules=$KS_MODULES;
//$_SESSION['fm_path']='';
		$this->sCurrentPath=$KS_MODULES->GetConfigVar('fm','current_path',UPLOADS_DIR);
		$this->sPath=(!empty($_SESSION['fm_path'])) ? $_SESSION['fm_path'] : $this->sCurrentPath;
		
		if(!empty($_GET['fm_path'])){
			$sPath=preg_replace('#^(([.]{2,}+[\/]{0,1})||(\.\/))#','',$_GET['fm_path']);
			$this->sPath=$this->sCurrentPath.$sPath;
		}
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

	public function GetFolder(){
		return str_replace($this->sCurrentPath,'',$this->sPath);
	}

	public function GetCurrentPath(){
		return $this->sCurrentPath;
	}
	public function ConcatPath($sDir, $sPath=''){
		$sPath=($sPath=='') ? $this->GetPath() : $sPath;
		if(empty($sDir))
			return $this->sPath;
		$this->sPath=$sPath.$sDir;
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

	public function IsCurrent($sPath=''){
		if($sPath=='')
			$sPath=$this->GetPath();
		$sRes=str_replace($this->sCurrentPath,'',$sPath);
		if(strlen($sRes)>0 && $sRes!='/')
			return false;
		return true;
	}

	public function GetParent($sPath=''){
		if($sPath=='')
			$sPath=$this->GetFolder();
		$arDirs=explode('/',$sPath);
		unset($arDirs[sizeof($arDirs)-1]);
		return (count($arDirs)>1)?implode('/',$arDirs):'/';
	}

	public function UploadFiles($sFieldName){
		$arFiles=(isset($_FILES[$sFieldName])) ? $_FILES[$sFieldName] : array();
		$arNames=(isset($_POST['file_name'])) ? $_POST['file_name'] : array();
		if(count($arFiles)<=0)
			return false;
		$arTemp=array();
		foreach($arFiles as $sKey=>$arValues){
			foreach($arValues as $nKey=>$sValue){
				if(!empty($sValue))
					$arTemp[$nKey][$sKey]=$sValue;
			}
		}
		$nTempCount=count($arTemp);
		if($nTempCount<=0)
			return false;
		$sPath=$this->GetPath();
		$nCount=0;
		foreach($arTemp as $nKey=>$arValue){
			$sNewName=(!empty($arNames[$nKey])) ? $arNames[$nKey] : $arValue['name'];
			if(@move_uploaded_file($arValue['tmp_name'], $sPath.'/'.$sNewName))
				$nCount++;
		}
		return array('sends'=>$nTempCount, 'uploads'=>$nCount);
	}
}