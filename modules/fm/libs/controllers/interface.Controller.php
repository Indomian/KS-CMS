<?php
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

interface Controller {
	public function Upload();
	public function Download($s);
	public function Edit($s);
	public function Copy(array $s);
	public function Cut(array $s);
	public function Paste();
	public function Delete(array $s);
	public function Open($s);
	public function Rename($s);
	public function Cancel();
}
