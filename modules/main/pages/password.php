<?
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
	function Run()
	{
		if ($_GET['lostpwd'] == 'Y')
		{
			$step = 1;
			$iStep=1;
			if(array_key_exists('step',$_POST))
				$iStep=intval($_POST);
			if ($iStep == 2)
			{
				$step = 2;
				/* Переданный почтовый адрес администратора */
				$email = $_POST['email'];
				if(strlen($email)==0)
				{
					$this->smarty->assign('message', $this->obModules->GetText('MAIN_ERROR_NOT_EMAIL'));
					$step = 1;
				}
				elseif(!IsEmail($email))
				{
					$this->smarty->assign('message', $this->obModules->GetText('MAIN_ERROR_NOT_EMAIL'));
					$step = 1;
				}
				elseif (!CCaptcha::CheckCaptcha($_POST['c']))
				{
					$this->smarty->assign('email',$email);
					$this->smarty->assign('message', $this->obModules->GetText('MAIN_ERROR_WRONG_CAPTCHA'));
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
									$this->smarty->assign('message',$this->obModules->GetText('MAIN_MAIL_SEND')." <b>".$email."</b>");
								else
									$this->smarty->assign('message', $this->obModules->GetText('MAIN_MAIL_NOT_SEND'). " <b>".$email."</b>");
							}
							else
							{
								$this->smarty->assign('message', $this->obModules->GetText('MAIN_PASSWORD_RESTORE_ERROR'));
							}
						}
						else
						{
							$step = 1;
							$this->smarty->assign('message', $this->obModules->GetText('MAIN_ADMINISTRATOR_RIGHT_REQUIED'));
						}
					}
					else
					{
						$step = 1;
						$smarty->assign('message', $this->obModules->GetText("MAIN_USER_NOT_REGISTERED"));
					}
				}
			}
			elseif ($iStep == 3)
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
							$this->smarty->assign('pwd', $pwd);
						}
						else
							$this->smarty->assign('message', $this->obModules->GetText('MAIN_PASSWORD_ALREADY_CHANGED'));
					}
					else
					{
						$step = 1;
						$this->smarty->assign('message', $this->obModules->GetText("MAIN_USER_NOT_REGISTERED"));
					}
				}
				else
				{
					$this->smarty->assign('message', $this->obModules->GetText('SYSTEM_WRONG_ADMIN_PATH'));
					$step=1;
				}
			}
			$this->smarty->assign('header', array('title' => $this->obModules->GetText('MAIN_PASSWORD_RESTORE_STEP').$step));
			$this->smarty->assign('step', $step);
			$this->smarty->display('admin/password.tpl');
			die();
		}
		else
		{
			CUrlParser::get_instance()->Redirect('/admin.php');
		}
	}
}