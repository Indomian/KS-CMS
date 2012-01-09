<?php
/**
 * @file main/pages/password.php
 * Файл обеспечивает восстановление пароля пользователя
 * Файл проекта kolos-cms.
 *
 * Изменен 13.01.2011
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.5.5
 */
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CModuleAdmin.php';

class CmainAIpassword extends CModuleAdmin
{
	private $arResult;

	function __construct($module_name,&$smarty,&$parent)
	{
		parent::__construct($module_name,$smarty,$parent);
		$this->arResult=array();
	}

	/**
	 * Данный метод вызывается при аякс запросе
	 */
	function RunAjax()
	{
		if(isset($_GET['lostpwd']) && $_GET['lostpwd'] == 'Y')
		{
			$this->arResult['step'] = 1;
			$iStep=1;
			if(array_key_exists('step',$_POST))
				$iStep=intval($_POST);
			if ($iStep == 2)
				$this->arResult['step']=$this->LostPasswordStep2();
			elseif ($iStep == 3)
				$this->arResult['step']=$this->LostPasswordStep3();
		}
		else
		{
			$this->arResult['result']='ok';
			if(isset($_SESSION['cu_user']))
				$this->arResult['lastHit']=$_SESSION['cu_user'];
			if($this->obUser->IsLogin())
			{
				if($this->obModules->GetConfigVar('main','user_inactive_check')==1 && $this->obModules->GetConfigVar('main','user_inactive_time')>0)
					if($this->arResult['lastHit']<time())
						$this->arResult['action']='relog';
			}
			else
				$this->arResult['action']='relog';
		}
		$this->smarty->assign('data',$this->arResult);
	}

	/**
	 * Метод вызывается при выводе окна авторизации
	 */
	private function LoginWindow()
	{
		$this->smarty->assign('VERSION',$this->obModules->GetVersionData());
		$this->smarty->assign('isajax',$this->obModules->GetMode()!='full');
		$this->smarty->assign('backurl',$_SERVER['REQUEST_URI']);
	}

	private function LostPasswordStep2()
	{
		$step = 2;
		/* Переданный почтовый адрес администратора */
		if(isset($_POST['email']))
			$email = $_POST['email'];
		else
			$this->arResult['message']=$this->obModules->GetText('MAIN_ERROR_NOT_EMAIL');
		if(strlen($email)==0)
		{
			$this->arResult['message']=$this->obModules->GetText('MAIN_ERROR_NOT_EMAIL');
			$step = 1;
		}
		elseif(!IsEmail($email))
		{
			$this->arResult['message']=$this->obModules->GetText('MAIN_ERROR_NOT_EMAIL');
			$step = 1;
		}
		elseif(!isset($_POST['c']) || !CCaptcha::CheckCaptcha($_POST['c']))
		{
			$this->arResult['email']=$email;
			$this->arResult['message']=$this->obModules->GetText('MAIN_ERROR_WRONG_CAPTCHA');
			$step = 1;
		}
		else
		{
			/* Ищем пользователя с таким почтовым адресом в базе */
			if ($arUser = $this->obUser->GetRecord(array('email' => $email)))
			{
				/* Получаем группы, к которым относится пользователь, и проверяем, что он администратор */
				if ($this->obUser->IsAdmin($arUser['id']))
				{
					$sCode=$this->obUser->GenPassword(6);
					$iId=$arUser['id'];
					if($this->obUser->Update($arUser['id'],array('code'=>$sCode)))
					{
						/* Запоминаем код в Смарти */
						$this->smarty->assign('code', $sCode);
						$this->smarty->assign('user_id',$iId);
						/* Отправка письма забывшему пароль */
						$msg = $this->smarty->fetch('admin/password_mail.tpl');
						if (mail($email, $this->obModules->GetText('MAIN_PASSWORD_RESTORE'), $msg))
							$this->arResult['message']=$this->obModules->GetText('MAIN_MAIL_SEND')." <b>".$email."</b>";
						else
							$this->arResult['message']=$this->obModules->GetText('MAIN_MAIL_NOT_SEND'). " <b>".$email."</b>";
					}
					else
						$this->arResult['message']=$this->obModules->GetText('MAIN_PASSWORD_RESTORE_ERROR');
				}
				else
				{
					$step = 1;
					$this->arResult['message']=$this->obModules->GetText('MAIN_ADMINISTRATOR_RIGHT_REQUIED');
				}
			}
			else
			{
				$step = 1;
				$this->arResult['message']=$this->obModules->GetText("MAIN_USER_NOT_REGISTERED");
			}
		}
		return $step;
	}

	function LostPasswordStep3()
	{
		/* По присланому коду восстанавливаем пароль */
		if (array_key_exists('c', $_GET) && array_key_exists('id',$_GET))
		{
			if($arUser=$this->obUser->GetRecord(array('id' => intval($_GET['id']),'code'=>$_GET['c'])))
			{
				if ($arUser['pwd_updated'] == 0)
				{
					$step = 3;
					$pwd = $this->obUser->GenPassword();
					$data = array
					(
						'id' => $arUser['id'],
						'password' => $pwd,
						'password_c' => $pwd,
						'pwd_updated' => 1,
						'code'=>'',
					);
					$this->obUser->Save('', $data);
					$this->arResult['pwd']=$pwd;
				}
				else
					$this->arResult['message']=$this->obModules->GetText('MAIN_PASSWORD_ALREADY_CHANGED');
			}
			else
			{
				$step = 1;
				$this->arResult['message']=$this->obModules->GetText("MAIN_USER_NOT_REGISTERED");
			}
		}
		else
		{
			$this->arResult['message']=$this->obModules->GetText('SYSTEM_WRONG_ADMIN_PATH');
			$step=1;
		}
		return $step;
	}

	/**
	 * Метод выполняет вывод окна авторизации
	 */
	function Run()
	{
		if(isset($_GET['lostpwd']) && $_GET['lostpwd'] == 'Y')
		{
			$step = 1;
			$iStep=1;
			if(array_key_exists('step',$_POST))
				$iStep=intval($_POST);
			if ($iStep == 2)
			{
				$step=$this->LostPasswordStep2();
			}
			elseif ($iStep == 3)
			{
				$step=$this->LostPasswordStep3();
			}
			foreach($this->arResult as $key=>$value)
				$this->smarty->assign($key,$value);
			$this->smarty->assign('header', array('title' => $this->obModules->GetText('MAIN_PASSWORD_RESTORE_STEP').$step));
			$this->smarty->assign('step', $step);
			$this->smarty->display('admin/password.tpl');
			die();
		}
		else
		{
			$this->LoginWindow();
			//CUrlParser::get_instance()->Redirect('/admin.php');
			//Отображаем окно входа и заканчиваем.
			$this->smarty->display('admin/login.tpl');
			exit();
		}
	}
}