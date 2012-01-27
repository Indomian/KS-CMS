<?php
/**
 * Файл обеспечивает работу с пользователями сайта, их авторизацию, хранение полей и многое другое.
 *
 * @since 05.11.2008
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
if (!defined('KS_ENGINE')) die("Hacking attempt!");
include_once MODULES_DIR.'/main/libs/class.CUsersCommon.php';
include_once MODULES_DIR.'/main/libs/interface.User.php';
include_once MODULES_DIR.'/main/libs/class.CModulesAccess.php';
include_once MODULES_DIR.'/main/libs/class.CImageUploader.php';

define('PASSWORD_CHARS','abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');

/**
 * Класс CUser - управление настройками пользователей
 *
 * @filesource class.CUser.php
 * @author BlaDe39 <blade39@kolosstudio.ru>, north-e <pushkov@kolosstudio.ru>
 * @version 2.5.4-14
 * @since 24.03.2009
 * Добавлены обработчики событий на действия с профилем пользователя
 * Устранено затирание сессии незалогиненного пользователя
 * Убрана автоматическая авторизация по параметру в URL
 */

class CConsoleUser extends CUsersCommon implements User
{
	protected $is_login;
	private $arAllowExt=array('jpg','jpeg','png');
	private $arUserVars;
	private $bUpdateVars;
	private $obModules;
	private $obSession;
	private $obLog;

	/**
	 * Конструктор объекта пользовательского класса
	 *
	 * Выполняет инициализацию пользовательской сессии
	 *
	 * @param string $sTable Таблица пользователей в базе данных
	 */
	function __construct($sTable = "users",$sUploadPath='/users',$sModule='users')
	{
		global $smarty, $KS_EVENTS_HANDLER;

		/*\todo Свести работу к одному классу */
		if(IS_ADMIN)
		{
			$this->obModules=CAdminModuleManagment::get_instance();
		}
		else
		{
			global $KS_MODULES;
			$this->obModules=$KS_MODULES;
		}
		$this->obSession=CSessionManager::get_instance();
		$this->obLog=new CObject('users_log');

		/* Вызов обработчика перед инициализацией объекта класса */
		$onBeforeInitParams = array();
		if (!$KS_EVENTS_HANDLER->Execute('main', 'onBeforeUserObjectInit', $onBeforeInitParams))
			throw new CError('MAIN_HANDLER_ERROR',0,$KS_EVENTS_HANDLER->GetLastEvent());

		/* Устанавливаем IP пользователя, если он еще не входил */
		if(!array_key_exists('USER_IP',$_SESSION) || $_SESSION['USER_IP'] == '')
			$_SESSION['USER_IP'] = $_SERVER['REMOTE_ADDR'];

		/* Инициализация полей класса CObject */
		parent::__construct($sTable,$sUploadPath,$sModule);

		$this->userdata = array();
		$this->arAccessLevels = array();
		$this->sUploadPath = '/users';
		$this->arInGroups = false;
		$this->is_login = false;

		$this->IsValid();
		$this->AddFileField('img');
		$smarty->assign('USER', $this->userdata);
		/* Вызов обработчика при инициализации объекта класса */
		$onInitParams = $this->userdata;
		$onUserObjectInitSuccess = $KS_EVENTS_HANDLER->Execute('main', 'onUserObjectInit', $onInitParams);
	}

	function SetGroupList($arGroupList)
	{
		$this->groups_list = $arGroupList;
	}

