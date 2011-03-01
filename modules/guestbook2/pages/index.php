<?php

if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CModuleAdmin.php';

class Cguestbook2AIindex extends CModuleAdmin
{
	function Run()
	{
		CUrlParser::Redirect('/admin.php?module=guestbook2&page=records');
	}
}
