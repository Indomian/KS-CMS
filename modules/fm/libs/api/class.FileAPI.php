<?php
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

class FileAPI {
	private static $obInstance;
	private $sImagesPath;
	public function __construct(){
		$this->sImagesPath='/uploads/templates/admin/images';
	}
	public static function Instance(){
		if(!self::$obInstance)
			self::$obInstance=new self();
		return self::$obInstance;
	}
	public function InitModifier(&$smarty){
		if($smarty && $smarty instanceof Smarty){
			$smarty->register_modifier('fm_icons',array(&$this, 'GetIcon'));
		}
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
	public function GetIcon($sFileType){
		$sData='/icons2/file.gif';
		if($sFileType=='dir')
			$sData='/icons2/folder.gif';
		else{
			$sFile='/fm/images/file_icons/file_extension_'.$sFileType.'.png';
			if(file_exists(ROOT_DIR.$this->sImagesPath.$sFile))
				$sData=$sFile;
		}
		return $sData;
	}
}