	function IsValid()
	{
		try
		{
			/* Проверка адреса пользователя и имени его сессии */
			if($_SESSION['USER_IP'] != $_SERVER['REMOTE_ADDR'])
			{
				$this->logout();
			}
			elseif(isset($_SESSION['cu_user']))
			{
				if (($_SESSION['cu_user'] - time()) > 0)
				{
					/* Обновление времени жизни сессии пользователя */
					$iUserID = $_SESSION['cu_user_id'];
					if (!is_numeric($iUserID))
					{
						$this->logout();
					}
					else
					{
						/* Проверка залогиненного пользователя на активность */
						$query="SELECT * " .
								"FROM ks_users " .
								"WHERE id = " . intval($iUserID).
								" AND NOT(" .
									" ((blocked_from<='".time()."'))" .
									" AND ((blocked_till>='".time()."'))) LIMIT 1";
						$resUser=$this->obDB->query($query);
						if ($this->obDB->num_rows($resUser) > 0)
						{
							$arRow = $this->obDB->get_row($resUser);
							if ($arRow['active'] == 1)
							{
								$this->GetGroups();
								$this->OnSessionUpdate($arRow);
							}
							else
							{
								throw new CUserError('MAIN_USER_INACTIVE');
							}
						}
						else
						{
							throw new CUserError('MAIN_USER_BLOCKED');
						}
					}
				}
				else
				{
					$this->TryLogout();
				}
			}
			else
			{
				$this->TryCookieLogin();
			}
		}
		catch (CUserError $e)
		{
			$this->logout();
			$this->userdata['LAST_ERROR']=$e->__toString();
			throw $e;
		}
	}

	function TryCookieLogin()
	{
		if(isset($_COOKIE['ks_token']) && IsHash($_COOKIE['ks_token']))
		{
			if($arUserRecord=$this->GetRecord(array('token'=>$_COOKIE['ks_token'])))
			{
				if($arUserRecord['last_ip']==$_SESSION['USER_IP'])
				{
					return $this->LoginByTitle($arUserRecord);
				}
			}
		}
	}

	/**
	 * Метод проверяет наличие куки и если её нет выполняет логаут, иначе обновляет данные пользователя
	 */
	function TryLogout()
	{
		$this->TryCookieLogin();
		$this->logout();
	}

	function SaveLogin()
	{
		$hash=md5(time().rand(0,9999).$this->userdata['id']);
		$arFields=array('token'=>$hash,'last_ip'=>$_SESSION['USER_IP']);
		$this->Update($this->ID(),$arFields);
		setcookie('ks_token',$hash,time()+2678400,'/');
	}

	/**
	 * Метод вызывается после выполнения базовых операций по авторизации
	 */
	private function OnSessionUpdate($arUser)
	{
		global $KS_EVENTS_HANDLER;
		$this->userdata=$arUser;
		$this->arUserVars=json_decode($arUser['user_vars'],true);
		$this->userdata['vars']=$this->arUserVars;
		//Проверка является ли данный запрос хитом, не лучший вариант, но позволяет блокировать не нужные хиты
		//на продление авторизации
		if(!(isset($_REQUEST['ishit']) && $_REQUEST['ishit']=='no'))
			$_SESSION['cu_user'] = time() + $this->obModules->GetConfigVar('main','user_inactive_time',3600);
		$this->Update($this->userdata['id'],array('last_visit'=>time()));
		$this->is_login = true;
		/* Вызов обработчика при обновлении времени жизни сессии */
		$onSessionUpdateParams = $this->userdata;
		$KS_EVENTS_HANDLER->Execute('main', 'onUserSessionUpdate', $onSessionUpdateParams);
	}

	/**
	 * Метод возвращает кэшированные данные пользователя
	 */
	function GetUserData()
	{
		return $this->userdata;
	}

	/**
	 * Метод логгирует действия над базой данных пользователей в отдельной таблице
	 */
	function LogAction($user_id,$action,$arData)
	{
		$arFields=array(
			'date'=>time(),
			'content'=>$action,
			'ip'=>$_SERVER['REMOTE_ADDR'],
			'user_id'=>$user_id,
			'request'=>json_encode($arData)
		);
		$this->obLog->Save('',$arFields);
	}

	/**
	 * Выполняет активацию пользователя по переданному хэш-коду.
	 * @param $code - хэш код пользователя.
	 */
	function Activate($code)
	{
		global $KS_EVENTS_HANDLER;
		$onBeforeActivateParams = array('code' => $code);
		// вызов обработчика перед активацией пользователя
		if (!$KS_EVENTS_HANDLER->Execute('main', 'onBeforeActivate', $onBeforeActivateParams))
			throw new CError('MAIN_HANDLER_ERROR');
		$query="SELECT * FROM ".PREFIX.$this->sTable." WHERE MD5(concat(`id`,`email`))='".$this->obDB->safesql($code)."'";
		$res=$this->obDB->query($query);
		if($this->obDB->num_rows($res)>0)
		{
			$arData=$this->obDB->get_row($res);
			$arData['active']=1;
			unset($arData['password']);

			$this->LogAction($arData['id'],'Активация пользователя',$arData);

			$SaveResult = $this->Save('', $arData);
			$onActivateParams = array($arData);
			// вызов обработчика при активации пользователя
			if (!$KS_EVENTS_HANDLER->Execute('main', 'onActivate', $onActivateParams))
				throw new CError('MAIN_HANDLER_ERROR');
			return $SaveResult;
		}
	}

