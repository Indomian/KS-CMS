<?php
if ($this->obUser->GetLevel($arModule['directory']) == 0)
	$this->AddMenuItem(MenuItem("NAVIGATION","MAIN","module=navigation",$this->GetText('menu_navigation'),'options.gif'),"GLOBAL");
?>