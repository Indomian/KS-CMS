<?php
/**
 * \file admin.inc.php
 * Файл для управления настройками интерфейса
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

$module_name='interfaces';
$access_level=$USER->GetLevel($module_name);
if($access_level>0) throw new CAccessError('INTERFACES_ACCESS_DENIED');

if(!preg_match('#[a-z0-9]*#',$_GET['page'])) throw new CError('SYSTEM_WRONG_ADMIN_PATH',1001);

if(file_exists(MODULES_DIR.'/'.$module_name.'/pages/'.$_GET['page'].'.php'))
{
	include  MODULES_DIR.'/'.$module_name.'/pages/'.$_GET['page'].'.php';
}
else
{
	include MODULES_DIR.'/'.$module_name.'/pages/smilies.php';
}
?>
