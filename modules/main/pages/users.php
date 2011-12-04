<?php
/**
 * Страница управления профилями пользователей ЦМС.
 *
 * 10.09.09 Добавлена поддержка ответа в формате json. Добавлена поддержка уменьшенной формы.
 *
 * @filesource users.php
 * @author BlaDe39 <blade39@kolosstudio.ru>, north-e <pushkov@kolosstudio.ru>
 * @version 1.1
 * @since 13.04.2009
 */

/* Защита от взлома */
if (!defined("KS_ENGINE")) die("Hacking attempt!");

class CmainAIusers extends CModuleAdmin
{
	private $obGroups;

	function __construct($module='main',&$smarty,&$parent)
	{
		parent::__construct($module,$smarty,$parent);
		$this->obGroups=new CUserGroup();
		//Устанавливаем значение для правильного вывода меню в верхней части шаблона
		$this->smarty->assign('modpage','users');
	}

	function Table()
	{
		$arSortFields=$this->obUser->GetFields();
		// Обработка порядка вывода элементов
		list($sOrderField,$sOrderDir)=$this->InitSort($arSortFields,$_REQUEST['order'],$_REQUEST['dir']);
		$sNewDir=($sOrderDir=='desc')?'asc':'desc';

		//Получаем список групп пользователей
		$arGroups=$this->obGroups->GetList();
		foreach($arGroups as $arGroup)
		{
			$arRes[$arGroup['id']]=$arGroup['title'];
		}

		if(class_exists('CFilterFrame'))
		{
			$obFilter=new CFilterFrame();
			$obFilter->AddField(array('FIELD'=>'title','METHOD'=>'~'));
			$obFilter->AddField(array('FIELD'=>'email','METHOD'=>'~'));
			$obFilter->AddField(array(
				'FIELD'=>'active',
				'TYPE'=>'SELECT',
				'VALUES'=>array(
					''=>$this->obModules->GetText('any'),
					'1'=>$this->obModules->GetText('active'),
					'0'=>$this->obModules->GetText('inactive')),
			));
			$obFilter->AddField(array('FIELD'=>'group_id','METHOD'=>'->','TYPE'=>'SELECT','VALUES'=>$arRes));
			$obFilter->AddField(array('FIELD'=>'last_visit','TYPE'=>'DATE','METHOD'=>'<>'));
			$arFilter=$obFilter->GetFilter();
			if(array_key_exists('->group_id',$arFilter) && strlen($arFilter['->group_id'])>0)
			{
				$arFilter['->users_grouplinks.group_id']=$arFilter['->group_id'];
				$arFilter['?users_grouplinks.user_id']='users.id';
				unset($arFilter['->group_id']);
			}
			$obFilter->SetSmartyFilter('filter');
			$arTitles=array(
				'title'=>$this->obModules->GetText('filter_login'),
				'email'=>$this->obModules->GetText('filter_email'),
				'active'=>$this->obModules->GetText('filter_active'),
				'last_visit'=>$this->obModules->GetText('filter_last_visit'),
				'date_add'=>$this->obModules->GetText('filter_date_add'),
				'group_id'=>$this->obModules->GetText('filter_group_id'),
			);
			$this->smarty->assign('ftitles',$arTitles);
		}
		else $arFilter=false;

		$obPages = new CPageNavigation($this->obUser);
		$iCount=$this->obUser->count($arFilter);
		$arOrder=array($sOrderField=>$sOrderDir);
		$arSelect=array(
			'id','title','email','last_visit','date_register','active'
		);
		if($arList=$this->obUser->GetList($arOrder,$arFilter,$obPages->GetLimits($iCount),$arSelect))
		{
			/**
			 * @todo Узнать зачем нужен именно такой формат ответа
			 * if($_GET['mode']=='ajax')
			{
				$data=array(
					'list'=>$list,
					'level'=>$USER->GetLevel('main'),
					'pages'=>$obPages->GetPages($totalUsers),
					'num_visible'=>$obPages->GetVisible(),
					'groups_num'=>$USER->GetNum(),
					'order'=>Array("newdir"=>$sNewDir,"curdir"=>$sOrderDir,"field"=>$sOrderField)
				);
				echo json_encode($data);
				die();
			}
			else
			{*/
			$this->smarty->assign("list",$arList);
			$this->smarty->assign("pages",$obPages->GetPages($iCount));
			$this->smarty->assign('level',$this->obUser->GetLevel('main'));
			$this->smarty->assign("order",Array("newdir"=>$sNewDir,"curdir"=>$sOrderDir,"field"=>$sOrderField));
		}
		return '';
	}

