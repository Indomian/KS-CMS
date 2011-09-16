<?php
/**
 * @file install.php
 * Файл выполняющий операции по установке модуля pages
 * В этом файле доступны следующие переменные:
 * $module - текстовый идентификатор устанавливаемого модуля
 * $KS_FS - объект для работы с файловой системой
 * $smarty - движок шаблонизатора
 * Файл проекта kolos-cms.
 *
 * Создан 16.09.11
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6.1
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CUserGroup.php';
require_once MODULES_DIR.'/main/libs/class.CAccess.php';
include_once MODULES_DIR.'/'.$module.'/libs/class.CDummy.php';
include MODULES_DIR.'/'.$module.'/install/description.php';
include MODULES_DIR.'/'.$module.'/install/db_structure.php';

//Определяем режим работы
if(array_key_exists('go',$_POST))
{
	//Список таблиц модуля
	$arDBList=array_keys($arStructure);
	//Получаем список таблиц системы
	$arTables=$this->obDB->ListTables();

	$showButtons=0;
	$arFields=array();
	foreach($arDBList as $sTable)
	{
		//Чистим базу (если были таблицы - потрем)
		if(in_array($sTable,$arTables))
		{
			$this->obDB->CheckTable($sTable,$arStructure[$sTable]);
		}
		else
		{
			$this->obDB->AddTable($sTable,$arStructure[$sTable]);
		}
	}
	//Проверяем наличие корневого элемента
	$obDummy=new CDummy();
	if(!($arRoot=$obDummy->GetRecord(array('id'=>'0'))))
	{
		$arFields=array(
			'id'=>1,
			'title'=>'Созданно при регистрации модуля',
		);
		$obDummy->Save('',$arFields);
	}
	//Прописываем уровни доступа для всех групп
	$USERGROUP=new CUserGroup;
	$obAccess=new CModulesAccess();
	//Выполняем сохранение прав доступа
	if($arAccess=$USERGROUP->GetList(array('title'=>'asc')))
		foreach($arAccess as $key=>$value)
			if($value['id']=='1')
				//Админы
				$obAccess->Set($value['id'],$module,0);
			else
				//Все остальные
				$obAccess->Set($value['id'],$module,10);
	//Собственно добавление модуля
	$arModule=array(
		'name'=>$arDescription['title'],
		'URL_ident'=>$module,
		'directory'=>$module,
		'include_global_template'=>1,
		'active'=>1,
		'orderation'=>100,
		'hook_up'=>0,
		'allow_url_edit'=>1,
	);
	$this->AddAutoField('id');
	$this->Save('',$arModule);
	$this->InstallAllModuleFiles($module);
	$this->AddNotify('SYSTEM_MODULE_INSTALL_OK',$arDescription['title'],NOTIFY_MESSAGE);
}
else
{
	//Если мы не выполняем работу то надо сообщить о настройках модуля перед установкой
	$arFields=array(
		'text'=>array(
			'type'=>'label',
			'title'=>$arDescription['description']
		)
	);
}

