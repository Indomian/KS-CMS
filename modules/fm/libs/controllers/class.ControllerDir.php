<?php
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

class ControllerDir implements Controller {
	private $obFileController;
	private $obModelUploadForm;
	private $obModelDir;
	private $obFile;
	private $obHelper;
	
	function __construct(Controller $obControllerFile){
		global $KS_FS;
		$this->obHelper=Helper::Instance();
		$this->obFile=$KS_FS;
		$this->obControllerFile=$obControllerFile;
		$this->obModelDir=new ModelDir();
		$this->obModelUploadForm=new ModelUploadForm();
	}
	
	public function Open($sPath){
		return $this->obModelDir->View();
	}
	
	public function Edit($sPath){
		throw new CFileError('FM_NO_METHOD');
	}

	public function Rename($sOldName){
		throw new CFileError('FM_NO_METHOD');
	}
	
	public function Delete(array $arElements){
		if(count($arElements)>0){
			$sPath=$this->obHelper->GetPath();
			$nCount=0;
			foreach($arElements as $nKey=>$sTitle){
				$this->obFile->Remove($sPath.'/'.$sTitle);
			}
		}
		$arMessage=array('text'=>'FM_DELETE_COMPLETE','code'=>2);
		$obModelResult=new ModelResult($arMessage);
		return $obModelResult->View();
	}
	
	public function Copy(array $arElements){
		if(count($arElements)>0){
			$sPath=$this->obHelper->GetPath();
			Buffer::Clear();
			foreach($arElements as $nKey=>$sTitle){
				Buffer::Add($sPath,$sTitle,'copy');
			}
		}
		$arMessage=array('text'=>'FM_COPY_COMPLETE','code'=>2);
		$obModelResult=new ModelResult($arMessage);
		return $obModelResult->View();
	}
	
	public function Cut(array $arElements){
		if(count($arElements)>0){
			$sPath=$this->obHelper->GetPath();
			Buffer::Clear();
			foreach($arElements as $nKey=>$sTitle){
				Buffer::Add($sPath,$sTitle,'cut');
			}
		}
		$arMessage=array('text'=>'FM_CUT_COMPLETE','code'=>2);
		$obModelResult=new ModelResult($arMessage);
		return $obModelResult->View();
	}
	
	public function Paste(){
		if($arData=Buffer::Get()){
			$sPath=$this->obHelper->GetPath();
			
			foreach($arData as $arElement){
				$bResult=$this->obFile->CopyFile($arElement['path'].'/'.$arElement['element'],$sPath.'/'.$arElement['element']);
				if($bResult && $arElement['marker']=='cut')
					$this->obFile->Remove($arElement['path'].'/'.$arElement['element']);
			}
			Buffer::Clear();
		}
		$arMessage=array('text'=>'FM_PASTE_COMPLETE','code'=>2);
		$obModelResult=new ModelResult($arMessage);
		return $obModelResult->View();
	}
	
	public function Upload(){
		return $this->obModelUploadForm->View();
	}
	
	public function Download($sElement){
		throw CFileError('FM_NO_METHOD');
	}

	public function Cancel(){
		$arMessage=array(
			'text'=>'FM_CANCELED',
			'code'=>2
		);
		$obModelResult=new ModelResult($arMessage);
		return $obModelResult->View();
	}
}