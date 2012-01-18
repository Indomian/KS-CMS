<?php
/**
 * Файл описания меню модуля "Карта сайта"
 *
 * @filesource sitemap/admin.menu.inc.php
 * @author BlaDe39 <blade39@kolosstudio.ru>
 * @version 2.6
 * @since 24.03.2009
 */
if($this->obUser->GetLevel($arModule['directory'])==0)
{
	//Размещаем пункты модуля в меню "Общие настройки"
	$this->AddMenuItem(
		MenuItem(
			'SM_OPTIONS',
			'MAIN',
			'module=sitemap&page=options',
			$this->GetText('menu_sitemap'),
			'sitemap.png'),
		'GLOBAL'
	);
}