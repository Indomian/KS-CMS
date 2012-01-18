<?php
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

class FileAPI {
	private static $obInstance;
	public function __construct(){}
	public static function Instance(){
		if(!self::$obInstance)
			self::$obInstance=new self();
		return self::$obInstance;
	}
	public function DownloadFile($sFile){
		if(file_exists($sFile)){
			$sLoadName=basename($sFile);
			header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
			header('Content-Type: application/octet-stream');
			header('Last-Modified: ' . gmdate('r', filemtime($sFile)));
			header('ETag: ' . sprintf('%x-%x-%x', fileinode($sFile), filesize($sFile), filemtime($sFile)));
			header('Content-Length: ' . (filesize($sFile)));
			header('Connection: close');
			header('Content-Disposition: attachment; filename="'.$sLoadName.'";');
			$rFile=fopen($sFile,'r');
			while(!feof($rFile)){
				echo fread($rFile, 1024);
				flush();
			}
			fclose($rFile);
		}else{
			header($_SERVER['SERVER_PROTOCOL'] .' 404 Not Found');
			header('Status: 404 Not Found');
		}
	}
	public function IsEditable($sFile){
		global $KS_MODULES;
		$bOpen=false;
		$sType="undefined";
		$sExtension='undefined';
		$arAvailableTypes=$KS_MODULES->GetConfigVar('fm','availabels_types',array());
		if(preg_match('#(\.[a-z]*)$#',$sFile,$arMatches)){
			$sExtension=substr($arMatches[0],1);
			if(isset($arAvailableTypes['image']) && in_array($sExtension,$arAvailableTypes['image'])){
				$bOpen=true;
				$sType='image';
			}elseif(isset($arAvailableTypes['text']) && in_array($sExtension,$arAvailableTypes['text'])){
				$bOpen=true;
				$sType='text';
			}
		}
		$arData=array(
			'type'=>$sType,
			'extension'=>$sExtension,
			'open'=>$bOpen
		);
		return $arData;
	}
	public function EditText($sFile){
		$sContent=(!empty($_POST['content'])) ? $_POST['content'] : '';
		if(file_exists($sFile)){
			@file_put_contents($sFile,$sContent);
			return true;
		}else{
			return false;
		}
	}
}