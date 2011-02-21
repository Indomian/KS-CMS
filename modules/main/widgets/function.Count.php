<?php

/**
 * Плагин Смарти, определяющий количество элементов в массиве
 * 
 * @author North-E <pushkov@kolosstudio.ru>
 * @version 1.0
 * @since 19.08.2009
 */

function smarty_function_Count($params, &$smarty)
{
	if (!isset($params['array']))
		return "";
		
	if (!is_array($params['array']))
		return "";
		
	return count($params['array']);
}

?>