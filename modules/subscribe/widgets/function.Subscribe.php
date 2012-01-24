<?php
/**
 * В файле находится виджет обеспечивающий вывод формы оформления подписки на рассылку
 * @since 04.02.2010
 * @author fox <fox@kolosstudio.ru>, blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/subscribe/libs/class.CSubscribeAPI.php';

function smarty_function_Subscribe($params,&$subsmarty)
{
	global $KS_MODULES,  $USER, $global_template;
 
	$access_level = $USER->GetLevel('subscribe');
	if ($access_level > 9) throw new CAccessError('SUBSCRIBE_ACCESS_DENIED'); 
	/* Получаем массив групп, к которым принадлежит пользователь */
	$arUsergroups = $USER->GetGroups();
	$obAPI=CSubscribeAPI::get_instance();
	if($arTmpNewsletters=$obAPI->Newsletter()->GetList(array('name'=>'asc'), array('active'=>1)))
	{
		$arNewsletters=array();
		//Выберем уровни доступа к рассылкам для указанных групп пользователей
		$arFilter=array(
			'->newsletter_id'=>array_keys($arTmpNewsletters),
			'->usergroup_id'=>$arUsergroups
		);
		if($arAccessTmp=$obAPI->Access()->GetList(false,$arFilter))
		{
			$arAccess=array();
			foreach($arAccessTmp as $arItem)
			{
				if(!array_key_exists($arItem['newsletter_id'],$arAccess))
					$arAccess[$arItem['newsletter_id']]=10;
				if($arItem['level']<$arAccess[$arItem['newsletter_id']])
					$arAccess[$arItem['newsletter_id']]=$arItem['level'];
			}
			foreach($arTmpNewsletters as $arItem)
			{
				/* Уничтожение не прошедших отбор опросов */
				$arItem['selectable']=true;
				if ($arAccess[$arItem['id']]==9)
					$arItem['selectable']=false;
				elseif ($arAccess[$arItem['id']]>9) continue;
				$arNewsletters[$arItem['id']]=$arItem;
			}
		}

		if(count($arNewsletters)>0)
		{
			$bFirst=true;
			$arUser=false;
			if($USER->ID())
				$arUser=$obAPI->SubscribeUsers()->GetRecord(array('uin'=>$USER->ID()));
			elseif(isset($_COOKIE['sid']))
				$arUser=$obAPI->SubscribeUsers()->GetRecord(array('id'=>$_COOKIE['sid']));
			if($arUser)
			{
				$bFirst=false;
				$arUserNewsletters=array();
				if($arUserSubscribes=$obAPI->Subscribers()->GetList(array('name'=>'asc'), array('uin'=>$arUser['id'])))
					foreach($arUserSubscribes as $arItem)
						$arUserNewsletters[]=$arItem['newsletter'];
				foreach($arNewsletters as $key=>$arItem)
					$arNewsletters[$key]['select']=in_array($arItem['id'],$arUserNewsletters);
			}
			try
			{
				if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['subscribe']))
				{
					$arData=array();
					$arData['newsletters']=array();
					foreach($arNewsletters as $arItem)
						if($arItem['selectable']&& isset($_POST['newsletters']) && in_array($arItem['id'],$_POST['newsletters']))
							$arData['newsletters'][]=$arItem['id'];
					if(!IsEmail($_POST['email']))
						throw new CDataError("SUBSCRIBE_MAIL_ERROR", 0, '"'.$_POST['email'].'"');
					else
						$arData['email']=$_POST['email'];
					if($arUser)
					{
						$arData['id']=$arUser['id'];
						$arData['active']=$arUser['active'];
						$arData['uin']=$arUser['uin'];
					}
					else
					{
						$arData['date_add']=time();
						$arData['active']=0;
						$arData['uin']=($USER->ID()?$USER->ID():-1);
					}
					if($id=$obAPI->SaveSubscribe('',$arData))
					{
						setcookie('sid',$id, time() + 2592000, '/');
						$obAPI->Subscribers()->SaveEx($id,$arData['newsletters']);
						if($arUser=$obAPI->SubscribeUsers()->GetRecord(array('uin'=>$USER->ID())) && $bFirst)
						{
							$arUser['activated']=$first;
							$obEmail=new CEmailMessage();
							$data=array(
								'code'=>$obAPI->GenSubscribeCode($arUser)
							);
							$obAPI->SubscribeUsers()->Update($arUser['id'],array('code'=>$data['code']));
							$obEmail->AddTemplate($arUser['email'],$data,'subscribe.activate.tpl','text/html');
						}
						CUrlParser::get_instance()->Redirect();
					}
					else
						throw new CError("SUBSCRIBE_SUBSCRIBE_ERROR");
				}
				elseif(isset($_GET['code']) && $_GET['code']!='')
				{
					if(IsTextIdent($_GET['code']) && $arUser=$obAPI->SubscribeUsers()->GetRecord(array('code'=>$_GET['code'])))
					{
						$arData=array(
							'active'=>1,
							'date_active'=>time(),
							'code'=>''
						);
						if($arUser['uin']==0)
							$arData['uin']=($USER->ID()?$USER->ID():0);
						$obAPI->SubscribeUsers()->Update($arUser['id'],$arData);
						$arUser['finish']=true;
					}
				}
			}
			catch (CError $e)
			{
				/* Возвращаем результат */
				$subsmarty->assign("data", $arUser);
				$subsmarty->assign("newsletters", $arNewsletters);
				$sResult=$KS_MODULES->RenderTemplate($subsmarty,'/subscribe/Subscribe',$params['global_template'],$params['tpl']);
				return $e->__toString().$sResult;
			}
			$subsmarty->assign("data", $arUser);
			$subsmarty->assign("newsletters", $arNewsletters);
		}
		else
			$subsmarty->assign('error',1);
	}
	else
		$subsmarty->assign('error',2);
	return $KS_MODULES->RenderTemplate($subsmarty,'/subscribe/Subscribe',$params['global_template'],$params['tpl']);
}
	
function widget_params_Subscribe()
{
	$arFields=array();
	return array(
	  'fields'=>$arFields,
	 );
}