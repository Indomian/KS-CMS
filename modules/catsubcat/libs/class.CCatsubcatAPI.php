<?php
/**
 * @filesource catsubcat/libs/class.CCatsubcatAPI.php
 * Файл содержит в себе класс АПИ модуля catsubcat
 * Файл проекта kolos-cms.
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 * @since 19.02.2012
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/main/libs/class.CBaseAPI.php';
include_once MODULES_DIR.'/catsubcat/libs/class.CCommonElement.php';
include_once MODULES_DIR.'/catsubcat/libs/class.CCommonCategory.php';
include_once MODULES_DIR.'/catsubcat/libs/class.CCatsubcatStorage.php';

/**
 * Класс обеспечивает высокоуровневые функции для модуля wave
 */
class CCatsubcatAPI extends CBaseAPI
{
	static private $obInstance;
	private $obCategory;
	private $obElement;
	private $obStorage;

	/**
	 * Метод заменяющий конструктор. Используется для инициализации.
	 */
	private function init()
	{
		$this->obCategory=false;
		$this->obElement=false;
		$this->obStorage=false;
	}

	/**
	 * This implements the 'singleton' design pattern
   	 *
     * @return object CMain The one and only instance
     */
  	static function get_instance()
  	{
	    if (!self::$obInstance)
	    {
    		self::$obInstance = new CCatsubcatAPI();
      		self::$obInstance->init();  // init AFTER object was linked with self::$instance
    	}
	    return self::$obInstance;
  	}

	/**
	 * Метод возвращает объект постов
	 */
	function Element()
	{
		if(!$this->obElement)
			$this->obElement=new CElement($this->Category());
		return $this->obElement;
	}

	function Category($obElement=NULL)
	{
		if(!$this->obCategory)
			$this->obCategory=new CCategory($obElement);
		return $this->obCategory;
	}

	function Storage()
	{
		if(!$this->obStorage)
			$this->obStorage=new CCatsubcatStorage();
		return $this->obStorage;
	}
}