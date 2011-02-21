<?php

if (!defined('KS_ENGINE')) die("Hacking attempt!");
include_once MODULES_DIR.'/main/libs/class.CUsersCommon.php';
include_once MODULES_DIR.'/photogallery/libs/class.ImageResizer.php';

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

class CUser extends CBaseUser
{
	public $userdata;
	protected $is_login;
	private $arAllowExt=array('jpg','jpeg','png');
	private $arUserVars;
	private $bUpdateVars;
	private $obModules;

	/**
	 * Конструктор объекта пользовательского класса
	 *
	 * Выполняет инициализацию пользовательской сессии
	 *
	 * @param string $sTable Таблица пользователей в базе данных
	 */
	function __construct($sTable = "users")
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

		/* Вызов обработчика перед инициализацией объекта класса */
		$onBeforeInitParams = array();
		if (!$KS_EVENTS_HANDLER->Execute('main', 'onBeforeUserObjectInit', $onBeforeInitParams))
			throw new CError('MAIN_HANDLER_ERROR',0,$KS_EVENTS_HANDLER->GetLastEvent());

		/* Устанавливаем IP пользователя, если он еще не входил */
		if ($_SESSION['USER_IP'] == '')
			$_SESSION['USER_IP'] = $_SERVER['REMOTE_ADDR'];

		/* Инициализация полей класса CObject */
		parent::__construct($sTable);

		$this->userdata = array();
		$this->arAccessLevels = array();
		$this->sUploadPath = 'users/';
		$this->fType = 'elm';
		$this->sWidth=0; //максимальная высота аватара
		$this->sHeight=0; //максимальная ширина аватара
		$this->sRatio=true; //маштабирование
		$this->sRatio_wb=false; //добаление белых полей при маштабировании
		$this->sSize=0; // максимальный размер фала аватара
		$this->bUpdateVars=false;

		/* Подгружаем список уровней доступа */
		$this->obDB->query("SELECT id, title FROM ks_usergroups ORDER BY id");
		while($row = $this->obDB->get_row())
			$this->groups_list[$row['id']] = $row['title'];
		$this->arInGroups = false;
		$this->is_login = false;

