<?php
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

class ModelDir implements Model {
	private $obFile;
	private $obHelper;
	function __construct(){
		global $KS_FS;
		$this->obFile=$KS_FS;
		$this->obHelper=Helper::Instance();
	}
	function View(){
		$arData=$this->GetData();
		return new ViewDir($arData);
	}
	function GetData(){
		$sPath=$this->obHelper->GetPath();
		$sFolder=$this->obHelper->GetFolder();
		$arDirItems=$this->obFile->GetDirItems($sPath);
		$arDirs=array();
		$arFiles=array();
		foreach($arDirItems as $sItem){
			$arData=array(
				'title'=>$sItem,
				'folder'=>$sFolder.'/'.$sItem,
				'path'=>$sPath.'/'.$sItem,
				'mode'=>$this->obHelper->GetPerm($sPath.'/'.$sItem)
			);
			if(is_dir($arData['path'])){
				$arData['date_access']=fileatime($arData['path']);
				$arData['size']=filesize($arData['path']);
				$arData['type']='dir';
				$arDirs[]=$arData;
			}else{
				$arStat=stat($arData['path']);
				$arData['size']=$arStat['size'];
				$arData['date_access']=$arStat['atime'];
				if(preg_match('#(\.[a-z]+)$#',$arData['title'],$arMatches))
					$arData['type']=substr($arMatches[0],1);
				$arFiles[]=$arData;
			}
		}
		$arTemp['items']=array_merge($arDirs,$arFiles);
		if(!$this->obHelper->IsCurrent())
			$arTemp['parent']=$this->obHelper->GetParent();
		return $arTemp;
	}
}