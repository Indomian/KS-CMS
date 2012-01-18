<?php
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

class ModelImage implements Model {
	private $sFile;
	private $obHelper;
	function __construct($sFile=false){
		$this->sFile=$sFile;
		$this->obHelper=Helper::Instance();
	}
	function View(){
		$sPath=$this->obHelper->GetPath();
		$sFolder=$this->obHelper->GetFolder();
		$arData=array(
			'title'=>$this->sFile,
			'path'=>str_replace(ROOT_DIR,'',$sPath).'/'.$this->sFile,
			'chain'=>$this->obHelper->GetNavChain($sFolder.'/'.$this->sFile)
		);
		return new ViewImage($arData);
	}
}