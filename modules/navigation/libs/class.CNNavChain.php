<?php
/**************************************************************\
/	KS ENGINE
/	(c) 2008 ALL RIGHTS RESERVED
/**************************************************************\
/**************************************************************\
/	Author: Kolos Andrew (DoTJ)
/	http://kolos-studio.ru/
/	http://dotj.ru/
/**************************************************************\
/**************************************************************\
/	Назначение: создание навигационных элементов
/	Версия:	0.1
/	Последняя модификация: 21.05.2008
/**************************************************************\
*/

if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

class CNNavChain extends CBaseObject
{	/* массив навигационной цепочки вида array([0] => array('<URI>' => '<PAGE_NAME>'), [1] => array('<URI>' => '<PAGE_NAME>'), ... ); */
	public $NC;
	static private $instance;
	/**
	 * This implements the 'singleton' design pattern
   	 *
     * @return object CNNavChain The one and only instance
     */
  	static function get_instance()
  	{
	    if (!self::$instance) 
	    {
    		self::$instance = new CNNavChain();
    	}
	    return self::$instance;
  	}

	/**
	 * Добавление нового элемента к массиву навигационной цепочки
	 * @param $uri -- добавляемый URI
	 * @param $name -- добавляемое имя
  	 */
	function NC_add_item($uri, $name) 
	{		$this->NC[] = array('uri' => $uri, 'title' => $name);
	}
	
	function Add($name,$uri=false)
	{
		$this->NC_add_item($uri,$name);
	}
}

?>