<?php
/**
 * @filesource main/pages/phpinfo.php
 * Файл обеспечивает вывод информации о текущих настройках php
 * @since 27.10.2008
 * @author BlaDe39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
if( !defined('KS_ENGINE') ){die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CModuleAdmin.php';

class CmainAIphpinfo extends CModuleAdmin
{
	function Run()
	{
		if($$this->obUser->GetLevel('main')>1)
			throw new CAccessError("MAIN_NO_RIGHT_VIEW_PHP");
		phpinfo();
		die();
	}
}