	/**
	 * Возвращает пользователя по переданному хэшу, хэш формируется как мд5 от пары id и email.
	 * @param $code - хэш пользователя.
	 */
	function GetByHash($code)
	{
		$query="SELECT * FROM ".PREFIX.$this->sTable." WHERE MD5(concat(`id`,`email`))='".$this->obDB->safesql($code)."'";
		$res=$this->obDB->query($query);
		if($this->obDB->num_rows($res)>0)
		{
			$arData=$this->obDB->get_row($res);
			return $arData;
		}
		return false;
	}

	/**
	 * Метод генерирует хэш для указанного пользователя
	 * @param $id int|array если передан номер - пользователь
	 * ищется в базе, если массив - генерируется на основе переданных данных
	 * @return string|false если код успешно создан - возвращется хэш иначе false
	 */
	function GenHash($id)
	{
		if(is_numeric($id))
		{
			$arUser=$this->GetRecord(array('id'=>$id));
		}
		else
		{
			$arUser=$id;
		}
		if($arUser['email']!='' && $arUser['id']>0) return md5($arUser['id'].$arUser['email']);
		return false;
	}

	/**
	 * Осуществляет принудительную авторизацию текущего пользователя. Как пользователя
	 * с указанным логином.
	 * @param $title string|array - логин пользователя или массив описания пользователя
	 */
	function LoginByTitle($title)
	{
		global $KS_EVENTS_HANDLER;
		$arUser=$title;
		if(is_string($title))
		{
			$arUser= $this->GetRecord(array('title' => $title));
		}
		if(is_array($arUser))
		{
			$this->LogAction($arUser['id'],'Попытка авторизации по логину',$arUser);

			if ($arUser['active'] == 0)	throw new CUserError('MAIN_USER_INACTIVE');
			if(($arUser['blocked_from']<=time())&&($arUser['blocked_till']>=time())) throw new CUserError('MAIN_USER_BLOCKED_TILL',0,date($this->obModules->GetConfigVar('main','time_format'),$arUser['blocked_till']));
			$_SESSION['cu_user_id']=$arUser['id'];
			$_SESSION['USER_IP']=$_SERVER['REMOTE_ADDR'];
			$this->is_login = true;
			$this->obDB->query("UPDATE ks_users SET pwd_updated=0,number_of_log_tries=0,last_visit=".time()." WHERE id='".$this->obDB->safesql($arUser['id'])."'");
			$onLoginParams = $this->userdata;
			$KS_EVENTS_HANDLER->Execute('main', 'onLogin', $onLoginParams);
			$this->OnSessionUpdate($arUser);
			return true;
		}
		return false;
	}

	/**
	 * Метод проверяет требования к паролю
	 */
	function CheckPasswordRequirements($password)
	{
		return strlen($password)>=6;
	}

	/**
	 * Метод выполняет генерацию хэша пароля из строки переданной методу
	 */
	function ConvertPassword($password)
	{
		return md5($password);
	}

