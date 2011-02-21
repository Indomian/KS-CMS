<?php

/**
 * Главный файл модуля навигации
 * 
 * @author DoTJ
 * @version 0.1
 * @since 11.04.2008
 */

/* Защита от взлома */
if (!defined("KS_ENGINE"))
	die("Hacking attempt!");

global $smarty, $KS_IND_matches, $KS_MODULES, $KS_IND_dir, $global_template,  $CNMenu;

/* Идентификатор модуля */
$module_name = "navigation";

try
{
	if($module_parameters['is_widget']==1)
	{
		if(file_exists(MODULES_DIR.'/navigation/widgets/function.'.$module_parameters['action'].'.php'))
		{
			include_once(MODULES_DIR.'/navigation/widgets/function.'.$module_parameters['action'].'.php');
			$output['main_content']=call_user_func('smarty_function_'.$module_parameters['action'],$module_parameters,$smarty);
		}
		else
		{
			throw new CError('SYSTEM_WIDGET_NOT_FOUND',3001);
		}
	}
	else
	{
		if( !isset($module_parameters['local_tpl'], $module_parameters['text_ident']) ) 
		{ 
			return ''; 
		}

		$smarty->assign('menu_data', $CNMenu->create_menu_arr($module_parameters['type']));
		if( isset($module_parameters['global_tpl']) ) 
		{
			$nav_global_template = $module_parameters['global_tpl'].'/navigation/';
		}
		else 
		{			$nav_global_template = '.default/navigation/';
		}

		if($smarty->template_exists($nav_global_template.$module_parameters['local_tpl']) ) 
		{				$output['menu'] = $smarty->fetch($nav_global_template.$module_parameters['local_tpl']);
		}	
		else 
		{			$output = '';
		}

		echo $output['menu'];
	}
}
catch (CError $e)
{
	$output['main_content']=$e;
}

?>