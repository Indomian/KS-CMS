<?php
/**
 * @file main/pages/index.php
 * Файл обработки основных операций модуля main
 * Файл проекта kolos-cms.
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 * @since 30.11.2011
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CModuleAdmin.php';

class CmainAIindex extends CModuleAdmin
{
	private $access_level;

	function __construct($module='main',&$smarty,&$parent)
	{
		parent::__construct($module,$smarty,$parent);
		$this->access_level=10;
	}

	/**
	 * Метод обходит массив пунктов меню и удаляет те, к которым нет прав доступа
	 * @param $arMenu array - массив пунктов меню
	 */
	private function _parseMenu($arMenu)
	{
		$arResult=array();
		foreach($arMenu as $key=>$arSubMenu)
			if(array_key_exists('access',$arSubMenu) && $this->access_level<=$arSubMenu['access'])
			{
				if(array_key_exists('items',$arSubMenu) && is_array($arSubMenu['items']))
					$arSubMenu['items']=$this->_parseMenu($arSubMenu['items']);
				$arResult[$key]=$arSubMenu;
			}
		return $arResult;
	}

	function Run()
	{
		//Проверка прав доступа
		$this->access_level = $this->obUser->GetLevel($this->module);
		if($this->access_level>9)
			throw new CAccessError("MAIN_ACCESS_ADMINISTRATIVE_PART_CLOSED");
		$arMenuTmp=$this->obModules->GetMenu();
		$arMenu=$this->_parseMenu($arMenuTmp['global']['items']);
		$this->smarty->assign('data',$arMenu);
		$page='';
		return $page;
	}
}
