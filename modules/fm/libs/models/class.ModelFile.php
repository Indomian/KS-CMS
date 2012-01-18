<?php
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

class ModelFile implements Model {
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
			'content'=>@file_get_contents($sPath.'/'.$this->sFile),
			'chain'=>$this->obHelper->GetNavChain($sFolder.'/'.$this->sFile)
		);
		return new ViewFile($arData);
	}
}