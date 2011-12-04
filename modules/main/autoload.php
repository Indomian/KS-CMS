<?php
/**
 * Файл обеспечивает регистрацию автозагрузчика классов модуля main
 */

function __KSCMSMainAutoLoader($sClassName)
{
	if(file_exists(MODULES_DIR.'/main/libs/class.'.$sClassName.'.php'))
		require_once  MODULES_DIR.'/main/libs/class.'.$sClassName.'.php';
	else
		return false;
}

if(!spl_autoload_register('__KSCMSMainAutoLoader'))
	throw new Exception('SPL_AUTOLOAD_FAIL');