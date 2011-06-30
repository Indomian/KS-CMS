<?php
/**
 * @file modules/hints/install/install.php
 * Файл выполняющий операции по установке модуля hints
 * Файл проекта kolos-cms.
 *
 * Создан 25.05.11
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

global $ks_db;

$module_name='hints';
$sContent='';

include 'description.php';
require_once MODULES_DIR.'/main/libs/class.CUserGroup.php';
require_once MODULES_DIR.'/main/libs/class.CModulesAccess.php';
require_once MODULES_DIR.'/main/libs/class.CEventTemplates.php';
include MODULES_DIR.'/'.$module_name.'/install/db_structure.php';

//Список таблиц модуля
$arDBList=array(
	'hints_data',
);
//Получаем список таблиц системы
$arTables=$ks_db->ListTables();

//Определяем режим работы
if(array_key_exists('go',$_POST))
{
	$showButtons=0;
	$arFields=array();
	foreach($arDBList as $sTable)
	{
		//Чистим базу (если были таблицы - потрем)
		if(in_array($sTable,$arTables))
		{
			$ks_db->CheckTable($sTable,$arStructure[$sTable]);
		}
		else
		{
			$ks_db->AddTable($sTable,$arStructure[$sTable]);
		}
	}
	//Прописываем уровни доступа для всех групп
	$USERGROUP=new CUserGroup;
	$arAccess['groups']=$USERGROUP->GetList(array('title'=>'asc'));
	$obAccess=new CModulesAccess();
	//Выполняем сохранение прав доступа
   	if(is_array($arAccess['groups']))
   	{
	   	foreach($arAccess['groups'] as $key=>$value)
		{
			if($value['id']=='1')
			{
				$obAccess->Set($value['id'],$module_name,0);
			}
			else
			{
				$obAccess->Set($value['id'],$module_name,9);
			}
		}
   	}

	$arModule=array(
		'name'=>$arDescription['title'],
		'URL_ident'=>$module_name,
		'directory'=>$module_name,
		'include_global_template'=>1,
		'active'=>1,
		'orderation'=>25,
		'hook_up'=>0,
		'allow_url_edit'=>1,
		'id'=>-1,
	);
	$this->AddAutoField('id');
	$this->Save('',$arModule);
	//Устанавливаем файлы модуля
	$this->InstallAllModuleFiles($module_name);
	$this->AddNotify('SYSTEM_MODULE_INSTALL_OK',$arDescription['title'],NOTIFY_MESSAGE);
}
else
{
	//Если мы не выполняем работу то надо сообщить о настройках модуля перед установкой
	$arFields=array(
	);
	$arFields['text']=array(
		'type'=>'label',
		'title'=>$arDescription['description']
		);
}


