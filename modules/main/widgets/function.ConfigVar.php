<?php

/**
 * Плагин Смарти, отображающий значение переменной настроек
 *
 * @author BlaDe39 <pushkov@kolosstudio.ru>
 * @version 2.6
 * @since 28.06.2011
 */

function smarty_function_ConfigVar($params, &$smarty)
{
	global $KS_MODULES;
	if(isset($params['module']) && isset($params['var']))
	{
		$default='';
		if(isset($params['default'])) $default=$params['default'];
		return $KS_MODULES->GetConfigVar($params['module'],$params['var'],$default);
	}
	return '';
}