		try
		{
			/* Проверка адреса пользователя и имени его сессии */
			if($_SESSION['USER_IP'] != $_SERVER['REMOTE_ADDR'])
			{
				$this->logout();
			}
			elseif(array_key_exists('CU_ACTION', $_REQUEST))
			{
				if (strcmp($_REQUEST['CU_ACTION'], 'logout') == 0)
				{
					/* Разлогинивание */
					$this->logout();
				}
			}
			elseif (isset($_SESSION['cu_user']))
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
								$this->OnAfterLogin($arRow);
							}
							else
							{
								throw new CUserError('MAIN_USER_BLOCKED');
								$this->logout();		// Если пользователь стал неактивным, то разлогиниваемся
								return;
							}
						}
						else
						{
							throw new CUserError('MAIN_USER_BLOCKED');
							//Если вобще не нашли такого пользователя - разлогиним его нафиг
							$this->logout();
							return;
						}
					}
				}
				else
				{
					$this->logout();
				}
			}
		}
		catch (CUserError $e)
		{
			$this->logout();
			$this->userdata['LAST_ERROR']=$e->__toString();
			throw $e;
		}
		$smarty->assign('USER', $this->userdata);
		/* Вызов обработчика при инициализации объекта класса */
		$onInitParams = $this->userdata;
		$onUserObjectInitSuccess = $KS_EVENTS_HANDLER->Execute('main', 'onUserObjectInit', $onInitParams);
	}

	/**
	 * Метод вызывается при освобождении памяти (удалении объекта класса)
	 */
	function __destruct()
	{
	}

	/**
	 * Метод вызывается после выполнения базовых операций по авторизации
	 */
	private function OnAfterLogin($arUser)
	{
		global $KS_EVENTS_HANDLER;
		$this->userdata=$arUser;
		$this->arUserVars=json_decode($arUser['user_vars'],true);
		$this->userdata['vars']=$this->arUserVars;
		$_SESSION['cu_user'] = time() + $this->obModules->GetConfigVar('main','user_inactive_time',3600);
		$this->Update($this->userdata['id'],array('last_visit'=>time()));
		$this->is_login = true;
		/* Вызов обработчика при обновлении времени жизни сессии */
		$onSessionUpdateParams = $this->userdata;
		$KS_EVENTS_HANDLER->Execute('main', 'onUserSessionUpdate', $onSessionUpdateParams);
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
			if ($arUser['active'] == 0)	throw new CUserError('MAIN_USER_BLOCKED');
			if(($arUser['blocked_from']<=time())&&($arUser['blocked_till']>=time())) throw new CUserError('MAIN_USER_BLOCKED_TILL',0,date($this->obModules->GetConfigVar('main','time_format'),$arUser['blocked_till']));
			$_SESSION['cu_user_id']=$arUser['id'];
			$_SESSION['USER_IP']=$_SERVER['REMOTE_ADDR'];
			$this->is_login = true;
			$this->obDB->query("UPDATE ks_users SET pwd_updated=0,number_of_log_tries=0,last_visit=".time()." WHERE id='".$this->obDB->safesql($arUser['id'])."'");
			$onLoginParams = $this->userdata;
			$KS_EVENTS_HANDLER->Execute('main', 'onLogin', $onLoginParams);
			$this->OnAfterLogin($arUser);
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
	function login()
	{
		global $smarty, $KS_EVENTS_HANDLER,$ks_config;
		$this->is_login = false;
		$username = $this->obDB->safesql($_REQUEST['CU_LOGIN']);			// Имя пользователя
		$password = $this->obDB->safesql($_REQUEST['CU_PASSWORD']);		// Пароль
		$onBeforeLoginParams = array('username' => $username, 'password' => $password);
		if (!$KS_EVENTS_HANDLER->Execute('main', 'onBeforeLogin', $onBeforeLoginParams))	// Вызов обработчика перед входом
			throw new CError('MAIN_HANDLER_ERROR');
		if ($row = $this->GetRecord(array('title' => $username)))
		{
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
					$this->obDB->query("UPDATE ks_users SET active=0,number_of_log_tries=number_of_log_tries+1 WHERE id='".intval($row['id'])."'");
					throw new CUserError('MAIN_USER_BLOCKED', 504);
				}
				else
				{
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

		/* Уничтожаем пользовательскую сессию */
		$_SESSION = array();
		unset($_COOKIE[session_name()]);
		session_destroy();
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
	 * Метод добавляет указанного пользователя в указанную группу
	 * @param $uid integer номер пользователя
	 * @param $gid integer номер группы пользователя
	 * @todo Определить класс для линков групп пользователей и убрать вызовы базы дынных напрямую
	 */
	function SetUserGroup($uid,$gid)
	{
		$this->obDB->query("INSERT INTO " . PREFIX . $this->sLinksTable .
			" (user_id,group_id,date_start,date_end) VALUES " .
			" ('".intval($uid)."','".intval($gid)."','".time()."','0')"
		);
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
	 * @depricated 27.01.11
	 */
	function is_admin($id)
	{
		if ($this->is_login)
		{
			$arGroups=$this->GetGroups();
			return in_array(1,$arGroups);
		}
		else
		{
			return false;
		}
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

	protected function _ParseItem(&$item)
	{
		$item['group_name']=$this->groups_list[$item['group_id']];
		return true;
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
	 * @param string $prefix Префикс полей пользователя
	 * @param array $data Массив полей (параметров) пользователя, которые нужно сохранить
	 * @param string $mytable Таблица пользователей
	 * @return int Идентификационный номер сохранённого пользователя
	 */
	function Save($prefix = "KS_", $data = "", $mytable = "")
	{
		global $KS_ERROR, $KS_EVENTS_HANDLER;
		/* Если мы не передаём данные для залогинивания непосредственно методу, то берём их из $_POST */
		if($data == "")
			$data = $_POST;

		$onBeforeSaveParams = $data;
		/* Выполнение обработчика перед сохранением профиля пользователя */
		if (!$KS_EVENTS_HANDLER->Execute('main', 'onBeforeSave', $onBeforeSaveParams))
			throw new CError('MAIN_HANDLER_ERROR');

		/* Длина имени пользователя должна быть не менее 4 */
		if(array_key_exists($prefix . 'title', $data) && (strlen($data[$prefix . 'title']) < 4))
			throw new CError('MAIN_USER_TOO_SHORT_LOGIN');

		/* Проверка пароля на непустоту и его сравнение с повторением пароля */
		if(($data[$prefix . 'password'] != "") && (strcmp($data[$prefix . 'password'], $data[$prefix . 'password_c']) == 0))
			$data[$prefix . 'password'] = $this->ConvertPassword($data[$prefix . 'password']);
		elseif($data[$prefix . 'password'] != $data[$prefix . 'password_c'])
			throw new CError('MAIN_USER_WRONG_PASSWORD_CONFIRM');
		else
			unset($data[$prefix . 'password']);

		$data[$prefix . "last_visit"] = 0;
		$data[$prefix . "number_of_log_tries"] = 0;
		if(!array_key_exists($prefix . "pwd_updated",$data))
			$data[$prefix . "pwd_updated"] = 0;

		$this->AddFileField('img');
		$this->AddCheckField('title');
		$this->obDB->begin();			// соединяемся с базой данных ЦМС
		try
		{

			/* Читаем текущие настройки существующего пользователя */
			if ($data[$prefix."id"] > 0)
				$previous_user_row = $this->GetRecord(array("id" => $data[$prefix."id"]));
			else
				$data[$prefix.'date_register']=time();
			//Здесь добавлена проверка на то, что файл вобще заливали

			if($_FILES[$prefix.'img']['error']==0)
			{

				//проверка размера файла аватара, если больше чем положено, выводим ошибку
				if($this->sSize)
				{
				    if($_FILES[$prefix."img"]["size"] > ($this->sSize*1024))
				    {
					throw new CError('USER_AVA_SIZE_BIG', 0 , '(максимальный размер '.($this->sSize*1024).'кб)');
				    }
				}
				//Проверка расширений файла

				$info = pathinfo(strtolower($_FILES[$prefix."img"]["name"]));
				if(!in_array($info['extension'],$this->arAllowExt)&&($info['extension']))
				{
					throw new CError('PHOTOGALLERY_WRONG_FILE');
				}
				//Проверка для самых хитрых, если переименовали расширение файла
				if ($_FILES[$prefix.'img'])
				{
				  $type=getimagesize($_FILES[$prefix . "img"]['tmp_name']);
				  if($type[2]!=2 &&  $type[2]!=3)
				  {
					  throw new CError('PHOTOGALLERY_WRONG_FILE');
				  }
				}
			}
			else
			{
				switch($_FILES[$prefix.'img']['error'])
				{
					case UPLOAD_ERR_FORM_SIZE:
					case UPLOAD_ERR_INI_SIZE:
						throw new CError('USER_AVA_SIZE_BIG', 0 , '(максимальный размер '.($this->sSize*1024).'кб)');
					break;
					case  UPLOAD_ERR_NO_FILE:
					break;
					default:
						throw new CError('SYSTEM_FILE_NOT_FOUND_OR_NOT_WRITABLE');
				}
			}
			/* Сохраняем данные пользователя и получаем его id */
			$res = parent::Save($prefix, $data, $mytable);
			if($_FILES[$prefix.'img']['error']==0)
			{
				//если данные успешно сохранились, делаем проверку дефолтных значений
    	        if($res && $this->sWidth && $this->sHeight && $_FILES[$prefix.'img'])
                {
				    //получаем данные созданного пользователя (существкещего)
                    $data = $this->GetRecord(array("id" => $res));
				    //ресайзим картинку как нам надо
                    $obPhoto = new ImageResizer("/uploads/".$data['img']);
			    	//дирректорию не создаем, затираем старый файл
                    $obPhoto->isCreateDir =false;
                    $obPhoto->Resize($this->sWidth, $this->sHeight, $this->sRatio,$this->sRatio_wb,"/uploads/".$this->sUploadPath);
				}
			}

			/* Избавляемся от префикса, чтобы в обработчике не заниматься проверкой */
			if ($prefix != '')
			{
				foreach ($data as $data_key => $data_item)
				{
					$new_key = preg_replace("#^" . $prefix . "(.*)$#", "$1", $data_key);
					$onSaveParams[$new_key] = $data_item;
				}
			}
			else
				$onSaveParams = $data;

			if (!isset($onSaveParams["id"]) || $onSaveParams["id"] <= 0)
			{
				/* Устанавливаем id только что созданного пользователя */
				$onSaveParams["new_user_id"] = $res;
			}
			else
			{
				/* Запоминаем предыдущие параметры юзера */
				$onSaveParams["previous_user_row"] = $previous_user_row;
			}

			if (!$KS_EVENTS_HANDLER->Execute('main', 'onSave', $onSaveParams))
				throw new CError('MAIN_HANDLER_ERROR');
		}
		catch (CError $e)
		{
			$this->obDB->rollback();
			throw $e;
		}
		catch (Exception $e)
		{
			$this->obDB->rollback();
			throw new CError('MAIN_SYSTEM_ERROR',100,$e);
		}
		$this->obDB->commit();
		return $res;
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
		$res = parent::DeleteItems($arFilter);			// Удаление элемента
		$onDeleteParams = array();
		if (!$KS_EVENTS_HANDLER->Execute('main', 'onDelete', $onDeleteParams))					// Вызов обработчика при удалении
			throw new CError('MAIN_HANDLER_ERROR',0,$KS_EVENTS_HANDLER->GetLastEvent());
		return $res;
	}
}

