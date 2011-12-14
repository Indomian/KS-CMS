<?php
/**
 * В файле находится класс обеспечивающий поиск и подключение глобальных шаблонов
 * @author blade39 <blade39@kolosstudio.ru>
 * @since 15.12.11
 * @version 2.7
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

class CGlobalTemplates extends CObject
{
	function __construct($sTable='main_path_to_template')
	{
		parent::__construct($sTable);
	}

	/**
	 * Метод возвращает имя шаблона по указанному адресу
	 * @param string $url
	 */
	function Get($url)
	{
		return $this->GetRecord(array('url_path'=>$url));
	}

	/**
	 * Метод выполняет привязку адреса к шаблону
	 * @param string $url
	 * @param string $template
	 */
	function Set($url,$template)
	{
		global $ks_db;
		$obTemplates=new CTemplates();
		if(!($arTemplates=$obTemplates->GetList())) throw new CError("MAIN_ERROR_SEARCH_TEMPLATES", 0);
		if (in_array($template,$arTemplates))
		{
			$arFilter=array('url_path'=>$url);
			if($this->Count($arFilter)>0)
				$this->Update($arFilter,array('template_path'=>$template),true);
			else
			{
				$arSave=array(
					'url_path'=>$url,
					'template_path'=>$template
				);
				$this->Save('',$arSave);
			}
			return true;
		}
		return false;
	}

	/**
	 * Метод выполняет удаление привязки по заданному адресу
	 * @param $url
	 */
	function Delete($url)
	{
		$this->DeleteItems(array('url_path'=>$url));
	}

	/**
	 * Метод выполняет удаление привязки по заданному шаблону
	 * @param unknown_type $tpl
	 */
	function DeleteByTemplate($tpl)
	{
		$this->DeleteItems(array('template_path'=>$tpl));
	}
}