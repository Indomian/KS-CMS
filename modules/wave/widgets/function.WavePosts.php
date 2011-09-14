<?php
/**
 * @file function.WavePosts.php
 * Виджет выполняющий работу по отображению и добавлению комментариев.
 * Файл проекта kolos-cms.
 *
 * @since 27.10.2010
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.5.4-14
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
function smarty_function_WavePosts($params,&$subsmarty)
{
	global $global_template,$USER,$ks_db,$KS_MODULES,$KS_URL,$KS_EVENTS_HANDLER;
	$arData=array();
	/* Проверка общих прав на просмотр тем */
	$arData['level'] = $USER->GetLevel('wave');
	if ($arData['level'] > KS_ACCESS_WAVE_VIEW)
		throw new CAccessError("WAVE_ACCESS_VIEW", 403);
	if($params['hash']=='')
		throw new CDataError("WAVE_HASH_REQUIRED");
	if(array_key_exists('count',$params))
		$params['count']=intval($params['count'])>0?intval($params['count']):10;
	else
		$params['count']=10;
	if(array_key_exists('order',$params))
		$params['order']=$params['order']=='desc'?'desc':'asc';
	else
		$params['order']='asc';
	$obPages=new CPages($params['count']);
	$obPostsAPI=CWaveAPI::get_instance();
	$arPost=array();
	$subsmarty->assign('edit','N');
	try
	{
		if($arData['level']<KS_ACCESS_WAVE_VIEW)
		{
			$iAmount=1;
			if($_SERVER['REQUEST_METHOD']=='POST')
			{
				if(array_key_exists('addpost',$_POST))
					$action='addpost';
				if(array_key_exists('update',$_POST))
					$action='update';
				elseif(array_key_exists('edit',$_POST))
					$action='edit';
				elseif(array_key_exists('hide',$_POST))
					$action='hide';
				elseif(array_key_exists('show',$_POST))
					$action='show';
				elseif(array_key_exists('delete',$_POST))
					$action='delete';
				elseif(array_key_exists('votep',$_POST))
					$action='votep';
				elseif(array_key_exists('votem',$_POST))
					$action='votem';
			}
			else
			{
				if(in_array($_GET['WV_a'],array('addpost','hide','show','delete','edit','votep','votem')))
					$action=$_GET['WV_a'];
			}
			switch($action)
			{
				case 'addpost':
					//Операция по добавлению сообщения
					$bError=false;
					$arPost=array(
						'content'=>EscapeHTML($_REQUEST['WV_content']),
					);
					if(!$USER->IsLogin())
					{
						if($KS_MODULES->GetConfigVar('wave','use_captcha',0)==1)
						{
							if (!CCaptcha::CheckCaptcha($_POST['c']))
							{
								$bError=$KS_MODULES->AddNotify("WAVE_WRONG_CAPTCHA");
							}
						}
						$arPost['user_email']=EscapeHTML($_REQUEST['WV_user_email']);
						$arPost['user_name']=EscapeHTML($_REQUEST['WV_user_name']);
						$arPost['user_id']=-1;
						if(strlen($arPost['user_email'])>0 && !IsEmail($arPost['user_email']))
							$bError=$KS_MODULES->AddNotify("WAVE_MAIL_ERROR");
					}
					else
					{
						$arPost['user_email']=$USER->Email();
						$arUser=$USER->GetUserData();
						$arPost['user_name']=$_REQUEST['WV_user_name']==''?$arUser['title']:EscapeHTML($_POST['WV_user_name']);
						$arPost['user_id']=$USER->ID();
					}
					if(!$bError)
					{
						if($id=$obPostsAPI->AddAnswer($params['hash'],intval($_REQUEST['WV_parent_id']),$arPost))
						{
							if($arPost=$obPostsAPI->Posts()->GetById($id))
							{
								$url='';
								$arFilter=array('hash'=>$arPost['hash']);
								if($arData['level']>KS_ACCESS_WAVE_MODERATE)
									$arFilter['active']=1;
								if(is_array($params['filter']))
									$arFilter=array_merge($arFilter,$params['filter']);
								if($arList=$obPostsAPI->Posts()->GetList(array('left_margin'=>'asc'),$arFilter,false,array('id')))
								{
									$arPages=$obPages->SearchPage($id,array_keys($arList));
									$url=$KS_URL->GetPath().'?'.$KS_URL->GetUrl(array('path','i','p'.$arPages['index'])).'&i='.$arPages['index'].'&p'.$arPages['index'].'='.$arPages['active'].'#com'.$id;
								}
								//Событие на успешное добавление
								$arData=array('post'=>$arPost,'url'=>$url);
								$KS_EVENTS_HANDLER->Execute("wave", "onAddPost",$arData);
								if(array_key_exists('noredirect',$params) && $params['noredirect']=='Y') $url='';
								$KS_MODULES->AddNotify("WAVE_ADD_OK",'',NOTIFY_MESSAGE);
								$KS_URL->redirect($url);
							}
							else
							{
								throw new CError('WAVE_POST_NOT_FOUND');
							}
						}
						else
						{
							throw new CError('WAVE_ADD_ERROR');
						}
					}
					else
					{
						throw new CDataError('WAVE_FIELDS_ERROR');
					}
				break;
				case 'votem':
					$iAmount=-$iAmount;
				case 'votep':
					try
					{
						if(intval($_REQUEST['WV_id'])>0)
						{
							if($arPost=$obPostsAPI->Posts()->GetById(intval($_REQUEST['WV_id'])))
							{
								$iAmount=$obPostsAPI->VotePost($arPost['id'],$iAmount);
								if(array_key_exists('type',$_GET) && $_GET['type']=='AJAX')
								{
									$arResult=array('value'=>$iAmount,'id'=>$arPost['id']);
									echo json_encode($arResult);
									die();
								}
							}
							else
							{
								throw new CError('WAVE_POST_NOT_FOUND');
							}
						}
						else
						{
							throw new CError('WAVE_WRONG_URL');
						}
					}
					catch(CError $e)
					{
						if(array_key_exists('type',$_GET) && $_GET['type']=='AJAX')
						{
							$arResult=array('error'=>$KS_MODULES->GetErrorText($e->getMessage()));
							echo json_encode($arResult);
							die();
						}
						else
						{
							$KS_MODULES->AddNotify($e->getMessage());
						}
					}
				break;
				case 'update':
					$bError=false;
					$arPost=array(
						'content'=>EscapeHTML($_REQUEST['WV_content']),
					);
					$id=intval($_REQUEST['WV_id']);
					if(!$USER->IsLogin())
					{
						$bError=$KS_MODULES->AddNotify("WAVE_AUTH_REQUIRED");
					}
					$obPostsAPI->Posts()->Update($id,$arPost);
					$KS_MODULES->AddNotify("WAVE_UPDATE_OK",'',NOTIFY_MESSAGE);
					$KS_URL->redirect();
				break;
				case 'edit':
					if(array_key_exists('WV_id',$_REQUEST) && $arPost=$obPostsAPI->Posts()->GetById(intval($_REQUEST['WV_id'])))
					{
						$subsmarty->assign('post',$arPost);
						$subsmarty->assign('edit','Y');
					}
					else
					{
						$KS_MODULES->AddNotify('WAVE_POST_NOT_FOUND');
					}
				break;
				case 'hide':
					if($obPostsAPI->Hide(intval($_REQUEST['WV_id'])))
					{
						$KS_MODULES->AddNotify("WAVE_HIDE_OK",'',NOTIFY_MESSAGE);
					}
					$KS_URL->redirect();
				break;
				case 'show':
					if($obPostsAPI->Show(intval($_REQUEST['WV_id'])))
					{
						$KS_MODULES->AddNotify("WAVE_SHOW_OK",'',NOTIFY_MESSAGE);
					}
					$KS_URL->redirect();
				break;
				case 'delete':
					if($obPostsAPI->Delete(intval($_REQUEST['WV_id'])))
					{
						$KS_MODULES->AddNotify("WAVE_DELETE_OK",'',NOTIFY_MESSAGE);
					}
					$KS_URL->redirect();
				break;
			}
		}
	}
	catch(CDataError $e)
	{
		$KS_MODULES->AddNotify($e->getMessage());
		$subsmarty->assign('post',$arPost);
	}
	catch(CError $e)
	{
		$KS_MODULES->AddNotify($e->getMessage());
	}

	$arFilter=array();
	if($arData['level']>KS_ACCESS_WAVE_MODERATE)
		$arFilter['active']=1;

	if(array_key_exists('filter',$params) && is_array($params['filter']))
		$arFilter=array_merge($arFilter,$params['filter']);

	$arSelect=$obPostsAPI->Posts()->GetFields();
	$arSelect[$USER->sTable.'.title']='author_name';
	$arSelect[$USER->sTable.'.id']='author_id';
	$arFilter['<?user_id']=$USER->sTable.'.id';

	$arPosts=$obPostsAPI->GetWave($params['hash'],$params['order'],$arFilter,$arSelect,$obPages);
	if(!$USER->IsLogin())
	{
		if($KS_MODULES->GetConfigVar('wave','use_captcha',0)==1)
		{
			$subsmarty->assign('use_captcha',1);
		}
	}
	$subsmarty->assign('fields',$obPostsAPI->GetFormFields());
	$subsmarty->assign('posts',$arPosts);
	$subsmarty->assign('data',$arData);
	$subsmarty->assign('pages',$obPages->GetPages());
	//Код для генерации пути к шаблону или вывод ошибки об отсутсвтии шаблона
	return $KS_MODULES->RenderTemplate($subsmarty,'/wave/WavePosts',$params['global_template'],$params['tpl']);
}

function widget_params_WavePosts()
{
	$arFields=array(
		'count'=>array(
			'title'=>'Количество выводимых комментариев',
			'type'=>'text',
			'value'=>'10',
		),
		'order'=>array(
			'title'=>'Порядок вывода',
			'type'=>'select',
			'value'=>array('asc'=>'Новые снизу','desc'=>'Новые сверху'),
		),
		'filter'=>array(
			'title'=>'Имя фильтра',
			'type'=>'text',
			'value'=>'',
		),
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