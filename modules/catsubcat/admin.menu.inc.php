<?php
if($this->obUser->GetLevel($arModule['directory'])<=3)
{
	$this->AddMenuItem(MenuItem("CATSUBCAT","CATSUBCAT","module=catsubcat",$arModule['name'],'text_pages'));
	$this->AddMenuItem(MenuItem('MANAGE','CATSUBCAT','module=catsubcat',$this->GetText('page_managment'),'item.gif'),'CATSUBCAT');
	$this->AddMenuItem(MenuItem('BASKET','CATSUBCAT','module=catsubcat&page=basket',$this->GetText('basket'),'basket.gif'),'CATSUBCAT');
	if($this->obUser->GetLevel($arModule['directory'])<1)
	{
		$this->AddMenuItem(MenuItem("OPTIONS","CATSUBCAT","module=catsubcat&page=options",$this->GetText('options'),'options.gif'),"CATSUBCAT");
	}
}

?>