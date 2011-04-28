<?php

if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CModuleAdmin.php';

class CbannersAIindex extends CModuleAdmin
{
	function Run()
	{
		CUrlParser::get_instance()->Redirect('/admin.php?module=banners&page=banners');
	}
}