	/**
	 * Метод осуществляет авторизацию пользователя по параметрам переданным
	 * в скрипт из браузера
	 */
	function login( $sLogin = false, $sPassword = false )
	{
		global $smarty, $KS_EVENTS_HANDLER,$ks_config;
		$this->is_login = false;

		if( !$sLogin )
			$sLogin = $_REQUEST['CU_LOGIN'];
		if( !$sPassword )
			$sPassword = $_REQUEST['CU_PASSWORD'];

		$username = $this->obDB->safesql($sLogin);			// Имя пользователя
		$password = $this->obDB->safesql($sPassword);		// Пароль
		$onBeforeLoginParams = array('username' => $username, 'password' => $password);
		if (!$KS_EVENTS_HANDLER->Execute('main', 'onBeforeLogin', $onBeforeLoginParams))	// Вызов обработчика перед входом
			throw new CError('MAIN_HANDLER_ERROR');
		if ($row = $this->GetRecord(array('title' => $username)))
		{
			$this->LogAction($row['id'],'Авторизация по паролю',array('pwd'=>$this->ConvertPassword($password),'password'=>$row['password']));

			if (strcmp($this->ConvertPassword($password), $row['password']) == 0)
			{
				$this->LoginByTitle($row);
			}
			else
			{
				$query="SELECT max(A.number_of_log_tries) as log_tryes " .
						"FROM ks_usergroups AS A, " .
							"ks_users_grouplinks as B " .
						"WHERE " .
							"B.user_id='{$row['id']}' AND " .
							"B.group_id=A.id AND " .
							"(B.date_start<".time()." OR B.date_start=0) AND " .
							"(B.date_end>".time()." OR B.date_end=0)" .
						"GROUP BY A.number_of_log_tries";
				$res=$this->obDB->query($query);
				if ($this->obDB->num_rows($res)>0)
				{
					$row1=$this->obDB->get_row($res);
				}
				else
				{
					$row1['log_tryes']=3;
				}
				if (($row['number_of_log_tries']+1) > $row1['log_tryes'])
				{
					$this->LogAction($row['id'],'Снятие активности по количеству попыток входа',array('done'=>$row['number_of_log_tries'],'available'=>$row1['log_tryes']));

					$this->obDB->query("UPDATE ks_users SET active=0,number_of_log_tries=number_of_log_tries+1 WHERE id='".intval($row['id'])."'");
					throw new CUserError('MAIN_USER_BLOCKED', 504);
				}
				else
				{
					$this->LogAction($row['id'],'Увеличение количества попыток входа',array('done'=>$row['number_of_log_tries']+1));

					$this->obDB->query("UPDATE ks_users SET number_of_log_tries=number_of_log_tries+1 WHERE id='".intval($row['id'])."'");
				}
				throw new CUserError('MAIN_USER_WRONG_PASSWORD', 501);
			}
		}
		else
		{
			throw new CUserError('MAIN_USER_WRONG_PASSWORD', 503);
		}
		$smarty->assign('USER', $this->userdata);
	}

	/**
	 * Метод разлогинивания пользователя
	 *
	 */
	function logout()
	{
		global $KS_EVENTS_HANDLER;
		/* Вызов обработчика перед выходом */
		$onBeforeLogoutParams = $this->userdata;
		$KS_EVENTS_HANDLER->Execute('main', 'onBeforeLogout', $onBeforeLogoutParams);

		$this->LogAction($this->ID(),'Деавторизация пользователя',array('session'=>$_SESSION,'user'=>$this->userdata));

		/* Уничтожаем пользовательскую сессию */
		CSessionManager::get_instance()->Destroy();
		//Стираем печеньку с токеном
		setcookie('ks_token','0',1,'/');
		$this->userdata=array();
		$this->is_login=false;

		/* Вызов обработчика после выхода */
		$onLogoutParams = array();
		$KS_EVENTS_HANDLER->Execute('main', 'onLogout', $onLogoutParams);
	}

	/**
	 * Метод определяет авторизован ли текущий пользователь сайта
	 * @return boolean true|false в зависимости от авторизации
	 */
	function IsLogin()
	{
		return $this->is_login;
	}

	/**
	 * Функция генерирует пароль
	 */
	function GenPassword($length=6)
	{
		return substr(str_shuffle(PASSWORD_CHARS),rand(0,strlen(PASSWORD_CHARS)-$length),$length);
	}

	/**
	 * Метод проверяет является ли пользователь администратором сайта
	 */
	function IsAdmin($id=false)
	{
		if(!$id) $id=$this->ID();
		$arGroups=$this->GetGroups($id);
		return in_array(1,$arGroups);
	}

