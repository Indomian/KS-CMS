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
	global $global_template,$USER,$ks_db,$KS_MODULES,$KS_URL;
	$arData=array();
	/* Проверка общих прав на просмотр тем */
	$arData['level'] = $USER->GetLevel('wave');
	if ($arData['level'] > KS_ACCESS_WAVE_VIEW) throw new CAccessError("WAVE_ACCESS_VIEW", 403);
	if($params['hash']=='') throw new CDataError("WAVE_HASH_REQUIRED");
	$params['count']=intval($params['count'])>0?intval($params['count']):10;
	$params['order']=$params['order']=='desc'?'desc':'asc';
	$obPostsAPI=CWaveAPI::get_instance();
	$arPost=array();
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
						'content'=>EscapeHTML($_POST['WV_content']),
					);
					if(!$USER->IsLogin())
					{
						$arPost['user_email']=EscapeHTML($_POST['WV_user_email']);
						$arPost['user_name']=EscapeHTML($_POST['WV_user_name']);
						$arPost['user_id']=-1;
						if($arPost['user_name']=='')
							$bError=$KS_MODULES->AddNotify("WV_NAME_ERROR");
						if(strlen($arPost['user_email'])>0 && !IsEmail($arPost['user_email']))
							$bError=$KS_MODULES->AddNotify("WV_MAIL_ERROR");
					}
					else
					{
						$arPost['user_email']=$USER->Email();
						$arPost['user_name']=$_POST['WV_user_name']==''?$USER->userdata['title']:EscapeHTML($_POST['WV_user_name']);
						$arPost['user_id']=$USER->ID();
					}
					if($arPost['content']=='')
						$bError=$KS_MODULES->AddNotify("WAVE_TEXT_ERROR");
					if(!$bError)
					{
						$id=$obPostsAPI->AddAnswer($params['hash'],intval($_POST['parent_id']),$arPost);
						if($arData['level']>=KS_ACCESS_WAVE_ADD_GUEST)
						{
							$KS_MODULES->AddNotify("WAVE_ADD_OK",'',NOTIFY_MESSAGE);
						}
					}
					else
					{
						throw new CDataError('WAVE_FIELDS_ERROR');
					}
				}
				elseif(array_key_exists('hide',$_POST))
				{
					$obPosts->Hide(intval($_POST['WV_id']));
				}
				elseif(array_key_exists('show',$_POST))
				{
					$obPosts->Show(intval($_POST['WV_id']));
				}
				elseif(array_key_exists('delete',$_POST))
				{
					$obPosts->Delete(intval($_POST['WV_id']));
				}
			}
			else
			{
				throw new CAccessError('WAVE_ACCESS_POST');
			}
		}
	}
	catch(CDataError $e)
	{
		$res=$e->__toString();
		$subsmarty->assign('post',$arPost);
	}
	catch(CError $e)
	{
		$res=$e->__toString();
	}

	$obPosts=$obPostsAPI->Posts();
	//Получаем список сообщений
	$arFilter=array(
		'hash'=>$params['hash'],
		'<?'.$obPosts->sTable.'.user_id'=>$USER->sTable.'.id',
	);
	if($arData['level']>KS_ACCESS_WAVE_MODERATE)
		$arFilter['active']=1;
	if(is_array($params['filter']))
		$arFilter=array_merge($arFilter,$params['filter']);
	$arSelect=$obPosts->arFields;
	foreach($USER->arFields as $sField)
		$arSelect[]=$USER->sTable.'.'.$sField;
	$obPages = new CPageNavigation($obPosts,false,$params['count']);
	$arOrder=array('left_margin'=>$params['order'],'date_add'=>$params['order']);
	$iCount=$obPosts->Count($arFilter);
	$arPosts=$obPosts->GetList($arOrder,$arFilter,$obPages->GetLimits($iCount),$arSelect);
	$subsmarty->assign('posts',$arPosts);
	$subsmarty->assign('data',$arData);
	$subsmarty->assign('pages',$obPages->GetPages());
	//Код для генерации пути к шаблону или вывод ошибки об отсутсвтии шаблона
	return $res.$KS_MODULES->RenderTemplate($subsmarty,'/wave/WavePosts',$params['global_template'],$params['tpl']);
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
?>