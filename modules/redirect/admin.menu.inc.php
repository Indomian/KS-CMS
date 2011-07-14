<?php
if ($this->obUser->GetLevel($arModule['directory']) == 0)
	$this->AddMenuItem(MenuItem("REDIRECT","MAIN","module=redirect",$this->GetText('menu_redirect'),'options.gif'),"GLOBAL");
