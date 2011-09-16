<?php
/**
 * @file modules/subscribe/admin.menu.inc.php
 * Файл построения административного меню модуля subscribe
 *
 * @author Konstantin Kuznetsov <lopikun@gmail.com>, blade39 <blade39@kolosstudio.ru>
 * @since 16.09.2011
 * @version 2.6
 */

/* Обязательно вставляем во все файлы для защиты от взлома */
if (!defined("KS_ENGINE")) die("Hacking attempt!");

if($this->obUser->GetLevel($arModule['directory'])<=5)
{
	$this->AddMenuItem(MenuItem("DUMMY","DUMMY","module=dummy",$arModule['name'],'text_pages'));
	$this->AddMenuItem(MenuItem('MANAGE','DUMMY','module=dummy',$this->GetText('dummy'),'item.gif'),'DUMMY');
	$this->AddMenuItem(MenuItem("SUBSCRIBE", $arModule['name'],"module=".$arModule['directory']."&page=newsletters", $arModule["name"], "subscribe_newsletters"));
	$this->AddMenuItem(MenuItem("SUBSCRIBE_MANAGEMENT", $arModule['name'],"module=".$arModule['directory']."&page=newsletters", $this->GetText('menu_managment'), "item.gif"), "SUBSCRIBE");
	$this->AddMenuItem(MenuItem("SUBSCRIBE_RELEASES", $arModule['name'],"module=".$arModule['directory']."&page=releases", $this->GetText('menu_releases'), "item.gif"), "SUBSCRIBE");
	$this->AddMenuItem(MenuItem("SUBSCRIBE_SUBSCRIBE", $arModule['name'],"module=".$arModule['directory']."&page=subscribe", $this->GetText('menu_subscribers'), "item.gif"), "SUBSCRIBE");

	if($this->obUser->GetLevel($arModule['directory'])==0)
	{
		$this->AddMenuItem(MenuItem("SUBSCRIBE_OPTIONS","SUBSCRIBE","module=".$arModule['directory']."&page=options",$this->GetText('menu_options'),'options.gif','SUBSCRIBE'),"SUBSCRIBE");
	}
}