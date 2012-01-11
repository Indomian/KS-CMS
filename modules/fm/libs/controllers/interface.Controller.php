<?php
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

interface Controller {
	public function Model();
	public function Upload();
	public function Download();
	public function Edit();
	public function Copy();
	public function Cut();
	public function Paste();
	public function Delete();
	public function Open();
}
