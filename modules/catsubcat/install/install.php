<?php
/**
 * @file install.php
 * Файл выполняющий операции по установке модуля catsubcat
 * Файл проекта kolos-cms.
 *
 * Создан 08.10.10
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.5.4-14
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

global $ks_db,$KS_EVENTS_HANDLER;

$module_name='catsubcat';
$sContent='';

include 'description.php';
require_once MODULES_DIR.'/main/libs/class.CUserGroup.php';
require_once MODULES_DIR.'/main/libs/class.CAccess.php';
include MODULES_DIR.'/'.$module_name.'/install/db_structure.php';
include MODULES_DIR.'/'.$module_name.'/libs/class.CCategory.php';

//Список таблиц модуля
$arDBList=array(
	'catsubcat_catsubcat',
	'catsubcat_element',
	'catsubcat_links'
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
	//Проверяем наличие корневого элемента
	$obCategory=new CCategory();
	if(!($arRoot=$obCategory->GetRecord(array('id'=>'0','parent_id'=>'0','text_ident'=>''))))
	{
		$ks_db->query('SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO"');
		$arFields=array(
			'id'=>0,
			'parent_id'=>0,
			'active'=>1,
			'text_ident'=>'',
			'title'=>'Index',
		);
		$obCategory->Save('',$arFields);
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
				$obAccess->Set($value['id'],$module_name,7);
			}
		}
   	}

	$arModule=array(
		'name'=>$arDescription['title'],
		'URL_ident'=>$module_name,
		'directory'=>$module_name,
		'include_global_template'=>1,
		'active'=>1,
		'orderation'=>1,
		'hook_up'=>0,
		'allow_url_edit'=>1,
		'id'=>-1,
	);
	$this->AddAutoField('id');
	$this->Save('',$arModule);
	//Устанавливаем файлы административного интерфейса
	if($_POST['installTemplates']==1)
	{
		if($arFiles=$KS_FS->GetDirItems(MODULES_DIR.'/catsubcat/install/templates/.default/'))
		{
			if(!file_exists(TEMPLATES_DIR.'/.default/catsubcat/'))
				$KS_FS->makedir(TEMPLATES_DIR.'/.default/catsubcat/');
			foreach($arFiles as $sFile)
			{
				$KS_FS->CopyFile(MODULES_DIR.'/catsubcat/install/templates/.default/'.$sFile,TEMPLATES_DIR.'/.default/catsubcat/'.$sFile,'');
			}
		}
	}
	if($arFiles=$KS_FS->GetDirItems(MODULES_DIR.'/catsubcat/install/templates/admin/'))
	{
		foreach($arFiles as $sFile)
		{
			$KS_FS->CopyFile(MODULES_DIR.'/catsubcat/install/templates/admin/'.$sFile,SYS_TEMPLATES_DIR.'/admin/'.$sFile,'');
		}
	}
	//Устанавливаем скрипты модуля
	if($arFiles=$KS_FS->GetDirItems(MODULES_DIR.'/catsubcat/install/js/'))
	{
		if(!file_exists(ROOT_DIR.JS_DIR))
			$KS_FS->makedir(ROOT_DIR.JS_DIR);
		if(!file_exists(ROOT_DIR.JS_DIR.'/catsubcat/'))
			$KS_FS->makedir(ROOT_DIR.JS_DIR.'/catsubcat/');
		foreach($arFiles as $sFile)
		{
			$KS_FS->CopyFile(MODULES_DIR.'/catsubcat/install/js/'.$sFile,ROOT_DIR.JS_DIR.'/catsubcat/'.$sFile,'');
		}
	}
	$this->AddNotify(SYSTEM_MODULE_INSTALL_OK,$arDescription['title'],NOTIFY_MESSAGE);
}
else
{
	//Если мы не выполняем работу то надо сообщить о настройках модуля перед установкой
	$arFields=array(
		'installTemplates'=>array(
			'type'=>'checkbox',
			'title'=>'Установить шаблоны виджетов',
			'value'=>'1',
		),
	);
	$arFields['text']=array(
		'type'=>'label',
		'title'=>$arDescription['description']
		);
}
?>