	/**
	 * Форма добавления/редактирования пользователя
	 */
	function EditForm($data=false)
	{
		if($data==false)
		{
			$data=array('id'=>-1);
			if (class_exists("CFields"))
			{
				/* Чтение пользовательских полей модуля users */
				$obFields = new CFields();
				$arFields = $obFields->GetList(array("id" => "asc"), array("module" => "users", "type" => $this->obUser->GetTable()));
				if (is_array($arFields))
					foreach($arFields as $item)
						$data['ext_'.$item["title"]] = $item["default"];
			}
		}
		else
		{
			if(array_key_exists('id',$data) && $data['id']>0)
				$data["GROUPS"] = $this->obUser->GetAllGroups($data['id']);
		}
		$this->smarty->assign("groupslist",$this->obGroups->GetList());
		$this->smarty->assign("userdata",$data);
		if (class_exists("CFields"))
		{
			$obFields=new CFields();
			$arFields=$obFields->GetList(Array("id"=>"asc"),Array("module"=>"users","type"=>$this->obUser->GetTable()));
			$this->smarty->assign("addFields",$arFields);
		}
		return '_edit';
	}

	/**
	 * Метод обеспечивает сохранение учётной записи пользователя
	 */
	function Save()
	{
		global $KS_URL;
		try
		{
			if($this->obUser->GetLevel('main')>8)
			{
				$this->obModules->AddNotify('MAIN_NO_RIGHT_TO_MANAGE_USERS');
				return $this->Table();
			}
			/**
			 * @todo Разобраться с этим кодом, возможно здесь он не нужен
			 */
			$this->obUser->AddAutoField("id");
			$this->obUser->sWidth=$this->obModules->GetConfigVar('main','avasizex',500);
			$this->obUser->sHeight=$this->obModules->GetConfigVar('main','avasizey',500);
			$this->obUser->sSize=$this->obModules->GetConfigVar('main','avasize',100);
			$this->obUser->sRatio=true;
			$this->obUser->sRatio_wb=false;

			$bError=0;
			//Проверка логина
			if(strlen($_POST['CU_title'])==0)
			{
				$bError+=$this->obModules->AddNotify('MAIN_USERS_TITLE_REQUIRED');
			}
			elseif(!IsTextIdent($_POST['CU_title']))
			{
				$bError+=$this->obModules->AddNotify('MAIN_USERS_TITLE_WRONG');
			}
			elseif($_POST['CU_id']>0)
			{
				if($arUser=$this->obUser->GetRecord(array('title'=>$_POST['CU_title'],'!id'=>$_POST['CU_id'])))
				{
					$bError+=$this->obModules->AddNotify('MAIN_USERS_TITLE_ALREADY');
				}
			}
			elseif($_POST['CU_id']<1)
			{
				if($arUser=$this->obUser->GetRecord(array('title'=>$_POST['CU_title'])))
				{
					$bError+=$this->obModules->AddNotify('MAIN_USERS_TITLE_ALREADY');
				}
			}
			//Проверка адреса электронной почты
			if(strlen($_POST['CU_email'])>0)
			{
				if(!IsEmail($_POST['CU_email']))
				{
					$bError+=$this->obModules->AddNotify('MAIN_USERS_EMAIL_WRONG');
				}
				elseif($_POST['CU_id']>0)
				{
					if($arUser=$this->obUser->GetRecord(array('email'=>$_POST['CU_email'],'!id'=>$_POST['CU_id'])))
					{
						$bError+=$this->obModules->AddNotify('MAIN_USERS_EMAIL_ALREADY');
					}
				}
				elseif($_POST['CU_id']<1)
				{
					if($arUser=$this->obUser->GetRecord(array('email'=>$_POST['CU_email'])))
					{
						$bError+=$this->obModules->AddNotify('MAIN_USERS_EMAIL_ALREADY');
					}
				}
			}
			//Проверка пароля
			if(strlen($_POST['CU_password'])>0)
			{
				if(strlen($_POST['CU_password'])<6)
				{
					$bError+=$this->obModules->AddNotify('MAIN_USERS_PASSWORD_SHORT');
				}
				elseif($_POST['CU_password']!=$_POST['CU_password_c'])
				{
					$bError+=$this->obModules->AddNotify('MAIN_USERS_PASSWORD_MISTAPE');
				}
				else
				{
					$_POST['CU_password']=md5($_POST['CU_password']);
					$_POST['CU_password_c']=$_POST['CU_password'];
				}
			}
			//Блокировка пользователя по времени
			if(strlen($_POST['CU_blocked_from'])>0)
			{
				$_POST['CU_blocked_from']=String2Time($_POST["CU_blocked_from"]);
			}
			else
			{
				$_POST['CU_blocked_from']=0;
			}
			if(strlen($_POST["CU_blocked_till"])>0)
			{
				$_POST["CU_blocked_till"]=String2Time($_POST["CU_blocked_till"]);
			}
			else
			{
				$_POST["CU_blocked_till"]=0;
			}
			if($_POST["CU_blocked_till"]<$_POST['CU_blocked_from']) $_POST["CU_blocked_till"]=$_POST['CU_blocked_from']+1;
			if($_POST['CU_id']>0 && $_POST['CU_id']==$this->obUser->ID())
			{
				if($_POST['CU_blocked_from']>0 || $_POST['CU_blocked_till']>0)
				{
					$this->obModules->AddNotify('MAIN_USERS_CANT_BLOCK_SELF');
					$_POST['CU_blocked_from']=0;
					$_POST['CU_blocked_till']=0;
				}
			}
			$_POST['CU_active']=intval($_POST['CU_active']);
			if($bError==0)
			{
				if($id = $this->obUser->Save("CU_",$_POST))
				{
					$usergroups=$this->obUser->GetAllGroups($id);
					if(isset($_POST['CU_groups']) && is_array($_POST["CU_groups"]))
					{
						foreach($_POST["CU_groups"] as $group_id)
						{
							if(intval($_POST["CU_groups_from".$group_id])>0)
							{
								$datefrom=String2Time($_POST["CU_groups_from".$group_id]);
							}
							else
							{
								$datefrom=0;
							}
							if(intval($_POST["CU_groups_to".$group_id])>0)
							{
								$dateto=String2Time($_POST["CU_groups_to".$group_id]);
							}
							else
							{
								$dateto=0;
							}
							$this->obUser->SetUserGroup($id,$group_id,$datefrom,$dateto);
							unset($usergroups[$group_id]);
						}
					}
					if(is_array($usergroups) && count($usergroups)>0)
					{
						foreach($usergroups as $group_id=>$something)
						{
							$this->obUser->UnsetUserGroup($id,$group_id);
						}
					}
					if(!array_key_exists('update',$_REQUEST))
					{
						$this->obModules->AddNotify('MAIN_USERS_SAVE_OK','',NOTIFY_MESSAGE);
						CUrlParser::get_instance()->Redirect("/admin.php?".$KS_URL->GetUrl(Array('ACTION','id')));
					}
					else
					{
						$this->obModules->AddNotify('MAIN_USERS_SAVE_OK','',NOTIFY_MESSAGE);
						CUrlParser::get_instance()->Redirect("/admin.php?".$KS_URL->GetUrl(array('ACTION','id')).'&ACTION=edit&id='.$id);
					}
				}
				else
				{
					throw new CError('MAIN_USERS_SAVE_ERROR');
				}
			}
			else
			{
				throw new CDataError('MAIN_USERS_FIELDS_ERROR');
			}
		}
		catch(CError $e)
		{
			$userdata=$this->obUser->GetFromPost('CU_',$_POST);
			$this->smarty->assign("last_error",$e->__toString());
			return $this->EditForm($userdata);
		}
	}

