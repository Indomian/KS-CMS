<?php
if ($this->obUser->GetLevel($arModule['directory']) == 0)
	$this->AddMenuItem(MenuItem("FM","MAIN","module=fm",$this->GetText('menu_fm'),'options.gif'),"GLOBAL");
