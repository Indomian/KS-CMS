<?php
/**
 * \file class.CSmile.php
 * В файле находится класс выполняющий работу со смайликами
 * Файл проекта kolos-cms.
 * 
 * Создан 08.06.2009
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 1.0
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/interfaces/libs/class.CBBParser.php';

/**
 * Класс работает со смайликами и текстом, выполняет преобразование текста
 */
class CSmile extends CTextParser
{
	function __construct($sTable='interfaces_smiles')
	{
		parent::__construct($sTable);
		$this->sUploadPath='interfaces/smilies/';
		$this->arFields=array('id','smile','img','group');
	}
	
	function Parse($text)
	{
		$arSmiles=$this->GetList(array('smile'=>'asc'));
		$arCodes=array();
		$arImages=array();
		foreach($arSmiles as $i=>$item)
		{
			$arCodes[]=$item['smile'];
			$arImages[]='<img src="/uploads/'.$item['img'].'">';
		}
		return str_replace($arCodes,$arImages,$text);
	}
	
	function Convert($text)
	{
		
	}
	
	/**
	 * Метод производит удаление элементов из таблицы по фильтру.
	 * \param $arFilter массив фильтрации
	 * \sa CObject::_GenWhere()
	 * \return true если удаение прошло успешно, false - если удаление не удалось
	 */
	
	function DeleteItems($arFilter)
	{
		global $ks_db;
		$arList=$this->GetList(array('id'=>'asc'),$arFilter);
		foreach($arList as $arItem)
		{
			@unlink(ROOT_DIR.'/uploads/'.$arItem['img']);
		}
		$sWhere=$this->_GenWhere($arFilter);
		$sWhere=preg_replace('# [a-z_\-]+\.#i',' ',$sWhere);
		if (strlen($sWhere)>0)
		{
			
			$query="DELETE FROM ".PREFIX.$this->sTable.$sWhere;
			$ks_db->query($query);
			return true;
		}
		return false;
	}

	function DeleteByIds($ids)
	{
		global $ks_db;
		if (is_array($ids))
		{
		$where=join('\', \'',$ids);
		$arList=$this->GetList(array('id'=>'asc'),array('->id'=>"('".$where."')"));
		foreach($arList as $arItem)
		{
			@unlink(ROOT_DIR.'/uploads/'.$arItem['img']);
		}
		$query="DELETE FROM ".PREFIX.$this->sTable." WHERE id IN ('".$where."')";
		$ks_db->query($query);
		}
	}
}
?>
