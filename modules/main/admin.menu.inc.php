<?php
if($this->obUser->IsLogin())
{
	$access = $this->obUser->GetLevel("main");
	$this->AddMenuItem(MenuItem("GLOBAL", "MAIN", "module=main&modpage=main", $this->GetText('menu_general'), "other_poss"));
	if ($access<=10)
	{
		if ($access <= 9) $this->AddMenuItem(MenuItem("USERS","MAIN","module=main&modpage=users",$this->GetText('menu_users'),'user.gif '),"GLOBAL");
		if ($access <= 2) $this->AddMenuItem(MenuItem("USERGROUPS","MAIN","module=main&modpage=usergroups",$this->GetText('menu_usergroups'),'group.gif '),"GLOBAL");
		if ($access == 0) $this->AddMenuItem(MenuItem("MODULES","MAIN","module=main&modpage=modules",$this->GetText('menu_module_control'),'module.gif '),"GLOBAL");
		if ($access <= 7)
		{
			$this->AddMenuItem(MenuItem("TEMPLATES","MAIN","module=main&modpage=templates",$this->GetText('menu_templates'),'template.gif '),"GLOBAL");
			//$this->AddMenuItem(MenuItem("ERRORS","MAIN","module=main&modpage=errors",$this->GetText('menu_error_text'),'item.gif '),"GLOBAL");
		}
		if ($access <= 6) $this->AddMenuItem(MenuItem("ETEMPLATES","MAIN","module=main&modpage=eventtemplates",$this->GetText('menu_email_templates'),'email_template.gif '),"GLOBAL");
		if ($access <= 8) $this->AddMenuItem(MenuItem("FIELDS","MAIN","module=main&modpage=fields",$this->GetText('menu_user_fields'),'user_field.gif '),"GLOBAL");
		if ($access == 0) $this->AddMenuItem(MenuItem("EVENTS","MAIN","module=main&modpage=events",$this->GetText('menu_email_sended'),'email_template.gif '),"GLOBAL");
		if ($access == 0) $this->AddMenuItem(MenuItem("OPTIONS","MAIN","module=main&modpage=options",$this->GetText('menu_sys_config'),'options.gif '),"GLOBAL");
		if ($access == 0) $this->AddMenuItem(MenuItem("UPDATE","MAIN","module=main&modpage=update",$this->GetText('menu_update'),'options.gif '),"GLOBAL");
		if ($access == 0) $this->AddMenuItem(MenuItem("UPDATE","MAIN","module=main&modpage=geography",$this->GetText('menu_geography'),'item.gif '),"GLOBAL");
	}
}
?>
