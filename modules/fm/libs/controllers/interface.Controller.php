<?php
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

interface Controller {
	public function Model();
	public function Upload();
	public function Download();
	public function Edit();
	public function Copy($s);
	public function Cut($s);
	public function Paste();
	public function Delete($s);
	public function Open($s);
	public function Rename($s);
}
