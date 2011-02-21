<?php
/**
 * @file class.CCatsubcatLinks.php
 * Файл класса ссылок модуля "Текстовые страницы"
 * Файл проекта kolos-cms.
 * 
 * Создан 25.02.2010
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.5.5
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

/**
 * Общий класс для работы с привязками
 */

class CElementLinks extends CObject
{
	function __construct($sTable='catsubcat_links')
	{
		parent::__construct($sTable);
		$this->arFields=array('id','element_id','category_id');
	}
}
?>
