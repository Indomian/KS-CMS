<?php
/**
 * Создан 04.02.2010
 *
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

function smarty_function_Subscribe($params,&$subsmarty)
{

	global $KS_MODULES,  $USER,$global_template;
	$module_name='subscribe';
	include_once MODULES_DIR . "/subscribe/libs/class.Subscribe.php";
	include_once MODULES_DIR.'/main/libs/class.CMessage.php';
		 
 	$access_level = $USER->GetLevel($module_name);
	if ($access_level > 9) throw new CAccessError('SUBSCRIBE_ACCESS_DENIED'); 
	/* Получаем массив групп, к которым принадлежит пользователь */
	
		
	
	$access_groups = $USER->GetGroups();
	
	$obNews = new CNewsletters();
	
	$obSubsUsergroupsLevels= new CNewsletterUsergroupsLevels();
	$obSubscribe = new CSubscribe();
	$obUsers = new CSubUsers();
	$newsletters=$obNews->GetList(array('name'=>'asc'), array('active'=>1));
	$arData = $_POST;
	
	
	$q = $newsletters;
	
 	for ($i = 0; $i < count($q); $i++)
	{	
		
		$cur_news_access = $obSubsUsergroupsLevels->GetList(array('usergroup_id' => "asc"), array('newsletter_id' => $newsletters[$i]['id'], '->usergroup_id' => "(" . implode(", ", $access_groups) . ")"));
		$news_access_level = 10;
		if (is_array($cur_news_access) && count($cur_news_access))
			foreach ($cur_news_access as $key => $cur_news_group_access)
				if ($cur_news_group_access['level'] < $news_access_level)
					$news_access_level = $cur_news_group_access['level'];
					
		

		/* Уничтожение не прошедших отбор опросов */
		$newsletters[$i]['selectable']=true;
		if ($news_access_level > 8)
			$newsletters[$i]['selectable']=false;
		if ($news_access_level > 9)
			unset($newsletters[$i]);
		
	}
	
	$first=true;
	if($USER->ID())
	{
		
		$userdata=$obSubscribe->GetRecord(array('uin'=>$USER->ID()));
		
		if(is_array($userdata))
		{
			$first=false;
			$arData['id']=$userdata['id'];
			$user_news=$obUsers->GetList(array('name'=>'asc'), array('uin'=>$userdata['id']));
		
			foreach($newsletters as $key=>$news)
			{
				$newsletters[$key]['select']=false;
				foreach($user_news as $unews)
				{
					if($news['id']==$unews['newsletter'])
					{
						$newsletters[$key]['select']=true;
					}
				}
			}
		}
		
		
	}
	elseif($_COOKIE['sid'])
	{
		$userdata=$obSubscribe->GetRecord(array('id'=>$_COOKIE['sid']));
		if(is_array($userdata))
		{
			$first=false;
			$arData['id']=$userdata['id'];
			$user_news=$obUsers->GetList(array('name'=>'asc'), array('uin'=>$userdata['id']));
		
			foreach($newsletters as $key=>$news)
			{
				$newsletters[$key]['select']=false;
				foreach($user_news as $unews)
				{
					if($news['id']==$unews['newsletter'])
					{
						$newsletters[$key]['select']=true;
					}
				}
			}
		}
	}
	try
	{
	
	if (count($newsletters) > 0)
	{
		
		if(is_array($arData['newsletters']))
		{
			
				foreach($newsletters as $news)
				{
					if($news['selectable']==false && ($key = array_search($news,$arData['newsletters']))!==false)
					{
						unset($arData['newsletters'][$key]);
					}
				}	
				if (!ereg("^([a-z0-9_.\-]+)(@)([a-z0-9_.\-]+)((\.[a-z0-8_-]+)+)$", $arData['email']))
					throw new CError("SUBSCRIBE_MAIL_ERROR", 0, '"'.$arData['email'].'"');
				
				$obSubscribe->AddCheckField('email');
				$obSubscribe->AddAutoField('id');
				$arData['date_add']=time();
				$arData['active']=($userdata['active']?$userdata['active']:0);
				$arData['uin']=($USER->ID()?$USER->ID():-1);
				
			
				
					
				$id = $obSubscribe->Save('', $arData);
				setcookie('sid',$id, time() + 2592000, '/');
				$obUsers->Save($id,$arData['newsletters']);
				$userdata=$obSubscribe->GetRecord(array('uin'=>$USER->ID()));
				if($first)
				{
					$userdata['activated']=$first;
					$obEmail=new CEmailMessage();
					$data['code'] = md5($userdata['id'] . $userdata['email']);
					
					$obEmail->AddTemplate($userdata['email'],$data,'subscribe.activate.tpl','text/html');
				}
		}
		elseif($_GET['code']!='')
		{
					
					if($_GET['code']==md5($userdata['id'] . $userdata['email']))
					{
						$arData['active']=1;
						$arData['date_active']=time();
						$arData['uin']=($USER->ID()?$USER->ID():-1);
						$id = $obSubscribe->Save('', $arData);
						$userdata['finish']=true;
					}
		}
			
				
			
		
	}

	}
	catch (CError $e)
	{
	/* Возвращаем результат */
		
	$subsmarty->assign("data", $userdata);
	$subsmarty->assign("newsletters", $newsletters);
	$sResult=$KS_MODULES->RenderTemplate($subsmarty,'/subscribe/Subscribe',$params['global_template'],$params['tpl']);
	return $e.$sResult;
	}
	$subsmarty->assign("data", $userdata);
	$subsmarty->assign("newsletters", $newsletters);
	$sResult=$KS_MODULES->RenderTemplate($subsmarty,'/subscribe/Subscribe',$params['global_template'],$params['tpl']);
	return $sResult;
}
	
function widget_params_Subscribe()
{
	$arFields=array();
	return array(
	  'fields'=>$arFields,
	 );
}
?>
