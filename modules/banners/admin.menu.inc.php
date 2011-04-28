<?php
/**
 * @file banners/admin.menu.inc.php
 * @since 08.04.2010
 * @author blade39 <blade39@kolosstudio.ru>
 *
 * Файл генерирует меню управления баннерами
 * @version 2.6
 */
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

$module_name=$arModule['directory'];
$accessLevel=$this->obUser->GetLevel($module_name);

if($accessLevel<=8)
{
	$this->AddMenuItem(
		MenuItem(
			"BANNERS",
			"BANNERS",
			"module=".$module_name,
			$this->GetTitle($module_name),
			'manage_ad'
		)
	);
	//Размещаем пункты модуля в меню "Рекламма"
	$this->AddMenuItem(
		MenuItem(
			'BANNERS_BANNERS',
			'BANNERS',
			'module='.$module_name.'&page=banners',
			$this->GetText('menu_banners'),
			'advertising.png',
			'BANNERS'),
		'BANNERS'
	);
	if($accessLevel<=5)
	{
		$this->AddMenuItem(
			MenuItem(
				'BANNERS_TYPES',
				'BANNERS',
				'module='.$module_name.'&page=types',
				$this->GetText('menu_banner_types'),
				'layout.png',
				'BANNERS'),
			'BANNERS'
		);
	}
	if($accessLevel<=4)
	{
		$this->AddMenuItem(
			MenuItem(
				'BANNERS_CLIENTS',
				'BANNERS',
				'module='.$module_name.'&page=clients',
				$this->GetText('menu_banner_clients'),
				'money.png',
				'BANNERS'),
			'BANNERS'
		);
	}
	if($accessLevel==0)
	{
		$this->AddMenuItem(
			MenuItem(
				'BANNERS_OPTIONS',
				'BANNERS',
				'module='.$module_name.'&page=options',
				$this->GetText('menu_banner_options'),
				'options.gif',
				'BANNERS'),
			'BANNERS'
		);
	}
}
