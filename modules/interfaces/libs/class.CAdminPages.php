<?php
/**
 * @filesource interfaces/libs/class.CAdminPages.php
 * @since 09.01.12
 * @author BlaDe39 <blade39@kolosstudio.ru>
 * В файле содержится класс обеспечивающий работу постраничной навигации в административном интерфейсе
 */
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/interfaces/libs/class.CPages.php';

/**
 * Класс обеспечивает упрощенную работу с навигацией, без передачи дополнительных параметров,
 * работает как класс CPageNavigation с параметрами заданными по умолчанию+использует значение настройки
 * количества записей на страницу из настроек по умолчанию
 */

class CAdminPages extends CPages
{
	function __construct($visible=false)
	{
		global $KS_MODULES;
		if(!$visible)
			$visible=$KS_MODULES->GetConfigVar('main','admin_items_count',10);
		parent::__construct($visible);
	}
}
