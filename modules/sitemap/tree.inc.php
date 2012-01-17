<?php
/**
 * В файле содержится класс выполняющий построение дерева модуля sitemap
 *
 * @filesource sitemap/tree.inc.php
 * @author blade39 <blade39@kolosstudio.ru>
 * @since 17.01.2012
 * @version 2.6
 */

/* Обязательно вставляем во все файлы для защиты от взлома */
if (!defined("KS_ENGINE")) die("Hacking attempt!");

include_once MODULES_DIR.'/main/libs/class.CModuleTree.php';

class CsitemapTree extends CModuleTree
{
	function __construct($module='sitemap',CSiteTree $obSiteTree)
	{
		parent::__construct($module,$obSiteTree);
		$this->sIcon='/icons_tree/sitemap.png';
	}

	function GetBrunch($key='')
	{
		if($key=='')
			$this->GetRootBrunch();
	}
}