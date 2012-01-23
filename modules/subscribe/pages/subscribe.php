<?php
/**
 * @file subscribe/pages/subscribe.php
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
require_once MODULES_DIR.'/subscribe/libs/class.CSubscribeAPI.php';

class CsubscribeAIsubscribe extends CModuleAdmin
{
	protected $iAccessLevel;
	private $obReleases;
	protected $obAPI;

	function __construct($module='subscribe',&$smarty,&$parent)
	{
		parent::__construct($module,$smarty,$parent);
		$this->iAccessLevel=$this->obUser->GetLevel($this->module);
		$this->obAPI=CSubscribeAPI::get_instance();
	}

	/**
	 * Метод подготавливает вывод таблицы записей о подписчиках
	 */
	protected function Table()
	{
		/* Поля, по которым можно отсортировать */
		$arSortFields = array("id", "email", "date_add", "date_active","active");
		/* Определяем поле для сортировки */
		list($sOrderField,$sOrderDir)=$this->InitSort($arSortFields);
		$sNewDir=($sOrderDir=='desc')?'asc':'desc';
		$arSort=array($sOrderField=>$sOrderDir);
		$iCount = $this->obAPI->SubscribeUsers()->count();
		$obPages = $this->InitPages();
		$arFilter=array(
			'<?subscribe_users.uin'=>'users.id',
		);
		$arSelect=array("id", 'uin', "email", "date_add", "date_active","active",'users.title'=>'title');
		$list=$this->obAPI->SubscribeUsers()->GetList($arSort, $arFilter, $obPages->GetLimits($iCount),$arSelect);
		$this->smarty->assign("list", $list);
		/* Для постраничной навигации */
		$this->smarty->assign("pages", $obPages->GetPages($iCount));
		/* Количество отображаемых на странице строк*/
		$this->smarty->assign("num_visible", $obPages->GetVisible());
		/* Параметры сортировки */
		$this->smarty->assign("order", array('newdir' => $sNewDir, 'curdir' => $sOrderDir, 'field' => $sOrderField));
		return '';
	}

	/**
	 * Метод выполняет общие операции для групп подписчиков
	 */
	protected function CommonAction()
	{
		$request_ids = array();
		$input_array = $_POST;
		if (count($input_array))
			foreach ($input_array as $variable => $value)
				if (preg_match("#^common_([0-9]+)$#", $variable, $subpatterns))
					$request_ids[] = intval($subpatterns[1]);
		if (count($request_ids) > 0)
			if (isset($_REQUEST['comdel']))/* Удаление*/
				foreach ($request_ids as $id)
				{
					$this->obAPI->SubscribeUsers()->Delete($id);
					$this->obAPI->Subscribers()->DeleteItems(array('uin'=>$id));
				}
			elseif (isset($_REQUEST['comact']))/* Активация */
				foreach ($request_ids as $id)
					$this->obAPI->SubscribeUsers()->Update($id, array('active' => "1"));
			elseif (isset($_REQUEST['comdea']))/* Деактивация */
				foreach ($request_ids as $id)
					$this->obAPI->SubscribeUsers()->Update($id, array('active' => "0"));
		/* Возвращаемся к списку  */
		$this->obUrl->Redirect("admin.php?".$this->obUrl->GetUrl(array()));
	}

	/**
	 * Метод выполняет сохранение записи о подписчике в базе данных
	 */
	protected function Save()
	{
		/* Параметры для сохранения */
				$arData = $_POST;
				/* Попытка сохранения данных */
				try
				{
					/* Поле для автозаполнения */
					if(!IsEmail($arData['SB_email']))
						throw new CError("SUBSCRIBE_MAIL_ERROR", 0, '"'.$arData['SB_email'].'"');

					if($arData['SB_id']<1)
						$arData['SB_date_add']=time();
					if(!isset($arData['SB_active']))
						$arData['SB_active']=0;
					else
						$arData['SB_active']=intval($arData['SB_active']);
					if(isset($arData['SB_date_active']) && $arData['SB_date_active']!='' && $arData['SB_active']==1)
						$arData['SB_date_active']=strtotime($arData['SB_date_active']);
					elseif($arData['SB_active']==1)
						$arData['SB_date_active']=time();
					else
						$arData['SB_date_active']='';
					if($id = $this->obAPI->SaveSubscribe('SB_', $arData))
					{
						$this->obAPI->Subscribers()->Save($id,$arData['SB_news']);
						/* Осуществляем редирект после успешного сохранения */
						if (array_key_exists('update', $_REQUEST))
							$this->obUrl->Redirect("admin.php?".$this->obUrl->GetUrl(array('action')).'&action=edit&id='.$id);
						else
							$this->obUrl->Redirect("admin.php?".$this->obUrl->GetUrl(array('action','p')));
					}
					else
						throw new CError('SUBSCRIBE_SUBSCRIBER_SAVE_ERROR');
				}
				catch(CError $e)
				{
					if($e->GetCode()==KS_ERROR_MAIN_ALREADY_EXISTS)
						$e=new CError("MAIN_RECORD_ALREADY_EXISTS",0,$arData['SB_email']);
					$data=$this->obAPI->SubscribeUsers()->GetRecordFromPost('SB_',$_POST);
					$this->smarty->assign('last_error', $e);
					return $this->EditForm($data);
				}
	}

	/**
	 * Метод выполняет подготовку данных для вывода формы редактирования подписчика
	 */
	protected function EditForm($data=false)
	{
		if(!$data)
		{
			$data=array(
				'id'=>-1,
				'uin'=>-1,
				'format'=>$this->obModules->GetConfigVar($this->module, "format"),
				'newsletters'=>$this->obAPI->Newsletter()->GetList(array('name'=>'asc')),
			);
		}
		else
		{
			$data['newsletters']=$this->obAPI->Newsletter()->GetList(array('name'=>'asc'));
			$user_news=$this->obAPI->Subscribers()->GetList(array('name'=>'asc'), array('uin'=>$data['id']));
			if($data['newsletters'])
				foreach($data['newsletters'] as $key=>$item)
				{
					$data['newsletters'][$key]['select']=false;
					if($user_news)
						foreach($user_news as $itemUsers)
							if($item['id']==$itemUsers['newsletter'])
								$data['newsletters'][$key]['select']=true;
				}
		}
		$this->obModules->UseJavaScript('/subscribe/adminSubscribeEdit.js');
		$this->smarty->assign("data", $data);
		return '_edit';
	}

	/**
	 * Основной метод, выполняет определение операции и вызывает соответствующие методы
	 */
	function Run()
	{
		if($this->iAccessLevel>1)
			throw new CAccessError('SUBSCRIBE_NOT_ACCESS_USERS');
		$page='';
		$this->ParseAction();
		$id=0;
		if(isset($_REQUEST['id']))
			$id=intval($_REQUEST['id']);

		$data=false;
		switch($this->sAction)
		{
			case "common":
				$page=$this->CommonAction();
			break;
			case "edit":
				$data=$this->obAPI->SubscribeUsers()->GetById($id);
			case "new":
				$page=$this->EditForm($data);
			break;
			case "save":
				$page=$this->Save();
			break;
			case "delete":
				if($arUser=$this->obAPI->SubscribeUsers()->GetById($id))
				{
					$this->obAPI->SubscribeUsers()->Delete($id);
					$this->obAPI->Subscribers()->DeleteItems(array('uin'=>$arUser['id']));
					$this->obModules->AddNotify('SUBSCRIBE_SUBSCRIBER_DELETE_OK',0,NOTIFY_MESSAGE);
				}
				else
					$this->obModules->AddNotify('SUBSCRIBE_SUBSCRIBER_NOT_FOUND');
				$this->obUrl->Redirect("admin.php?".$this->obUrl->GetUrl(array("action", "id")));
			break;
			default:
				$page=$this->Table();
			break;
		}
		return '_subscribe'.$page;
	}
}