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

/**
 * Устаревший класс
 * @deprecated 2.6
 */
class CNavigation {
	/* массив навигационной цепочки вида array([0] => array('<URI>' => '<PAGE_NAME>'), [1] => array('<URI>' => '<PAGE_NAME>'), ... ); */
	public $NC;

	function __construct() {
		/*
		Инициализация блоковых функций смарти
		*/

  		global $smarty;
  		$smarty->register_function("ksShowNavChain", array($this,"_smarty_ShowNavChain"));
  		throw new CError('SYSTEM_FUNCTION_DEPRECATE');
	}

	function NC_add_item($uri, $name) {
  		/*
  		Добавление нового элемента к массиву навигационной цепочки

  		ВХОДЯЩИЕ ДАННЫЕ:
  			$uri -- добавляемый URI
  			$name -- добавляемое имя
  		*/

		$value[] = $name;

        $this->NC[] = array('uri' => $uri, 'title' => $name);
	}

	function _smarty_ShowNavChain($params) {
    	/*
		Смарти-блоковая функция-плагин
    	*/

    	global $smarty;

    	if( empty($params['tpl']) ) {
    		$params['tpl'] = '.default/etc/nav_chain.tpl';
    	}
    	$NC_array = $this->NC;

        if( is_array($NC_array) ) {
        	for( $i = 0; $i < count($NC_array); $i ++ ) {
	        	$smarty->assign("NC_arr", $NC_array[$i]);
	        	if( $i == (count($NC_array)-1) ) {
	        		/* делимитер не нужен */
		        	$output .= $smarty->fetch($params['tpl']);
		        }
		        else {
		        	/* делимитер нужен */
					$output .= $smarty->fetch($params['tpl']).( (empty($params['delimiter'])) ? ' / ' : $params['delimiter']);
		        }
	        }
        }

        return $output;
	}

}

?>