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
	static function Add($mData,$sMarker=''){
		$this->Clear();
		$_SESSION['buffer']=array(
			'data'=>$mData,
			'marker'=>$sMarker
		);
	}
	
	static function Clear(){
		unset($_SESSION['buffer']);
	}
	
	static function Get(){
		return (!empty($_SESSION['buffer'])) ? $_SESSION['buffer'] : false;
	}
}