	function Run()
	{
		global $KS_URL;
		/* Проверка прав доступа к редактированию пользователей */
		if ($this->obUser->GetLevel("main") > 9) throw new CAccessError("MAIN_NO_RIGHT_TO_VIEW_USERS");
		if($this->obUser->GetLevel('main')>8)
			$this->smarty->assign('shortMode','Y');
		$userdata=false;
		if(array_key_exists('action',$_REQUEST))
			$sAction=$_REQUEST['action'];
		else
			$sAction='';
		if(array_key_exists('id',$_REQUEST))
			$iId=intval($_REQUEST['id']);
		else
			$iId=0;
		try
		{
			switch($sAction)
			{
				case 'common':
					if ($this->obUser->GetLevel("main") > 3)
						throw new CAccessError("MAIN_NO_RIGHT_TO_MANAGE_USERS");
					$arElements=$_POST['sel']['elm'];
					if(in_array($this->obUser->ID(),$arElements))
					{
						$pos=array_search($this->obUsers->ID(),$arElements);
						unset($arElements[$pos]);
						$this->obModules->AddNotify("MAIN_OPERATIONS_NOT_APPLIED_YOUR_ACCOUNT",'');
					}
					if (array_key_exists('comact',$_POST))
					{
						// Установка общей активности для выделенных элементов
						$this->obUser->Update($arElements,Array('active'=>'1'));
						$this->obModules->AddNotify('MAIN_USERS_ACTIVE_OK','',NOTIFY_MESSAGE);
						CUrlParser::get_instance()->Redirect("/admin.php?".$KS_URL->GetUrl(Array('ACTION','id')));
					}
					elseif (array_key_exists('comdea',$_POST))
					{
						//Снятие общей активности для элементов
						$this->obUser->Update($arElements,Array('active'=>'0'));
						$this->obModules->AddNotify('MAIN_USERS_DEACTIVE_OK','',NOTIFY_MESSAGE);
						CUrlParser::get_instance()->Redirect("/admin.php?".$KS_URL->GetUrl(Array('ACTION','id')));
					}
					elseif (array_key_exists('comdel',$_POST))
					{
						// Удаление выделенных элементов
						$this->obUser->DeleteByIds($arElements);
						$this->obModules->AddNotify('MAIN_USERS_DELETE_OK','',NOTIFY_MESSAGE);
						CUrlParser::get_instance()->Redirect("/admin.php?".$KS_URL->GetUrl(Array('ACTION','id')));
					}
					$page=$this->Table();
				break;
				case 'edit':
					$userdata = $this->obUser->GetRecord(array("id" => $iId));
				case 'new':
					$page=$this->EditForm($userdata);
				break;
				case 'save':
					$page=$this->Save();
				break;
				case 'delete':
					if ($this->obUser->GetLevel("main") > 3)
						throw new CAccessError("MAIN_NO_RIGHT_TO_MANAGE_USERS");
					if($iId!=$this->obUser->ID())
					{
						if($arUser=$this->obUser->GetById($iId))
						{
							$this->obUser->Delete($iId);
							$this->obModules->AddNotify('MAIN_USERS_DELETE_OK','',NOTIFY_MESSAGE);
							CUrlParser::get_instance()->Redirect("/admin.php?".$KS_URL->GetUrl(Array('ACTION','id')));
						}
						else
						{
							throw new CError('MAIN_USER_NOT_FOUND');
						}
					}
					else
					{
						$this->obModules->AddNotify("MAIN_NOT_DELETE_YOURSELF");
					}
				break;
				default:
					$page=$this->Table();
			}
		}
		catch(CError $e)
		{
			$this->obModules->AddNotify($e->getMessage());
			$page=$this->Table();
		}
		return "_users".$page;
	}
}

