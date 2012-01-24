<?php
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

class ControllerFile implements Controller {
	private $obModelUploadResult;
	private $obModelFile;
	private $obModelImage;
	private $obFile;
	private $obHelper;
	private $obFileAPI;
	
	function __construct(){
		global $ks_fs;
		$this->obFile=$ks_fs;
		$this->obFileAPI=FileAPI::Instance();
		$this->obHelper=Helper::Instance();
	}

	public function Open($sFile){
		if($sFile!=''){
			$sPath=$this->obHelper->GetPath();
			$arData=$this->obFileAPI->IsEditable($sFile);
			if(!$arData['open'])
				throw new CFileError('FM_EDIT_THE_FILE_IS_FORBIDDEN');
			if($arData['type']=='image'){
				$obModeImage=new ModelImage($sFile);
				return $obModeImage->View();
			}else{
				$obModelFile=new ModelFile($sFile);
				return $obModelFile->View();
			}
		}else{
			throw new CFileError('FM_EDIT_THE_FILE_IS_FORBIDDEN');
		}
	}

	public function Edit($sFile){
		if($sFile=='')
			throw new CFileError('FM_EDIT_THE_FILE_IS_FORBIDDEN');
		$sPath=$this->obHelper->GetPath();
		$arData=$this->obFileAPI->IsEditable($sFile);
		if(!$arData['open'])
			throw new CFileError('FM_EDIT_THE_FILE_IS_FORBIDDEN');
		if($arData['type']=='text'){
			$this->obFileAPI->EditText($sPath.'/'.$sFile);
		}
		$arMessage=array('text'=>'FM_EDIT_COMLETE','code'=>2);
		$obModelResult=new ModelResult($arMessage);
		return $obModelResult->View();
	}

	public function Rename($sOldName){
		throw new CFileError('FM_NO_METHOD');
	}

	public function Delete(array $sFile){
		throw new CFileError('FM_NO_METHOD');
	}

	public function Copy(array $sFile){
		throw new CFileError('FM_NO_METHOD');
	}

	public function Cut(array $sFile){
		throw new CFileError('FM_NO_METHOD');
	}

	public function Paste(){
		throw new CFileError('FM_NO_METHOD');
	}

	public function Upload(){
		$arFiles=$_FILES;
		$arNames=(!empty($_POST['file_name'])) ? $_POST['file_name'] : array();
		if(count($arFiles)<=0)
			throw new CFileError('FM_UPLOAD_FILE_NOT_SELECTED');
		$sPath=$this->obHelper->GetPath();
		foreach($arFiles as $sKey=>$arValue){
			$obFileUploader=new CFileUploader($sKey, __CLASS__);
			$obFileUploader->SetRootDir($sPath);
			$sNewName=(!empty($arNames[$sKey])) ? $arNames[$sKey] : $arValue['name'];
			if(empty($sNewName))
				continue;
			$obFileUploader->Upload($sNewName, false);
		}
		$arMessage=array('text'=>'FM_UPLOAD_COMLETE','code'=>2);
		$obModelResult=new ModelResult($arMessage);
		return $obModelResult->View();
	}

	public function Download($sFile){
		if(!empty($sFile)){
			$sPath=$this->obHelper->GetPath();
			$sFile=$sPath.'/'.$sFile;
			if(is_file($sFile))
				$this->obFileAPI->DownloadFile($sFile);
		}
		return $this->obModelResult->View();
	}

	public function Cancel(){
		throw CFileError('FM_NO_METHOD');
	}
}
