<?php
/**
 * \file uninstall.php
 * Файл настройки удаления модуля Гостевая книга
 * Файл проекта kolos-cms.
 *
 * Создан 14.10.2009
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 1.0
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

global $ks_db;
$module_name='banners';
$sContent='';
include 'description.php';
require_once MODULES_DIR.'/main/libs/class.CUserGroup.php';
require_once MODULES_DIR.'/main/libs/class.CAccess.php';
include MODULES_DIR.'/'.$module_name.'/install/db_structure.php';

if(!$this->IsModule($module_name)) throw new CError('MAIN_MODULE_NOT_REGISTERED');

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
}
else
{
	//Если мы не выполняем работу то надо сообщить о настройках модуля перед установкой
	$arFields=array(
		'deleteData'=>array(
			'type'=>'checkbox',
			'title'=>'Удалить данные модуля &quot;Баннеры&quot;',
			'value'=>'1',
		),
	);
	$showButtons=1;
}
?>
