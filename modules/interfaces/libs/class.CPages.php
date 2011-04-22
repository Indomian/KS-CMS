<?php
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/interfaces/libs/class.CPageNavigation.php';

/**
 * Класс обеспечивает упрощенную работу с навигацией, без передачи дополнительных параметров,
 * работает как класс CPageNavigation с параметрами заданными по умолчанию
 */

class CPages extends CPageNavigation
{
	function __construct($visible=false)
	{
		parent::__construct(NULL,false,$visible,false);
	}

	function SetItems($i)
	{
		$this->iItems=intval($i);
	}
}