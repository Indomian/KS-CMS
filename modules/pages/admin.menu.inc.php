<?php
/**
 * @file admin.menu.inc.php
 * Файл построения административного меню модуля dummy
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @since 31.08.2011
 * @version 2.6
 */

/* Обязательно вставляем во все файлы для защиты от взлома */
if (!defined("KS_ENGINE")) die("Hacking attempt!");

if($this->obUser->GetLevel($arModule['directory'])<=3)
{
	$this->AddMenuItem(MenuItem("DUMMY","DUMMY","module=dummy",$arModule['name'],'text_pages'));
	$this->AddMenuItem(MenuItem('MANAGE','DUMMY','module=dummy',$this->GetText('dummy'),'item.gif'),'DUMMY');
}