	/**
	 * Метод сохраняет нового пользователя или изменяет данные в профиле существующего
	 *
	 * @version 1.1
	 * @since 07.05.2009
	 *
	 * 1. Добавлена фильтрация префикса полей перед вызовом обработчика onSave
	 * 2. Исправлены ошибки, связанные с формированием параметров для обработчика onSave
	 *
	 * Удалена вся лишняя фигня
	 *
	 * @param string $prefix Префикс полей пользователя
	 * @param array $data Массив полей (параметров) пользователя, которые нужно сохранить
	 * @param string $mytable Таблица пользователей
	 * @return int Идентификационный номер сохранённого пользователя
	 */
	function Save($prefix = "KS_", $data = "")
	{
		global $KS_EVENTS_HANDLER;
		/* Если мы не передаём данные для залогинивания непосредственно методу, то берём их из $_POST */
		if($data == "")
			$data = $_POST;

		$onBeforeSaveParams = $data;
		/* Выполнение обработчика перед сохранением профиля пользователя */
		if (!$KS_EVENTS_HANDLER->Execute('main', 'onBeforeSave', $onBeforeSaveParams))
			throw new CError('MAIN_HANDLER_ERROR');

		$this->AddCheckField('title');
		try
		{
			/* Читаем текущие настройки существующего пользователя и заодно проверяем данные о пароле*/
			if (isset($data[$prefix."id"]) && $data[$prefix."id"] > 0)
			{
				$arOldValue=$this->GetById($data[$prefix."id"]);
				if(isset($data[$prefix.'password']) && isset($data[$prefix.'password_c']) && $data[$prefix.'password']=='')
					unset($data[$prefix.'password']);
			}
			else
			{
				if(isset($data[$prefix.'password']))
				{
					if($data[$prefix.'password']=='')
						throw new CDataError('MAIN_USER_PASSWORD_REQUIRED');
				}
				else
					throw new CDataError('MAIN_USER_PASSWORD_REQUIRED');
				$data[$prefix.'date_register']=time();
			}

			/* Сохраняем данные пользователя и получаем его id */
			$nId = (!empty($data[$prefix."id"])) ? (int)$data[$prefix."id"] : 0;
			$this->LogAction($nId,'Сохранение записи в БД',array($data));

			if($res = parent::Save($prefix, $data))
			{
				/* Избавляемся от префикса, чтобы в обработчике не заниматься проверкой */
				$onSaveParams = $data;
				if ($prefix != '')
					foreach ($data as $data_key => $data_item)
						$onSaveParams[preg_replace("#^".$prefix."#","",$data_key)] = $data_item;
				if (!isset($onSaveParams["id"]) || $onSaveParams["id"] <= 0)
					$onSaveParams["new_user_id"] = $res;
				else
					$onSaveParams["previous_user_row"] = $arOldValue;
				if (!$KS_EVENTS_HANDLER->Execute('main', 'onSave', $onSaveParams))
					throw new CError('MAIN_HANDLER_ERROR');
				return $res;
			}
			else
				throw new CError('MAIN_USER_SAVE_ERROR');
		}
		catch (CError $e)
		{
			throw $e;
		}
		catch (Exception $e)
		{
			throw new CError('MAIN_SYSTEM_ERROR',100,$e);
		}
	}

	function Update($id,$arData,$bWhere=false)
	{
		if(count($arData)==1 && array_key_exists('last_visit',$arData))
		{
		}
		else
		{
			$this->LogAction($id,'Обновление записи в БД',array($arData));
		}
		return parent::Update($id,$arData,$bWhere);
	}

	/**
	 * Метод возвращает значение переменной пользователя
	 */
	function GetUserVar($var)
	{
		if(!is_array($this->arUserVars)) $this->arUserVars=array();
		if(is_string($var) && array_key_exists($var,$this->arUserVars))
		{
			return $this->arUserVars[$var];
		}
		else
		{
			return null;
		}
	}

	/**
	 * Метод устанавливает значение пользовательской переменной
	 */
	function SetUserVar($var,$value)
	{
		if(!is_array($this->arUserVars)) $this->arUserVars=array();
		$this->arUserVars[$var]=$value;
		$this->bUpdateVars=true;
		return true;
	}

	/**
	 * Служебный метод, выполняет запись данных в БД
	 */
	function WriteUserVars()
	{
		if($this->bUpdateVars && $this->IsLogin())
		{
			$this->Update($this->ID(),array('user_vars'=>json_encode($this->arUserVars)));
		}
	}

