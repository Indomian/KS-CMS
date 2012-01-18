<?php
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

interface Controller {
	public function Upload();
	public function Download();
	public function Edit();
	public function Copy($s);
	public function Cut($s);
	public function Paste();
	public function Delete(array $s);
	public function Open();
	public function Rename($s);
}
