<?php
/**
 * @file uninstall.php
 * Файл настройки удаления модуля "Комментарии"
 * Файл проекта kolos-cms.
 *
 * @since 27.10.2010
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.5.4-14
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

global $ks_db,$KS_EVENTS_HANDLER;

$module_name='wave';
$sContent='';

include 'description.php';
require_once MODULES_DIR.'/main/libs/class.CUserGroup.php';
require_once MODULES_DIR.'/main/libs/class.CAccess.php';

if(!$this->IsModule('wave')) throw new CError('MAIN_MODULE_NOT_REGISTERED');

//Список таблиц модуля
$arDBList=array(
	'wave_posts',
);

//Определяем режим работы
if(array_key_exists('go',$_POST))
{
	$showButtons=0;
	$arTables=$ks_db->ListTables();
	if(intval($_POST['deleteData'])>0)
	{
		foreach($arDBList as $sTable)
		{
			if(in_array(PREFIX.$sTable,$arTables)) $ks_db->query("DROP TABLE ".PREFIX.$sTable);
		}
	}
	//Удаляем запись о модуле
	$this->DeleteItems(array('directory'=>$module_name));
	//Сообщаем что все ок
	//Удаляем файлы административных шаблонов
	if($arFiles=$KS_FS->GetDirItems(MODULES_DIR.'/wave/install/templates/admin/'))
	{
		foreach($arFiles as $sFile)
		{
			$KS_FS->Remove(SYS_TEMPLATES_DIR.'/admin/'.$sFile);
		}
	}
	if(intval($_POST['deleteTemplates'])>0)
	{
		if(file_exists(TEMPLATES_DIR.'/.default/wave/'))
		{
			$KS_FS->Remove(TEMPLATES_DIR.'/.default/wave/');
		}
	}
	$this->AddNotify(SYSTEM_MODULE_UNINSTALL_OK,$arDescription['title'],NOTIFY_MESSAGE);
}
else
{
	//Если мы не выполняем работу то надо сообщить о настройках модуля перед установкой
	$arFields=array(
		'deleteData'=>array(
			'type'=>'checkbox',
			'title'=>'Удалить все комментарии',
			'value'=>'1',
		),
		'deleteTemplates'=>array(
			'type'=>'checkbox',
			'title'=>'Удалить шаблоны виджетов в шаблоне по умолчанию',
			'value'=>'1',
		),
	);
	$showButtons=1;
}
?>
