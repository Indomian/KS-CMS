<?php
/**
 * Файл отвечает за генерацию древовидной структуры лайт-версии сайта
 *
 * @filesource lite.php
 * @author blade39 <blade39@kolosstudio.ru>
 * @since 07.04.2009
 * @version 2.6
 */

/* Обязательно вставляем во все файлы для защиты от взлома */
if (!defined("KS_ENGINE")) die("Hacking attempt!");

include_once MODULES_DIR.'/main/pages/main.php';

class CmainAIlite extends CmainAImain
{
	function Run()
	{
		$this->smarty->assign('modpage','lite');
		parent::Run();
	}
}
