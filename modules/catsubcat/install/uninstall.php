<?php
/**
 * @file uninstall.php
 * Файл настройки удаления модуля catsubcat
 * Файл проекта kolos-cms.
 *
 * Создан 08.10.10
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.5.4-14
 * @todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

global $ks_db,$KS_EVENTS_HANDLER;

$module_name='catsubcat';
$sContent='';

include 'description.php';
require_once MODULES_DIR.'/main/libs/class.CUserGroup.php';
require_once MODULES_DIR.'/main/libs/class.CAccess.php';

if(!$this->IsModule('catsubcat')) throw new CError('MAIN_MODULE_NOT_REGISTERED');

//Список таблиц модуля
$arDBList=array(
	'catsubcat_catsubcat',
	'catsubcat_element',
	'catsubcat_links'
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
		//Удаляем записи о дополнительных полях
		if (class_exists('CFields'))
		{
			$obFields=new CFields();
			if($arFields=$obFields->GetList(Array('id'=>'asc'),Array('module'=>$module_name)))
			{
				foreach($arFields as $arField)
				{
					$obFields->Delete($arField['id']);
				}
			}
		}
	}
	//Удаляем запись о модуле
	$this->DeleteItems(array('directory'=>$module_name));
	//Сообщаем что все ок
	$this->AddNotify(SYSTEM_MODULE_UNINSTALL_OK,$arDescription['title'],NOTIFY_MESSAGE);
	//Удаляем файлы административных шаблонов
	if($arFiles=$KS_FS->GetDirItems(MODULES_DIR.'/catsubcat/install/templates/admin/'))
	{
		foreach($arFiles as $sFile)
		{
			$KS_FS->Remove(SYS_TEMPLATES_DIR.'/admin/'.$sFile);
		}
	}
	if(intval($_POST['deleteTemplates'])>0)
	{
		if(file_exists(TEMPLATES_DIR.'/.default/catsubcat/'))
		{
			$KS_FS->Remove(TEMPLATES_DIR.'/.default/catsubcat/');
		}
	}
}
else
{
	//Если мы не выполняем работу то надо сообщить о настройках модуля перед установкой
	$arFields=array(
		'deleteData'=>array(
			'type'=>'checkbox',
			'title'=>'Удалить данные модуля (страницы, разделы)',
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