	/**
	 * Метод удаления пользователей
	 *
	 * @param array $arFilter Ассоциативный массив с параметром удаления пользователя: id => id_пользователя
	 * @return int id удалённого пользователя
	 */
	function DeleteItems($arFilter)
	{
		global $KS_EVENTS_HANDLER;
		$onBeforeDeleteParams['id'] = $arFilter['id'];
		if (!$KS_EVENTS_HANDLER->Execute('main', 'onBeforeDelete', $onBeforeDeleteParams))		// Вызов обработчика перед удалением
			throw new CError('MAIN_HANDLER_ERROR',0,$KS_EVENTS_HANDLER->GetLastEvent());

		$this->LogAction($arUser['id'],'Удаление пользователей по фильтру',$arFilter);

		$res = parent::DeleteItems($arFilter);			// Удаление элемента
		$onDeleteParams = array();
		if (!$KS_EVENTS_HANDLER->Execute('main', 'onDelete', $onDeleteParams))					// Вызов обработчика при удалении
			throw new CError('MAIN_HANDLER_ERROR',0,$KS_EVENTS_HANDLER->GetLastEvent());
		return $res;
	}

	var $groups_list;
	protected $userdata;
	/**
	 * Массив со списком групп к которым принадлежит данный пользователь.
	 * @var array $arInGroups
	 */
	protected $arInGroups;
	protected $arAccessLevels; 		/**<Хранит список уровней доступа к которым был сделан запрос*/

	/**
	 * Метод возвращает массив упорядоченных по номеру групп, к которым принадлежит текущий пользователь
	 *
	 * @version 1.1
	 * @since 11.05.2009
	 *
	 * Добавлено получение групп для незалогиненного пользователя
	 *
	 * @param int $user_id id незалогиненного пользователя
	 * @return array
	 */
	function GetGroups($user_id = 0)
	{
		global $ks_db;

		/* Если неоткуда взять id, тогда выходим */
		if ($this->ID()==0 && $user_id == 0)
			return array(0);

		/* Если указан id пользователя, то работаем по нему */
		$possible_user_id = intval($user_id);
		if ($possible_user_id > 0)
			$id = $possible_user_id;
		else
			$id = $this->ID();

		if($id!=$this->ID())
		{
			$time = time();
			$arFilter=array(
				'user_id'=>$id,
				'AND'=>array(
					array(
						'OR'=>array(
							array('>=date_end'=>$time),
							array('date_end'=>0)
						),
					),
					array(
						'OR'=>array(
							array('<=date_start'=>$time),
							array('date_start'=>0)
						),
					),
				)
			);
			$arResult = array(0);
			if($arList=$this->obLinks->GetList(array('group_id'=>'asc'),$arFilter))
			{
				foreach($arList as $arRow)
				{
					$arResult[] = $arRow['group_id'];
				}
			}
		}
		elseif ($this->arInGroups == false)
		{
			$time = time();
			$arFilter=array(
				'user_id'=>$id,
				'AND'=>array(
					array(
						'OR'=>array(
							array('>=date_end'=>$time),
							array('date_end'=>0)
						),
					),
					array(
						'OR'=>array(
							array('<=date_start'=>$time),
							array('date_start'=>0)
						),
					),
				)
			);
			$arResult = array(0);
			if($arList=$this->obLinks->GetList(array('group_id'=>'asc'),$arFilter))
			{
				foreach($arList as $arRow)
				{
					$arResult[] = $arRow['group_id'];
				}
			}
			$this->arInGroups = $arResult;
		}
		else
			$arResult=$this->arInGroups;
		return $arResult;
	}

	function ID()
	{
		if(is_array($this->userdata) && array_key_exists('id',$this->userdata))
		{
			return $this->userdata['id'];
		}
		return 0;
	}

	function Email()
	{
		return $this->userdata['email'];
	}

	/**
	 * Метод, возвращает полный список групп к которым привязан указанный пользователь.
	 * Обычно используется в системе администрирования.
	 * @param id - номер пользователя
	 */
	function GetAllGroups($id)
	{
		$arResult=false;
		if($arList=$this->obLinks->GetList(array('group_id'=>'asc'),array('user_id'=>intval($id))))
		{
			$arResult=array();
			foreach($arList as $arRow)
			{
				$arResult[$arRow['group_id']]=$arRow;
			}
		}
		return $arResult;
	}

