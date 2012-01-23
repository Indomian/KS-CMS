<?php
/**
 * @file subscribe/pages/index.php
 * Файл управления рассылками
 * Файл проекта kolos-cms.
 *
 * Создан 01.02.2010
 *
 * @author fox <fox@kolosstudio.ru>, blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR."/main/libs/class.CUserGroup.php";
require_once MODULES_DIR."/main/libs/class.CAccess.php";
require_once MODULES_DIR.'/main/libs/class.CModuleAdmin.php';

class CsubscribeAIindex extends CModuleAdmin
{
	private $iAccessLevel;
	private $obUserGroups;
	private $obAccess;
	private $obAPI;

	function __construct($module='subscribe',&$smarty,&$parent)
	{
		parent::__construct($module,$smarty,$parent);
		$this->iAccessLevel=$this->obUser->GetLevel($this->module);
		$this->obAPI=CSubscribeAPI::get_instance();
		/* Объект для работы с группами пользователей */
		$this->obUserGroups = new CUserGroup();
		/* Объект для работы с правами доступа пользователей к модулям */
		$this->obAccess = new CModulesAccess();
	}

	/**
	 * Метод выполняет подготовку данных для вывода страницы списка тем рассылки
	 * @return string Название шаблона для вывода
	 */
	protected function Table()
	{
		/* Поля, по которым можно отсортировать */
		$arSortFields = array("id", "name", "date_add","description","active");
		//Определяем порядок сортировки записей
		list($sOrderField,$sOrderDir)=$this->InitSort($arSortFields);
		$sNewDir=($sOrderDir=='desc')?'asc':'desc';
		$arSort=array($sOrderField=>$sOrderDir);
		$arFilter=array();
		$obPages=$this->InitPages();
		$totalNews = $this->obAPI->Newsletter()->count();
		/* Для постраничной навигации */
		$this->smarty->assign("pages", $obPages->GetPages($totalNews));
		/* Количество отображаемых на странице голосований */
		$this->smarty->assign("num_visible", $obPages->GetVisible());
		/* Параметры сортировки */
		$this->smarty->assign("order", array('newdir' => $sNewDir, 'curdir' => $sOrderDir, 'field' => $sOrderField));
		$this->smarty->assign("list", $this->obAPI->Newsletter()->GetList($arSort, $arFilter, $obPages->GetLimits($totalNews)));
		return '';
	}

	/**
	 * Метод выполняет сохранение записи темы рассылки
	 * @return string Название шаблона для вывода
	 */
	protected function Save()
	{
		/* Параметры для сохранения */
		$arData = $_POST;
		/* Попытка сохранения данных */
		$bError=0;
		/* Поле для автозаполнения */
		if (strlen(trim($arData['SB_name'])) == 0)
			$bError+=$this->obModules->AddNotify("SUBSCRIBE_NAME_ERROR");
		if(!isset($arData['SB_active']))
			$arData['SB_active']=0;
		else
			$arData['SB_active']=intval($arData['SB_active']);
		/* Сохранение*/
		if($bError==0)
		{
			try
			{
				if($id = $this->obAPI->Newsletter()->Save('SB_', $arData))
				{
					/* Теперь остаётся сохранить выставленные уровни доступа */
					if (is_array($_POST['SB_groupLevel']))
					{
						$groups_levels = $_POST['SB_groupLevel'];
						if (count($groups_levels))
							foreach ($groups_levels as $group_id => $group_levels)
								if (count($group_levels))
								{
									$max_group_level = 10;
									foreach ($group_levels as $group_level)
										if ($group_level < $max_group_level)
											$max_group_level = $group_level;
									/* Формируем массив для записи */
									$usergroups_levels_row = array(
										'newsletter_id' => $id,
										'usergroup_id' => $group_id,
										'level' => $max_group_level
									);

									/* Параметры идентификации уровня доступа */
									$arFilter = array('newsletter_id' => $id, 'usergroup_id' => $group_id);
									/* Проверяем запись на существование */
									if($row = $this->obAPI->Access()->GetRecord($arFilter))
										$this->obAPI->Access()->Update($row['id'], $usergroups_levels_row);
									elseif (!$this->obAPI->Access()->Save("", $usergroups_levels_row))
										throw new CError("SUBSCRIBE_NEWSLETTER_ACCESS_SAVE_ERROR");
								}
					}
				}
				else
					throw new CError('SUBSCRIBE_NEWSLETTER_SAVE_ERROR');
				$this->obModules->AddNotify('SUBSCRIBE_NEWSLETTER_SAVE_OK','',NOTIFY_MESSAGE);
				/* Осуществляем редирект после успешного сохранения */
				if (array_key_exists('update', $_REQUEST))
					$this->obUrl->Redirect("admin.php?".$this->obUrl->GetUrl(array('action')).'&action=edit&id='.$id);
				else
					$this->obUrl->Redirect("admin.php?".$this->obUrl->GetUrl(array('action','p')));
			}
			catch(CError $e)
			{
				$this->obModules->AddNotify($e->getMessage());
			}
		}
		$data=$obNews->GetRecordFromPost('SB_',$_POST);
		return $this->EditForm($data);
	}

	/**
	 * Метод выполняет подготовку данных для вывода формы редактирования темы подписки
	 * @param mixed $data массив записи или false если нужно сформировать данные для новой записи
	 * @return string название шаблона
	 */
	protected function EditForm($data=false)
	{
		if(!$data)
		{
			$data=array(
				'id'=>-1,
				'active'=>1,
				'orderation'=> 10
			);
		}
		/* Список групп пользователей*/
		$access['usergroups'] = $this->obUserGroups->GetList();
		/* Уровни доступа к модулю */
		$access['levels'] = $this->obModules->GetAccessArray($this->module);
		unset($access['levels'][0]);
		/* Забиваем уровни доступа групп пользователей сначала по умолчанию */
		$ug_levels = $this->obAccess->GetList(array('id' => "asc"), array('module' => $this->module));
		$usergroups_levels = array();
		if (is_array($ug_levels) && count($ug_levels))
			foreach($ug_levels as $ug_level)
				$usergroups_levels[$ug_level['group_id']] = $ug_level;

		if($real_ug_levels = $this->obAPI->Access()->GetList(false, array('newsletter_id' => $data['id'])))
			foreach ($real_ug_levels as $level_item)
				$usergroups_levels[$level_item['usergroup_id']]['level'] = $level_item['level'];
		else
			foreach ($usergroups_levels as $usergroup_level_key => $usergroup_level)
				if ($usergroup_level['level'] == 0)
					$usergroups_levels[$usergroup_level_key]['level'] = 5;
		$access['usergroups_levels'] = $usergroups_levels;
		$this->smarty->assign("data", $data);
		$this->smarty->assign("access", $access);
		return '_edit';
	}

	/**
	 * Метод выполняет общие действия над темами рассылки
	 */
	protected function CommonActions()
	{
		if(isset($_POST['sel']['cat']) && is_array($_POST['sel']['cat']) && count($_POST['sel']['cat'])>0)
		{
			$arIds = array();
			foreach ($_POST['sel']['cat'] as $key=>$val)
				if($val==1) $arIds[]=intval($key);
			$arIds=array_unique($arIds);
			if($arIds=$this->obAPI->Newsletter()->GetList(false,array('->id'=>$arIds),false,array('id')))
			{
				$arIds=array_keys($arIds);
				if (isset($_REQUEST['comdel']))
					$this->obAPI->Newsletter()->DeleteItems(array('->id'=>$arIds));
				elseif (isset($_REQUEST['comact']))
					$this->obAPI->Newsletter()->Update($arIds, array('active' => "1"));
				elseif (isset($_REQUEST['comdea']))
					$this->obAPI->Newsletter()->Update($arIds, array('active' => "0"));
				$this->obModules->AddNotify('SUBSCRIBE_NEWSLETTER_COMMON_OPERATION_OK',0,NOTIFY_MESSAGE);
			}
		}
		$this->obUrl->Redirect("admin.php?".$this->obUrl->GetUrl(array()));
	}

	/**
	 * Метод выполняет обработку запроса от пользователя и определяет выполняемую операцию
	 * @return string имя шаблона для отображения
	 */
	function Run()
	{
		if($this->iAccessLevel>5)
			throw new CAccessError('SUBSCRIBE_NOT_ACCESS_USERS');
		$this->ParseAction();
		$page='';
		$data=false;
		$id=0;
		if(isset($_REQUEST['id']))
			$id=intval($_REQUEST['id']);
		switch($this->sAction)
		{
			case "common":
				$this->CommonActions();
			break;
			case "edit":
				$data=$this->obAPI->Newsletter()->GetRecord(array('id'=>$id));
				if(!$data) throw new CError('SUBSCRIBE_NEWSLETTER_NOT_FOUND');
			case "new":
				$page=$this->EditForm($data);
			break;
			case "save":
				$page=$this->Save($id);
			break;
			case "delete":
				if($arRecord=$this->obAPI->Newsletter()->GetById($id))
				{
					$this->obAPI->Newsletter()->Delete($arRecord['id']);
					$this->obModules->AddNotify('SUBSCRIBE_NEWSLETTER_DELETE_OK',0,NOTIFY_MESSAGE);
				}
				else $this->obModules->AddNotify('SUBSCRIBE_NEWSLETTER_NOT_FOUND');
				$this->obUrl->Redirect("admin.php?".$this->obUrl->GetUrl(array("action", "id")));
			break;
			default:
				$page=$this->Table();
			break;
		}
		return '_newsletters'.$page;
	}
}

