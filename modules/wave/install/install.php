<?php
/**
 * @file install.php
 * Файл выполняющий операции по установке модуля "Комментарии"
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
include MODULES_DIR.'/wave/install/db_structure.php';

//Список таблиц модуля
$arDBList=array(
	'wave_posts',
	'wave_rating_locks',
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
				$obAccess->Set($value['id'],$module_name,8);
			}
		}
   	}

	$arModule=array(
		'name'=>$arDescription['title'],
		'URL_ident'=>'',
		'directory'=>$module_name,
		'include_global_template'=>1,
		'active'=>1,
		'orderation'=>5,
		'hook_up'=>0,
		'allow_url_edit'=>0,
		'id'=>-1,
	);
	$this->AddAutoField('id');
	$this->Save('',$arModule);
	//Устанавливаем файлы административного интерфейса
	if($_POST['installTemplates']==1)
	{
		if($arFiles=$KS_FS->GetDirItems(MODULES_DIR.'/wave/install/templates/.default/'))
		{
			if(!file_exists(TEMPLATES_DIR.'/.default/wave/'))
				$KS_FS->makedir(TEMPLATES_DIR.'/.default/wave/');
			foreach($arFiles as $sFile)
			{
				$KS_FS->CopyFile(MODULES_DIR.'/wave/install/templates/.default/'.$sFile,TEMPLATES_DIR.'/.default/wave/'.$sFile,'');
			}
		}
	}
	//Устанавливаем системные шаблоны
	if($arFiles=$KS_FS->GetDirItems(MODULES_DIR.'/wave/install/templates/admin/'))
	{
		foreach($arFiles as $sFile)
		{
			$KS_FS->CopyFile(MODULES_DIR.'/wave/install/templates/admin/'.$sFile,SYS_TEMPLATES_DIR.'/admin/'.$sFile,'');
		}
	}
	//Устанавливаем скрипты модуля
	if($arFiles=$KS_FS->GetDirItems(MODULES_DIR.'/wave/install/js/'))
	{
		if(!file_exists(ROOT_DIR.JS_DIR))
			$KS_FS->makedir(ROOT_DIR.JS_DIR);
		if(!file_exists(ROOT_DIR.JS_DIR.'/wave/'))
			$KS_FS->makedir(ROOT_DIR.JS_DIR.'/wave/');
		foreach($arFiles as $sFile)
		{
			$KS_FS->CopyFile(MODULES_DIR.'/wave/install/js/'.$sFile,ROOT_DIR.JS_DIR.'/wave/'.$sFile,'');
		}
	}
	$this->AddNotify('SYSTEM_MODULE_INSTALL_OK',$arDescription['title'],NOTIFY_MESSAGE);
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
