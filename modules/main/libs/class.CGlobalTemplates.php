<?php
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/main/libs/class.CObject.php';

/**
 * @todo Документировать класс
 * @author blade39
 *
 */
class CGlobalTemplates extends CObject
{
	function __construct($sTable='main_path_to_template')
	{
		parent::__construct($sTable);
	}

	function Get($url)
	{
		return $this->GetRecord(array('url_path'=>$url));
	}

	function Set($url,$template)
	{
		$obTemplates=new CTemplates();
		if(!($arTemplates=$obTemplates->GetList())) throw new CError("MAIN_ERROR_SEARCH_TEMPLATES", 0);
		if (in_array($template,$arTemplates))
		{
			if($arData=$this->Get($url))
				$this->Update($arData['id'],array('template_path'=>$template));
			else
				$this->Save('',array('url_path'=>$url,'template_path'=>$template));
			return true;
		}
		return false;
	}

	function Delete($url)
	{
		return $this->DeleteItems(array('url_path'=>$url));
	}

	function DeleteByTemplate($tpl)
	{
		return $this->DeleteItems(array('template_path'=>$tpl));
	}
}