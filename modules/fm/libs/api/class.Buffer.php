<?php
/**
 * Класс для работы с буфером
 * @author Dmitry Konev <d.konev@kolosstudio.ru>,
 * @version 2.6
 * @since 12.01.12
 */

/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') )
	die("Hacking attempt!");

class Buffer {
	static function Add($sPath,$sElement,$sMarker=''){
		$_SESSION['buffer'][$sElement]=array(
			'path'=>$sPath,
			'element'=>$sElement,
			'marker'=>$sMarker
		);
	}
	
	static function Clear(){
		$_SESSION['buffer']=array();
	}
	
	static function Get(){
		return (!empty($_SESSION['buffer'])) ? $_SESSION['buffer'] : false;
	}

	static function In($sElement, $sMarker=false){
		if(!isset($_SESSION['buffer'][$sElement]))
			return false;
		if(!$sMarker)
			return true;
		return ($_SESSION['buffer'][$sElement]['marker']==$sMarker);
	}
}