<?php
if($this->obUser->GetLevel($arModule['directory'])==0)
{
	$this->AddMenuItem(MenuItem("WAVE","OPTIONS","module=wave&page=options",$this->GetText('menu_wave'),'options.gif'),"GLOBAL");
}
