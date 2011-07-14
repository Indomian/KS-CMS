<?php
if($this->obUser->GetLevel($arModule['directory'])==0)
{
	//Размещаем пункты модуля в меню "Общие настройки"
	$this->AddMenuItem(
		MenuItem(
			'HINTS',
			'MAIN',
			'module=hints',
			$this->GetText('menu_hints'),
			'comments.png'),
		'GLOBAL'
	);
}