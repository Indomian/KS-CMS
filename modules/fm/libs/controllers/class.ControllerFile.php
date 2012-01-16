<?php
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

class ControllerFile implements Controller {
	private $obModelUploadResult;
	private $obModelFile;
	private $obModelImage;
	private $obFile;
	private $obHelper;
	
	function __construct(){
		global $ks_fs;
		$this->obFile=$ks_fs;
		$this->obHelper=Helper::Instance();
		$this->obModelImage=new ModelImage();
		$this->obModelFile=new ModelFile();
		$this->obModelUploadResult=new ModelUploadResult();
	}

	public function Open(){
		return $this->obModelFile->View();
	}

	public function Edit(){
		
	}

	public function Rename($sOldName){
		$sNewName=(!empty($_POST['new_name'])) ? $_POST['new_name'] : '';
		if(!$sOldName || $sOldName=='' || $sNewName=='')
			throw new CFileError('FM_INCORRECT_DATA');
		$this->obFile->Rename($sOldName, $sNewName);
		return $this->obModelFile->View();
	}

	public function Delete($sFile){
		$sFile=$this->obHelper->ConcatPath($sFile);
		if($this->obFile->Remove($sFile)){
			return $this->obModelDir->View();
		}
		return $this->obModelDir->View();
	}

	public function Copy($sFile){
		$sFile=$this->obHelper->ConcatPath($sFile);
		Buffer::Add($sFile,'copy');
		return $this->obModelDir->View();
	}

	public function Cut($sFile){
		$sFile=$this->obHelper->GetPath($sFile);
		Buffer::Add($sFile,'cut');
		return $this->obModelDir->View();
	}

	public function Paste(){
		if($arData=Buffer::Get()){
			$sCurrentPath=$this->obHelper->GetPath();
			$sMarker=(!empty($arData['marker'])) ? $arData['marker'] : 'copy';
			$sFile=$this->obHelper->GetPathInfo($arData['data'],'filename');
			$bResult=$this->obFile->CopyFile($arData['data'],$this->obHelper->ConcatPath($sFile));
			if($bResult && $sMarker=='cut')
				$this->obFile->Remove($arData['data']);
		}
		return $this->obModelDir->View();
	}

	public function Upload(){
		return $this->obModelUploadForm->View();
	}

	public function Download(){
		throw CFileError('FM_NO_METHOD');
	}
}
