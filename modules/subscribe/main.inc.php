<?php
/**
 * CMS-remote
 * 
 * Created on 17.11.2008
 *
 * Developed by Ilya Doroshko, BlaDe39 <blade39@kolosstudio.ru>
 * 
 */
global $smarty,$KS_IND_dir;

	include_once MODULES_DIR."/subscribe/widgets/function.Subscribe.php";
	$output['main_content']= smarty_function_Subscribe($module_parameters,$smarty);


?>
