<?php
/**
 * @file function.WaveForm.php
 * Виджет выполняющий работу по отображению формы добавления комментариев
 * Файл проекта kolos-cms.
 *
 * @since 10.12.2010
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.5.4-16
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

/**
 * Функция производит вывод комментарии
 * @param $params массив параметров.
 * @param $subsmarty - указатель на объект смарти.
 * Параметры могут быть следующими:
 * 	count - количество выводимых элементов;
 *  hash - ключ элемента к которому выводятся комментарии
 *  filter - массив для фильтрации выводимых записей;
 */
function smarty_function_WaveForm($params,&$subsmarty)
{
	global $USER,$KS_MODULES,$KS_URL;
	//Проверка и инициализация аякса
	$arData=array();
	/* Проверка общих прав на просмотр тем */
	$arData['level'] = $USER->GetLevel('wave');
	if ($arData['level'] > KS_ACCESS_WAVE_ADD_GUEST) throw new CAccessError("WAVE_ACCESS_POST", 403);
	if($params['hash']=='') throw new CDataError("WAVE_HASH_REQUIRED");
	$obPosts=new CWavePosts();
	$arPost=array();
	if($USER->IsLogin())
	{
		$arPost['user_email']=$USER->Email();
		$arPost['user_name']=$USER->userdata['title'];
		$arPost['user_id']=$USER->ID();
	}
	try
	{
		if($_SERVER['REQUEST_METHOD']=='POST' && $KS_URL->CheckPostHash())
		{
			if($arData['level']<KS_ACCESS_WAVE_VIEW)
			{
				if(array_key_exists('addpost',$_POST))
				{
					//Операция по добавлению сообщения
					$bError=false;
					$arPost=array(
						'hash'=>$params['hash'],
						'active'=>1,
						'parent_id'=>intval($_POST['parent_id'])
					);
					if($arData['level']>=KS_ACCESS_WAVE_ADD_GUEST)
					{
						$arPost['active']=0;
					}
					$arPost['content']=EscapeHTML($_POST['WV_content']);
					if(!$USER->IsLogin())
					{
						if($KS_MODULES->GetConfigVar('wave','use_captcha',0)==1)
						{
							if (!CCaptcha::CheckCaptcha($_POST['c']))
							{
								$bError=$KS_MODULES->AddNotify("WV_WRONG_CAPTCHA");
							}
						}
						$arPost['user_email']=EscapeHTML($_POST['WV_user_email']);
						$arPost['user_name']=EscapeHTML($_POST['WV_user_name']);
						$arPost['user_id']=-1;
					}
					else
					{
						$arPost['user_email']=$_POST['WV_user_name']==''?$USER->Email():EscapeHTML($_POST['WV_user_email']);
						$arPost['user_name']=$_POST['WV_user_name']==''?$USER->userdata['title']:EscapeHTML($_POST['WV_user_name']);
						$arPost['user_id']=$USER->ID();
					}

					//Проверяем значения обязательных полей
					if($KS_MODULES->GetConfigVar('wave','field_necessary_user_name',0)==1)
						if(strlen($arPost['user_name'])<1)
							$bError=$KS_MODULES->AddNotify("WV_NAME_ERROR");
					if($KS_MODULES->GetConfigVar('wave','field_necessary_user_email',0)==1)
					{
						if(!IsEmail($arPost['user_email']) || strlen($arPost['user_email'])==0)
							$bError=$KS_MODULES->AddNotify("WV_MAIL_ERROR");
					}
					elseif(strlen($arPost['user_email'])>0 && !IsEmail($arPost['user_email']))
							$bError=$KS_MODULES->AddNotify("WV_MAIL_ERROR");
					if($KS_MODULES->GetConfigVar('wave','field_necessary_content',0)==1)
						if(strlen($arPost['content'])<1)
							$bError=$KS_MODULES->AddNotify("WAVE_TEXT_ERROR");

					if($arData['level']>KS_ACCESS_WAVE_ANSWER && $arPost['parent_id']>0)
					{
						$bError=$KS_MODULES->AddNotify('WAVE_ANSWER_DENIED');
					}
					if(!$bError)
					{
						if($id=$obPosts->Save('',$arPost))
						{
							if($arData['level']>=KS_ACCESS_WAVE_ANSWER_GUEST)
							{
								$KS_MODULES->AddNotify("WAVE_ADD_OK",'',NOTIFY_MESSAGE);
							}
							$arData['ok']=1;
						}
					}
					else
					{
						throw new CDataError('WAVE_FIELDS_ERROR');
					}
				}
			}
			else
			{
				throw new CAccessError('WAVE_ACCESS_POST');
			}
		}
	}
	catch(CError $e)
	{
		$arData['error']=$e->__toString();
	}
	$arData['user_name_title']=$KS_MODULES->GetConfigVar('wave','field_title_user_name',$KS_MODULES->GetText('field_user_name'));
	$arData['content_title']=$KS_MODULES->GetConfigVar('wave','field_title_content',$KS_MODULES->GetText('field_content'));
	$arData['user_email_title']=$KS_MODULES->GetConfigVar('wave','field_title_user_email',$KS_MODULES->GetText('field_user_email'));
	$arData['captcha_title']=$KS_MODULES->GetConfigVar('wave','field_title_captcha',$KS_MODULES->GetText('field_captcha'));
	if(!$USER->IsLogin())
	{
		if($KS_MODULES->GetConfigVar('wave','use_captcha',0)==1)
		{
			$subsmarty->assign('use_captcha',1);
		}
	}
	$subsmarty->assign('data',$arData);
	$subsmarty->assign('post',$arPost);
	//Код для генерации пути к шаблону или вывод ошибки об отсутсвтии шаблона
	return $KS_MODULES->RenderTemplate($subsmarty,'/wave/WaveForm',$params['global_template'],$params['tpl']);
}

function widget_params_WaveForm()
{
	$arFields=array(
		'hash'=>array(
			'title'=>'Ключ записи к которой выводятся комментарии',
			'type'=>'text',
			'value'=>''
		),
	);
	return array(
		'fields'=>$arFields,
	);
}
?>
