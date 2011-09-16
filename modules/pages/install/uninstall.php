<?php
/**
 * @file uninstall.php
 * Файл настройки удаления модуля dummy
 * Файл проекта kolos-cms.
 *
 * Создан 31.08.11
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CUserGroup.php';
require_once MODULES_DIR.'/main/libs/class.CAccess.php';
include MODULES_DIR.'/'.$module.'/install/description.php';
include MODULES_DIR.'/'.$module.'/install/db_structure.php';

//Определяем режим работы
if(array_key_exists('go',$_POST))
{
	//Список таблиц модуля
	$arDBList=array_keys($arStructure);
	$showButtons=0;
	$arTables=$this->obDB->ListTables();

	if(isset($_POST['deleteData']) && intval($_POST['deleteData'])>0)
	{
		foreach($arDBList as $sTable)
			if(in_array(PREFIX.$sTable,$arTables)) $this->obDB->query("DROP TABLE ".PREFIX.$sTable);
		//Удаляем записи о дополнительных полях
		$obFields=new CFields();
		$obFields->DeleteItems(array('module'=>$module));
	}
	//Удаляем запись о модуле
	$this->DeleteItems(array('directory'=>$module));
	$this->UnInstallAllModuleFiles($module);
	//Сообщаем что все ок
	$this->AddNotify('SYSTEM_MODULE_UNINSTALL_OK',$arDescription['title'],NOTIFY_MESSAGE);
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
	);
	$showButtons=1;
}
