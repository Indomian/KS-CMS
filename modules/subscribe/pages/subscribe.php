<?php
/**
 * @file modules/subscribe/pages/subscribe.php
 * Файл управления подписчиками рассылок
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

class CsubscribeAIsubscribe extends CModuleAdmin
{
	private $access_level;
	private $obNews;
	private $obReleases;
	private $obUsers;
	private $obSubscribe;
	private $obPages;
	private $obUserGroups;
	private $obAccess;
	private $obSubsUsergroupsLevels;

	function __construct($module='subscribe',&$smarty,&$parent)
	{
		parent::__construct($module,$smarty,$parent);
		$this->access_level=$this->obUser->GetLevel($this->module);
		$this->obSubscribe = new CSubscribe();
		$this->obNews = new CObject('subscribe_newsletters');
		$this->obReleases = new CReleases();
		$this->obUsers = new CSubUsers();
		/* Объект для работы с группами пользователей */
		$this->obUserGroups = new CUserGroup();
		/* Объект для работы с правами доступа пользователей к модулям */
		$this->obAccess = new CModulesAccess();
		$this->obSubsUsergroupsLevels= new CObject('subscribe_usergroups_levels');
	}

	function Table()
	{
		/* Поля, по которым можно отсортировать */
		$arSortFields = array("id", "email", "date_add", "date_active","active");
		/* Определяем поле для сортировки */
		list($sOrderField,$sOrderDir)=$this->InitSort($arSortFields,$_REQUEST['order'],$_REQUEST['dir']);
		$sNewDir=($sOrderDir=='desc')?'asc':'desc';
		$arSort=array($sOrderField=>$sOrderDir);
		$iCount = $this->obSubscribe->count();
		$obPages = new CPages();
		/* Для постраничной навигации */
		$this->smarty->assign("pages", $obPages->GetPages($iCount));
		/* Количество отображаемых на странице строк*/
		$this->smarty->assign("num_visible", $obPages->GetVisible());
		/* Параметры сортировки */
		$this->smarty->assign("order", array('newdir' => $sNewDir, 'curdir' => $sOrderDir, 'field' => $sOrderField));
		$listUsers=$this->obSubscribe->GetUsers(array('id'=>'asc'));
		$listNews=$this->obReleases->GetNewslettersList(array('id'=>'asc'));
		$arFilter=false;
		if($list=$this->obSubscribe->GetList($arSort, $arFilter, $obPages->GetLimits($iCount)))
			foreach($list as $key=>$item)
			{
				if($list[$key]['uin']>=0)
				{
					foreach($listUsers as $itemUsers)
						if($list[$key]['uin']==$itemUsers['id'])
							$list[$key]['uin']=$itemUsers['title'];
					if($list[$key]['uin']>0)
						$list[$key]['uin']=false;
				}
				else
					$list[$key]['uin']=false;
			}
		$this->smarty->assign("list", $list);
		return '';
	}

	function EditForm($data=false)
	{
		if(!$data)
		{
			$data=array(
				'id'=>-1,
				'uin'=>-1,
				'newsletters'=>$this->obNews->GetList(array('name'=>'asc')),
				'format'=>$this->obModules->GetConfigVar($this->module, "format"),
			);
		}
		$this->smarty->assign("data", $data);
		return '_edit';
	}

	function Run()
	{
		$page='';
		if($this->obUser->GetLevel($this->module)>1) throw new CAccessError('SUBSCRIBE_NOT_ACCESS_USERS');

		$action='';
		if(isset($_REQUEST['action']))
			$action=$_REQUEST['action'];
		$id=0;
		if(isset($_REQUEST['id']))
			$id=intval($_REQUEST['id']);

		$data=false;

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
							$obSubscribe->Delete($id);
							$obUsers->DeleteItems(array('uin'=>$id));
						}
					}
					elseif (isset($_REQUEST['comact']))
					{
						/* Активация */
						foreach ($request_ids as $id)
							$obSubscribe->Update($id, array('active' => "1"));
					}
					elseif (isset($_REQUEST['comdea']))
					{
						/* Деактивация */
						foreach ($request_ids as $id)
							$obSubscribe->Update($id, array('active' => "0"));
					}
				}

				/* Возвращаемся к списку  */
				CUrlParser::Redirect("admin.php?" . $KS_URL->GetUrl(array()));
			break;
			/* Редактирование */
			case "edit":
				/* Идентификатор */
				/**
				 * @todo Понять зачем выбирать всех подписчиков, это ведь тупо расточительно и бесполезно
				 */
				$data = $this->obSubscribe->GetRecord(array('id' => $id));
				$data['users']=$this->obSubscribe->GetUsers();
				$data['newsletters']=$this->obNews->GetList(array('name'=>'asc'));
				$user_news=$this->obUsers->GetList(array('name'=>'asc'), array('uin'=>$id));
				if($data['newsletters'])
					foreach($data['newsletters'] as $key=>$item)
					{
						$data['newsletters'][$key]['select']=false;
						if($user_news)
							foreach($user_news as $itemUsers)
								if($item['id']==$itemUsers['newsletter'])
									$data['newsletters'][$key]['select']=true;
					}
				$users=$this->obSubscribe->GetList();
				foreach($data['users'] as $key=>$item)
					foreach($users as $itemUsers)
						if($item['id']==$itemUsers['uin'] && $data['uin']!=$item['id'])
							unset($data['users'][$key]);
			case "new":
				$page=$this->EditForm($data);
			break;

			/* Сохранение */
			case "save":
				/* Идентификатор */

				$id = intval($_REQUEST['id']);
				/* Параметры для сохранения */
				$arData = $_POST;

				/* Попытка сохранения данных */
				try
				{
					/* Поле для автозаполнения */
					if (!ereg("^([a-z0-9_.\-]+)(@)([a-z0-9_.\-]+)((\.[a-z0-8_-]+)+)$", $arData['SB_email']))
						throw new CError("SUBSCRIBE_MAIL_ERROR", 0, '"'.$arData['SB_email'].'"');

					$obSubscribe->AddCheckField('email');
					$obSubscribe->AddAutoField('id');
					$arData['SB_date_add']=time();

					if($arData['SB_date_active']!='' && $arData['SB_active']==1)
							$arData['SB_date_active']=strtotime($arData['SB_date_active']);
					elseif($arData['SB_date_active']=='' && $arData['SB_active']==1)
						$arData['SB_date_active']=time();
					else
						unset($arData['SB_date_active']);
					$id = $obSubscribe->Save('SB_', $arData);
					$obUsers->Save($id,$arData['SB_news']);
					/* Осуществляем редирект после успешного сохранения */
					if (array_key_exists('update', $_REQUEST))
						CUrlParser::Redirect("admin.php?".$KS_URL->GetUrl(array('ACTION')).'&ACTION=edit&id='.$id);
					else
						CUrlParser::Redirect("admin.php?".$KS_URL->GetUrl(array('ACTION','p')));

				}
				catch(CError $e)
				{
					if($e->GetCode()==KS_ERROR_MAIN_ALREADY_EXISTS)
					{
						$e=new CError("MAIN_RECORD_ALREADY_EXISTS",0, 'Почта '.$arData['SB_email']);
					}
					$data=$obSubscribe->GetRecordFromPost('SB_',$_POST);

					$smarty->assign('last_error', $e);

					$data['users']=$obSubscribe->GetUsers();
					$data['newsletters']=$obNews->GetList(array('name'=>'asc'), $arFilter);
					$user_news=$obUsers->GetList(array('name'=>'asc'), array('uin'=>$data['id']));
					if($data['newsletters'])
					foreach($data['newsletters'] as $key=>$item)
					{
						$data['newsletters'][$key]['select']=false;
						if($user_news)
						foreach($user_news as $itemUsers)
						{
							if($item['id']==$itemUsers['newsletter'])
							{
								$data['newsletters'][$key]['select']=true;
							}
						}

					}
					/*$users=$obSubscribe->GetList();
					foreach($data['users'] as $key=>$item)
					{
						foreach($users as $itemUsers)
						{

							if($item['id']==$itemUsers['uin'] && $data['uin']!=$item['id'])
							{
								unset($data['users'][$key]);
							}
						}
					}*/



					$smarty->assign('data',$data);
					$page.='_edit';



				}


			break;

			/* Удаление */
			case "delete":

				$id = intval($_REQUEST['id']);


				$obSubscribe->Delete($id);
				$obUsers->DeleteItems(array('uin'=>$id));
				/* В случае успеха (или не успеха - как повезёт) делаем редирект */
				CUrlParser::Redirect("admin.php?" . $KS_URL->GetUrl(array("ACTION", "id")));
			break;

			default:
				$page=$this->Table();
			break;
		}
		return '_subscribe'.$page;
	}
}