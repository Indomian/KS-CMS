<?php
/**
 * @file modules/subscribe/pages/newsletters.php
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
	private $access_level;
	private $obNews;
	private $obPages;
	private $obUserGroups;
	private $obAccess;
	private $obSubsUsergroupsLevels;

	function __construct($module='subscribe',&$smarty,&$parent)
	{
		parent::__construct($module,$smarty,$parent);
		$this->access_level=$this->obUser->GetLevel($this->module);
		$this->obNews = new CObject('subscribe_newsletters');
		/* Объект для работы с группами пользователей */
		$this->obUserGroups = new CUserGroup();
		/* Объект для работы с правами доступа пользователей к модулям */
		$this->obAccess = new CModulesAccess();
		$this->obSubsUsergroupsLevels= new CObject('subscribe_usergroups_levels');
	}

	function Table()
	{
		/* Поля, по которым можно отсортировать */
		$arSortFields = array("id", "name", "date_add","description","active");
		//Определяем порядок сортировки записей
		list($sOrderField,$sOrderDir)=$this->InitSort($arSortFields,$_REQUEST['order'],$_REQUEST['dir']);
		$sNewDir=($sOrderDir=='desc')?'asc':'desc';
		$arSort=array($sOrderField=>$sOrderDir);
		$arFilter=array();
		$obPages = new CPages();
		$totalNews = $this->obNews->count();
		/* Для постраничной навигации */
		$this->smarty->assign("pages", $obPages->GetPages($totalNews));
		/* Количество отображаемых на странице голосований */
		$this->smarty->assign("num_visible", $obPages->GetVisible());
		/* Параметры сортировки */
		$this->smarty->assign("order", array('newdir' => $sNewDir, 'curdir' => $sOrderDir, 'field' => $sOrderField));
		$this->smarty->assign("list", $this->obNews->GetList($arSort, $arFilter, $obPages->GetLimits($totalNews)));
		return '';
	}

	function Save($id)
	{
		/* Параметры для сохранения */
		$arData = $_POST;
		/* Попытка сохранения данных */
		$bError=0;
		/* Поле для автозаполнения */
		if (strlen(trim($arData['SB_name'])) == 0)
			$bError+=$this->obModules->AddNotify("SUBSCRIBE_NAME_ERROR");
		/* Сохранение*/
		if($bError==0)
		{
			try
			{
				if($id = $this->obNews->Save('SB_', $arData))
				{
					/* Теперь остаётся сохранить выставленные уровни доступа */
					if (is_array($_POST['SB_groupLevel']))
					{
						$groups_levels = $_POST['SB_groupLevel'];
						if (count($groups_levels))
							foreach ($groups_levels as $group_id => $group_levels)
							{
								if (count($group_levels))
								{
									$max_group_level = 10;
									foreach ($group_levels as $group_level)
									{
										if ($group_level < $max_group_level)
											$max_group_level = $group_level;
									}
									/* Формируем массив для записи */
									$usergroups_levels_row = array('newsletter_id' => $id, 'usergroup_id' => $group_id, 'level' => $max_group_level);

									/* Параметры идентификации уровня доступа */
									$ident_filter = array('newsletter_id' => $id, 'usergroup_id' => $group_id);

									/* Проверяем запись на существование */
									$row = $this->obSubsUsergroupsLevels->GetRecord($ident_filter);
									if (is_array($row) && count($row) > 0)
										$this->obSubsUsergroupsLevels->Update($row['id'], $usergroups_levels_row);
									elseif (!$this->obSubsUsergroupsLevels->Save("", $usergroups_levels_row))
										throw new CError("SUBSCRIBE_NEWSLETTER_ACCESS_SAVE_ERROR");
								}
							}
					}
				}
				else
				{
					throw new CError('SUBSCRIBE_NEWSLETTER_SAVE_ERROR');
				}
				$this->obModules->AddNotify('SUBSCRIBE_NEWSLETTER_SAVE_OK','',NOTIFY_MESSAGE);
				/* Осуществляем редирект после успешного сохранения */
				if (array_key_exists('update', $_REQUEST))
					CUrlParser::get_instance()->Redirect("admin.php?".CUrlParser::get_instance()->GetUrl(array('action')).'&action=edit&id='.$id);
				else
					CUrlParser::get_instance()->Redirect("admin.php?".CUrlParser::get_instance()->GetUrl(array('action','p')));
			}
			catch(CError $e)
			{
				$this->obModules->AddNotify($e->getMessage());
			}
		}
		$data=$obNews->GetRecordFromPost('SB_',$_POST);
		foreach ($usergroups_levels as $usergroup_level_key => $usergroup_level)
		{
			if ($usergroup_level['level'] == 0)
				$usergroups_levels[$usergroup_level_key]['level'] = 5;
		}
		$access['usergroups_levels'] = $usergroups_levels;

		$this->smarty->assign('data',$data);
		$tihs->smarty->assign("access", $access);
		return '_edit';
	}

	function EditForm($data=false)
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

		if($real_ug_levels = $this->obSubsUsergroupsLevels->GetList(false, array('newsletter_id' => $data['id'])))
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

	function Run()
	{
		$action='';
		if(isset($_REQUEST['action']))
			$action=$_REQUEST['action'];

		$page='';
		$data=false;
		$id=0;
		if(isset($_REQUEST['id'])) $id=intval($_REQUEST['id']);
		switch($action)
		{
			case "common":
				$request_ids = array();
				$input_array = $_POST;
				if (count($input_array))
					foreach ($input_array as $variable => $value)
						if (preg_match("#^common_([0-9]+)$#", $variable, $subpatterns))
							$request_ids[] = intval($subpatterns[1]);

				if (count($request_ids) > 0)
				{
					if (isset($_REQUEST['comdel']))
					{
						/* Удаление*/
						foreach ($request_ids as $id)
						{
							$obNews->Delete($id);
						}
					}
					elseif (isset($_REQUEST['comact']))
					{
						/* Активация */
						foreach ($request_ids as $id)
							$obNews->Update($id, array('active' => "1"));
					}
					elseif (isset($_REQUEST['comdea']))
					{
						/* Деактивация */
						foreach ($request_ids as $id)
							$obNews->Update($id, array('active' => "0"));
					}
				}

				/* Возвращаемся к списку опросов */
				CUrlParser::Redirect("admin.php?" . $KS_URL->GetUrl(array()));
			break;

			case "edit":
				if($data=$this->obNews->GetRecord(array('id'=>$id)))
				{

				}
				else
				{
					throw new CError('SUBSCRIBE_NEWSLETTER_NOT_FOUND');
				}
			case "new":
				/* Новое */
				$page=$this->EditForm($data);
			break;

			/* Сохранение */
			case "save":
				$page=$this->Save($id);
			break;

			/* Удаление */
			case "delete":
				/* Идентификатор */
				$id = intval($_REQUEST['id']);


				$obNews->Delete($id);

				/* В случае успеха (или не успеха - как повезёт) делаем редирект */
				CUrlParser::Redirect("admin.php?" . $KS_URL->GetUrl(array("ACTION", "id")));
			break;

			default:
				$page=$this->Table();
			break;
		}
		return '_newsletters'.$page;
	}
}

