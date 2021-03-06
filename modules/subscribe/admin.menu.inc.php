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
	$this->AddMenuItem(MenuItem("SUBSCRIBE", $arModule['directory'],"module=".$arModule['directory'], $arModule["name"], "subscribe_newsletters"));
	$this->AddMenuItem(MenuItem("SUBSCRIBE_MANAGEMENT", "SUBSCRIBE","module=".$arModule['directory'], $this->GetText('menu_managment'), "mail_box.png"), "SUBSCRIBE");
	$this->AddMenuItem(MenuItem("SUBSCRIBE_RELEASES", "SUBSCRIBE","module=".$arModule['directory']."&page=releases", $this->GetText('menu_releases'), "email_go.png"), "SUBSCRIBE");
	$this->AddMenuItem(MenuItem("SUBSCRIBE_SUBSCRIBE", "SUBSCRIBE","module=".$arModule['directory']."&page=subscribe", $this->GetText('menu_subscribers'), "vcard_edit.png"), "SUBSCRIBE");

	if($this->obUser->GetLevel($arModule['directory'])==0)
	{
		$this->AddMenuItem(MenuItem("SUBSCRIBE_OPTIONS","SUBSCRIBE","module=".$arModule['directory']."&page=options",$this->GetText('menu_options'),'options.gif'),"SUBSCRIBE");
	}
}