	/**
	 * Возвращает уровень прав доступа к определенному модулю.
	 * @param $module - текстовый идентификатор модуля.
	 */

	function GetLevel($module,$iUserID=false)
	{
		$obAccess=new CModulesAccess();
		$arGroups=$this->GetGroups($iUserID);
		$level=10;
		if(array_key_exists($module,$this->arAccessLevels))
		{
			$res=$this->arAccessLevels[$module];
		}
		elseif($res=$obAccess->GetList(array('group_id'=>'asc'),array('module'=>$module)))
		{
			$this->arAccessLevels[$module]=$res;
		}
		else
		{
			return $level;
		}
		foreach($res as $key=>$value)
		{
			//Проверяем принадлежит ли пользователь найденой группе, если да то смотрим больше ли текущий
			//уровень чем текущий, если больше - считаем его текущим.
			if((in_array($value['group_id'],$arGroups))&&($level>$value['level']))
			{
				$level=$value['level'];
			}
		}
		return $level;
	}

	/**
	 * Метод размещает пользователя в указанных группах, при этом стираются все привязки
	 * пользователя к другим группам
	 * @return integer количество групп в которые был внесён пользователь
	 */
	function SetAllUserGroups($iUserID,$arGroups)
	{
		if(!is_array($arGroups)) throw new CDataError('SYSTEM_ARRAY_REQUIRED');
		if($arUser=$this->GetRecord(array('id'=>$iUserID)))
		{
			if(!is_array($arGroups)&&is_numeric($arGroups)) $arGroups=array($arGroups);
			if(!is_array($arGroups)) return false;
			$this->obLinks->DeleteItems(array('user_id'=>$arUser['id']));
			$iDone=0;
			foreach($arGroups as $group)
			{
				if(is_numeric($group))
				{
					$arFields=array(
						'user_id'=>$arUser['id'],
						'group_id'=>$group,
						'date_start'=>0,
						'date_end'=>0,
					);
					if($this->obLinks->Save('',$arFields))
						$iDone++;
					else
						throw new CError('SYSTEM_USER_LINK_SAVE_ERROR');
				}
				elseif(is_array($group))
				{
					$arFields=array(
						'user_id'=>$arUser['id'],
						'group_id'=>$group['id'],
						'date_start'=>intval($group['date_from']),
						'date_end'=>intval($group['date_to']),
					);
					if($this->obLinks->Save('',$arFields))
						$iDone++;
					else
						throw new CError('SYSTEM_USER_LINK_SAVE_ERROR');
				}
			}
			return $iDone;
		}
		return false;
	}

	/**
	 * Метод добавляет указанного пользователя в указанную группу
	 * @param $uid integer номер пользователя
	 * @param $gid integer номер группы пользователя
	 */
	function SetUserGroup($uid,$gid,$from=false,$to=false)
	{
		if($arRecord=$this->obLinks->GetRecord(array('user_id'=>$uid,'group_id'=>$gid)))
		{
			if($from!=false || $to!=false)
			{
				$arUpdate=array();
				if($from>0) $arUpdate['date_start']=intval($from);
				if($to>0) $arUpdate['date_end']=intval($from);
				$this->obLinks->Update($arRecord['id'],$arUpdate);
			}
		}
		else
		{
			$arFields=array(
				'user_id'=>intval($uid),
				'group_id'=>intval($gid),
				'date_start'=>time(),
			);
			if($from>0) $arFields['date_start']=intval($from);
			if($to>0) $arFields['date_end']=intval($to);
			$this->obLinks->Save('',$arFields);
		}
	}

	/**
	 * Метод убирает указанного пользователя из указанной группы
	 * @param $uid integer номер пользователя
	 * @param $gid integer номер группы пользователя
	 */
	function UnsetUserGroup($uid,$gid)
	{
		if($arRecord=$this->obLinks->GetRecord(array('user_id'=>$uid,'group_id'=>$gid)))
		{
			$this->obLinks->Delete($arRecord['id']);
		}
	}
}

