<?php
/**
 * @file function.GB2Posts.php
 * Виджет выполняющий работу по отображению и добавлению сообщений гостевой книги.
 * Файл проекта kolos-cms.
 * 
 * Создан: 24.11.2008
 * Изменен: 13.09.2010
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.5.4-13
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}
include_once MODULES_DIR.'/guestbook2/libs/class.CGB2Api.php';
include_once MODULES_DIR.'/interfaces/libs/CInterface.php';

/**
 * Функция производит вывод сообщение гостевой книги
 * @param $params массив параметров.
 * @param $subsmarty - указатель на объект смарти.
 * Параметры могут быть следующими:
 * 	count - количество выводимых элементов;
 *  id - номер записи для вывода
 *  filter - массив для фильтрации выводимых записей;
 */
function smarty_function_GB2Posts($params,&$subsmarty)
{
	global $global_template,$USER,$ks_db,$KS_MODULES,$KS_URL;
	//Проверка и инициализация аякса
	if($params['isAjax']=='Y') 
	{
		/*Ключ о том это аякс запрос или нет*/
		$oldAjax=false;
		$obAjax=new CAjax('GB2Posts',$params);
		if(array_key_exists('ajaxMode',$_REQUEST))
		{
			if($obAjax->CheckHash($_REQUEST['ajaxMode']))
			{
				$oldAjax=true;
				ob_clean();
			}
			else
				return '';
		}
	}
	$arData=array();
	/* Проверка общих прав на просмотр тем */
	$arData['level'] = $USER->GetLevel('guestbook2');
	if ($arData['level'] > KS_ACCESS_GB2_VIEW)
		throw new CAccessError("GB2_ACCESS_VIEW", 403);
	$params['count']=intval($params['count'])>0?intval($params['count']):10;
	$arData['showCaptcha']=$KS_MODULES->GetConfigVar('guestbook2','use_captcha');
	$obGB2=CGB2API::get_instance();
	//Получаем список категорий
	if($arTmpCategories=$obGB2->GetCategories())
	{
		foreach($arTmpCategories as $arRow)
		{
			$arCategories[$arRow['id']]=$arRow;
		}
	}
	$arPost=array();
	try
	{
		if($_SERVER['REQUEST_METHOD']=='POST' && $KS_URL->CheckPostHash())
		{
			if($arData['level']<KS_ACCESS_GB2_VIEW)
			{
				if(array_key_exists('addpost',$_POST))
				{
					//Операция по добавлению сообщения
					$arPost=array();
					if($arData['level']==KS_ACCESS_GB2_ANSWER_GUEST)
					{
						$arPost['active']=0;
					}
					$arPost['title']=$_POST['GB_title'];
					$arPost['content']=$_POST['GB_content'];
					if(array_key_exists(intval($_POST['GB_cat']),$arCategories))
						$arPost['category_id']=intval($_POST['GB_cat']);
					else
						$arPost['category_id']=0;
					
					if(!$USER->IsLogin())
					{
						$arPost['user_email']=$_POST['GB_user_email'];
						$arPost['user_name']=$_POST['GB_user_name'];
						$arPost['user_id']=-1;
						
						if($arPost['user_name']=='')
							throw new CDataError("GB2_NAME_ERROR");
						if (!ereg("^([a-z0-9_.\-]+)(@)([a-z0-9_.\-]+)((\.[a-z0-8_-]+)+)$", $arPost['user_email']))
							throw new CDataError("GB2_MAIL_ERROR");
					
					}
					else
					{
						$arPost['user_email']=$USER->Email();
						$arPost['user_name']=$USER->userdata['title'];
						$arPost['user_id']=$USER->ID();
						
					}
					if($arPost['content']=='')
						throw new CDataError("GB2_TEXT_ERROR");
					
					if($arData['showCaptcha']==1)
					{
						if (!CCaptcha::CheckCaptcha($_POST['c']))
							throw new CDataError("USER_CAP_ERROR");			
					}
				
					$id=$obGB2->AddPost($arPost);
					if($arData['level']==KS_ACCESS_GB2_ANSWER_GUEST)
					{
						throw new CError("GB2_ADD_OK");
					}
				}
				elseif(array_key_exists('hide',$_POST))
				{
					$obGB2->HidePost(intval($_POST['GB_id']));
				}
				elseif(array_key_exists('show',$_POST))
				{
					$obGB2->ShowPost(intval($_POST['GB_id']));
				}
				elseif(array_key_exists('delete',$_POST))
				{
					$obGB2->DeletePost(intval($_POST['GB_id']));
					unset($_POST['GB_id']);
				}
			}
			else
			{
				throw new CAccessError('GB2_ACCESS_POST');
			}
		}
	}
	catch(CDataError $e)
	{
		$res=$e;
		$subsmarty->assign('post',$arPost);
	}
	catch(CError $e)
	{
		$res=$e;
	}
	
	//Получаем список сообщений
	$arFilter=array();
	if($arData['level']>KS_ACCESS_GB2_REPLY)
		$arFilter['active']=1;
	if(isset($params['id']))
		$arFilter['id']=$params['id'];
	if(is_array($params['filter']))
		$arFilter=array_merge($arFilter,$params['filter']);
	$obPages = new CPageNavigation($obGB2->obPosts,false,$params['count']);
	$arOrder=array('date_shown'=>'desc');
	$arPosts=$obGB2->GetPosts($arOrder,$arFilter,$obPages);
	
	$subsmarty->assign('posts',$arPosts);
	$subsmarty->assign('categories',$arCategories);
	$subsmarty->assign('currentCat',$arFilter['category_id']);
	$subsmarty->assign('pages',$obPages->GetPages());
	$subsmarty->assign('data',$arData);
	//Код для генерации пути к шаблону или вывод ошибки об отсутсвтии шаблона
	$sResult=$res.$KS_MODULES->RenderTemplate($subsmarty,'/guestbook2/GB2Posts',$params['global_template'],$params['tpl']);
	if($params['isAjax']=='Y') $sResult=$obAjax->GetCode($sResult,$oldAjax);
	if($oldAjax)
	{
		echo $sResult;
		die();
	}
	return $sResult;		
}

function widget_params_GB2Posts()
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
		'id'=>array(
			'title'=>'Номер выводимой записи',
			'type'=>'text',
			'value'=>''
		),
	);
	return array(
		'fields'=>$arFields,
	);
}
?>