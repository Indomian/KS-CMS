<?php
/**
 * @file install.php
 * Файл выполняющий операции по установке модуля Рассылка сообщений
 * Файл проекта kolos-cms.
 *
 * Создан 24.08.2009
 *
 * @author Konstantin Kuznetsov <lopikun@gmail.com>, blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CEventTemplates.php';
require_once MODULES_DIR.'/main/libs/class.CUserGroup.php';
require_once MODULES_DIR.'/main/libs/class.CAccess.php';
include MODULES_DIR.'/'.$module.'/install/description.php';
include MODULES_DIR.'/'.$module.'/install/db_structure.php';


//Определяем режим работы
if(array_key_exists('go',$_POST))
{
	$showButtons=0;
	$arFields=array();
	//Прописываем уровни доступа для всех групп
	$USERGROUP=new CUserGroup;
	$arAccess['groups']=$USERGROUP->GetList(array('title'=>'asc'));
	$obAccess=new CModulesAccess();
	//Выполняем сохранение прав доступа
   	if(is_array($arAccess['groups']))
	   	foreach($arAccess['groups'] as $key=>$value)
			if($value['id']=='1')
				$obAccess->Set($value['id'],$module,0);
			else
				$obAccess->Set($value['id'],$module,8);

	/*Содаем таблицы для модуля*/
	$arDBList=array_keys($arStructure);
	//Получаем список таблиц системы
	$arTables=$this->obDB->ListTables();
	foreach($arDBList as $sTable)
		if(in_array($sTable,$arTables))
			$this->obDB->CheckTable($sTable,$arStructure[$sTable]);
		else
			$this->obDB->AddTable($sTable,$arStructure[$sTable]);

	//Добавляем файлы почтовых шаблонов в систему
	$obTpl=new _CEventTemplates();
	$arNewTemplates=array(
		'subscribe.message.tpl'=>array('file_id'=>'subscribe.message.tpl','title'=>'Шаблон сообщения рассылки'),
		'subscribe.activate.tpl'=>array('file_id'=>'subscribe.activate.tpl','title'=>'Подтверждение подписки'),
	);
	$arTemplates=$obTpl->GetList(array('id'=>'asc'),array('->file_id'=>array_keys($arNewTemplates)));
	if(is_array($arTemplates) && count($arTemplates)>0)
		foreach($arTemplates as $arItem)
			unset ($arNewTemplates[$arItem['file_id']]);
	foreach($arNewTemplates as $key=>$arItem)
		$obTpl->Save('',$arItem);

	//Собственно добавление модуля
	$arModule=array(
		'name'=>$arDescription['title'],
		'URL_ident'=>$module,
		'directory'=>$module,
		'include_global_template'=>1,
		'active'=>1,
		'orderation'=>50,
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
	$arFields=array();
	$arFields['text']=array(
		'type'=>'label',
		'title'=>$arDescription['description']
		);
}
