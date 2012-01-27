<?php
/**
 * Файл обеспечивает инициализацию и управление сессиями пользователей при работе через консоль
 *
 * @since 24.10.2011
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
if (!defined('KS_ENGINE')) die("Hacking attempt!");

class CSessionManager
{
	protected static $instance;

	function __construct()
	{
	}

	static function get_instance()
	{
		if(!CSessionManager::$instance)
		{
			CSessionManager::$instance=new CSessionManager();
			CSessionManager::$instance->init();
		}
		return CSessionManager::$instance;
	}

	private function init()
	{
		if(array_key_exists('KSSESSID',$_GET))
		{
			session_id($_GET['KSSESSID']);
			unset($_GET['KSSESSID']);
		}
		session_name('KSSESSID');
		if (!session_start())
			throw new Exception("No sessions");
	}

	function Destroy()
	{
		/* Уничтожаем пользовательскую сессию */
		$_SESSION = array();
		unset($_COOKIE[session_name()]);
		session_destroy();
	}
}
 
