<?php
/**
 * @file subscribe/pages/releases.php
 * Файл управления выпусками рассылки
 * Файл проекта kolos-cms.
 *
 * @since 01.02.2010
 *
 * @author fox <fox@kolosstudio.ru>, blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CUserGroup.php';
require_once MODULES_DIR.'/main/libs/class.CModuleAdmin.php';
require_once MODULES_DIR.'/subscribe/libs/class.CEmails.php';

class CsubscribeAIreleases extends CModuleAdmin
{
	private $access_level;
	private $obAPI;
	private $obUserGroups;
	private $obAccess;
	private $obMailSend;

	function __construct($module='subscribe',&$smarty,&$parent)
	{
		parent::__construct($module,$smarty,$parent);
		$this->access_level=$this->obUser->GetLevel($this->module);
		if($this->access_level>5) throw new CAccessError('SUBSCRIBE_NOT_ACCESS_RELEASES');
		$this->obAPI=CSubscribeAPI::get_instance();
		$this->obMailSend=new CEmails();
		/* Объект для работы с группами пользователей */
		$this->obUserGroups = new CUserGroup();
		/* Объект для работы с правами доступа пользователей к модулям */
		$this->obAccess = new CModulesAccess();
	}

	/**
	 * Метод выполняет подготовку данных для формы редактирования выпуска
	 * @param $data - массив с описанием выпуска, можно не передавать
	 * @return string $page - название шаблона, который необходимо отрисовать
	 */
	function EditForm($data=false)
	{
		if(!$data)
		{
			$data=array(
				'id'=>-1,
				'encryption'=>$this->obModules->GetConfigVar('subscribe',"encryption",'UTF-8'),
				'from'=>$this->obModules->GetConfigVar('subscribe',"from",$this->obModules->GetConfigVar('main','emailFrom')),
				'groups'=>array(),
				'newsletter'=>-1,
				'newsletters'=>false
			);
		}
		if(!$this->obAPI->Newsletter()->GetRecord(array('id' => $data['newsletter'])))
			$data['newsletter']=-1;
		$groupslist=$this->obUserGroups->GetList();
		if($groupslist && isset($data['groups']))
			foreach($groupslist as $key=>$elm)
				foreach($data['groups'] as $group)
					if($elm['id']==$group)
						$groupslist[$key]['select']=true;
		$data['groupslist']=$groupslist;
		$newsletters=$this->obAPI->GetNewslettersList();
		if($newsletters && isset($data['newsletters']) && $data['newsletters'])
			foreach($newsletters as $key=>$elm)
				foreach($data['newsletters'] as $newsletter)
					if($elm['id']==$newsletter)
						$newsletters[$key]['select']=true;
		$data['newsletters']=$newsletters;
		$this->smarty->assign("data", $data);
		return '_edit';
	}

	/**
	 * Метод обеспечивает вывод списка выпусков рассылки
	 */
	function Table()
	{
		/* Поля, по которым можно отсортировать */
		$arSortFields = array("id", "theme", "date_add","send", "from");
		//Определяем порядок сортировки записей
		list($sOrderField,$sOrderDir)=$this->InitSort($arSortFields);
		$sNewDir=($sOrderDir=='desc')?'asc':'desc';
		/* Параметры сортировки для выборки списка */
		$arOrder = array($sOrderField => $sOrderDir);
		$arFilter=false;
		$obPages=$this->InitPages();
		$totalNews = $this->obAPI->Release()->count();
		$listNews=$this->obAPI->GetNewslettersList(array('id'=>'asc'));
		if($list=$this->obAPI->Release()->GetList($arOrder, $arFilter, $obPages->GetLimits($totalNews)))
			foreach($list as $key=>$item)
				if($list[$key]['newsletter']>=0)
				{
					foreach($listNews as $itemNews)
						if($list[$key]['newsletter']==$itemNews['id'])
							$list[$key]['newsletter']=$itemNews['name'];
					if((int)($list[$key]['newsletter']))
						$list[$key]['newsletter']=false;
				}
				else
					$list[$key]['newsletter']=false;
		$this->smarty->assign("list", $list);
		/* Для постраничной навигации */
		$this->smarty->assign("pages", $obPages->GetPages($totalNews));
		/* Количество отображаемых на странице строк */
		$this->smarty->assign("num_visible", $obPages->GetVisible());
		/* Параметры сортировки */
		$this->smarty->assign("order", array('newdir' => $sNewDir, 'curdir' => $sOrderDir, 'field' => $sOrderField));
	}

	/**
	 * Метод обеспечивает выполнение общих действий над выпусками рассылок
	 */
	function CommonActions()
	{
		$request_ids = array();
		$input_array = $_POST;
		if (count($input_array))
			foreach ($input_array as $variable => $value)
				if (preg_match("#^common_([0-9]+)$#", $variable, $subpatterns))
					$request_ids[] = intval($subpatterns[1]);

		if (count($request_ids) > 0)
		{
			if (isset($_REQUEST['comdel']))/* Удаление*/
				foreach ($request_ids as $id)
					$this->obAPI->Release()->Delete($id);
			elseif (isset($_REQUEST['comact']))/* Активация */
				foreach ($request_ids as $id)
					$this->obAPI->Release()->Update($id, array('active' => "1"));
			elseif (isset($_REQUEST['comdea']))/* Деактивация */
				foreach ($request_ids as $id)
					$this->obAPI->Release()->Update($id, array('active' => "0"));
			$this->obModules->AddNotify('SUBSCRIBE_COMMON_ACTION_OK','',NOTIFY_MESSAGE);
		}
		else
			$this->obModules->AddNotify('SUBSCRIBE_COMMON_ACTION_NO_RECORDS');
		$this->obUrl->Redirect("admin.php?" . $this->obUrl->GetUrl(array()));
	}

	/**
	 * Метод выполняет сохранение/обновление выпуска рассылки
	 */
	function Save()
	{
		/* Параметры для сохранения */
		$arData = $_POST;
		/* Попытка сохранения данных */
		try
		{
			/* Поле для автозаполнения */
			$this->obAPI->Release()->AddAutoField('id');

			if (strlen(trim($arData['SB_theme'])) == 0 || strlen(trim($arData['SB_theme']))>250)
				throw new CError("SUBSCRIBE_THEME_ERROR",0);
			if (strlen(trim($arData['SB_content'])) == 0)
				throw new CError("SUBSCRIBE_CONTENT_ERROR",0);
			if (strlen(trim($arData['SB_from'])) == 0)
				throw new CError("SUBSCRIBE_FROM_FIELD_ERROR",0);
			if (!IsEmail($arData['SB_from']))
				throw new CError("SUBSCRIBE_MAIL_ERROR", 0, '"'.$arData['SB_from'].'"');
			if (isset($arData['SB_to']) && $arData['SB_to']!='' && !IsEmail($arData['SB_to']) && $arData['SB_to'])
				throw new CError("SUBSCRIBE_MAIL_ERROR", 0, '"'.$arData['SB_to'].'"');
			/* Сохранение записи */
			if($id = $this->obAPI->Release()->Save('SB_', $arData))
			{
				/* Отправка */
				if (array_key_exists('send', $_REQUEST))
					$this->obAPI->PrepareAndSend($id);
				/* Осуществляем редирект после успешного сохранения */
				if (array_key_exists('update', $_REQUEST))
					$this->obUrl->Redirect("admin.php?".$this->obUrl->GetUrl(array('ACTION')).'&action=edit&id='.$id);
				else
					$this->obUrl->Redirect("admin.php?".$this->obUrl->GetUrl(array('ACTION','p')));
			}
			throw new CError('SUBSRIBE_RELEASE_SAVE_ERROR');
		}
		catch(CError $e)
		{
			$this->obModules->AddNotify($e->getMessage());
			$data=$this->obAPI->Release()->GetRecordFromPost('SB_',$_POST);
			$data['newsletters']=$this->obAPI->GetNewslettersList();
			$data['groupslist']=$this->obUserGroups->GetList();
			return $this->EditForm($data);
		}
	}

	function Run()
	{
		/* Чтение списка допустимых действий */
		$this->ParseAction();
		$page='';
		$arData=false;
		$id=0;
		if(isset($_REQUEST['id']))
			$id=intval($_REQUEST['id']);
		switch($this->sAction)
		{
			case "common":
				$this->CommonActions();
			break;
			case "edit":
				$arData = $this->obAPI->Release()->GetRecord(array('id' => $id));
			case "new":
				$page=$this->EditForm($arData);
			break;
			case "save":
				$page=$this->Save();
			break;
			case "delete":
				$this->obAPI->Release()->Delete($id);
				$this->obUrl->Redirect("admin.php?" . $this->obUrl->GetUrl(array("action", "id")));
			break;
			default:
				$page=$this->Table();
			break;
		}
		return '_releases'.$page;
	}
}