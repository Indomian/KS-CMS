<?php
/**
 * @file sitemap/pages/index.php
 * Файл административного интерфейса карты сайта по умолчанию
 *
 * @since 09.01.12
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CModuleAdmin.php';

class CsitemapAIindex extends CModuleAdmin
{
	function Run()
	{
		CUrlParser::get_instance()->Redirect('/admin.php?module=sitemap&page=options');
	}
}