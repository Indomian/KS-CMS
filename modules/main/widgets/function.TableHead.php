<?php
/**
 * @file function.TableHead.php
 * Функция TableHead осуществляет вывод заголовков таблицы в системе администрирования
 * Файл проекта kolos-cms.
 *
 * Создан 12.10.2010
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.5.4-14
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

function smarty_function_TableHead($params,&$smarty)
{
	global $KS_URL,$KS_MODULES;
	$sResult.='<th';
	if($params['attr']!='')
	{
		$sResult.=str_replace('>','&gt;',$params['attr']);
	}
	$sResult.='>';
	if(IsTextIdent($params['field']))
	{
		if($params['title']=='')
		{
			$field=$KS_MODULES->GetText('field_'.$params['field']);
			if($field=='')
			{
				$field=$params['field'];
			}
		}
		else
		{
			$field=$params['title'];
		}
		if($params['nosort']!='Y' && $params['field']!='' && is_array($params['order']))
		{
			$arUrlParams=array(
				'_CLEAR'=>"PAGE",
				'order'=>$params['field'],
				'dir'=>$params['order']['newdir']
			);
			$sResult.='<nobr><a href="'.$KS_URL->_smarty_get_url($arUrlParams).'">'.$field.'</a>';
			if($params['order']['field']==$params['field'])
			{
				$sResult.='&nbsp;<img src="'.$smarty->get_config_vars('images_path').'/t.gif" style="background:url(\''.$smarty->get_config_vars('images_path').'/';
				if($params['order']['curdir']=='asc')
					$sResult.='up';
				else
					$sResult.='down';
				$sResult.='.gif\') left 50% no-repeat" border="0" width="15" height="10"/>';
			}
			$sResult.='</nobr>';
		}
		else
		{
			$sResult.=$field;
		}

	}
	else
	{
		$sResult.=$params['field'];
	}
	$sResult.='</th>';
	return $sResult;
}
?>
