<?php
if($this->obUser->GetLevel($arModule['directory'])==0)
{
	//Размещаем пункты модуля в меню "Общие настройки"
	$this->AddMenuItem(
		MenuItem(
			'SM_OPTIONS',
			'MAIN',
			'module=sitemap&page=options',
			$this->GetText('menu_sitemap'),
			'options.gif'),
		'GLOBAL'
	);
}

?>