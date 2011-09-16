<?php
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CUserGroup.php';

global $KS_URL, $USER;
$module_name='subscribe';
/* Чтение списка допустимых действий */	
$acceptable_actions = $KS_MODULES->GetConfigVar($module_name, "acceptable_actions");

/* Определение действия */
$action = "";
if (isset($_REQUEST["ACTION"]))
	if (in_array($_REQUEST["ACTION"], $acceptable_actions))
		$action = $_REQUEST["ACTION"];

$obReleases = new CReleases();
$obPages = new CPageNavigation($obReleases);
$obGroups=new CUserGroup();
$obNews = new CNewsletters();
$obMailSend = new CEmails();
$obUser= new CUser();
$obSubscribe = new CSubscribe();
	

$page='_releases';

if ($action === "new" || $action === "edit" || $action === "update")
	$page .= "_edit";		// Создание или редактирования элемента (формы или поля формы)

if(!$action)
{
	/* Поля, по которым можно отсортировать */
	$arSortFields = array("id", "theme", "date_add","send", "from");
	
	/* Определяем поле для сортировки */
	$sOrderField = (in_array($_REQUEST['order'], $arSortFields)) ? $_REQUEST['order'] : "id";
	
	/* Определяем направление сортировки (текущее и будущее) */
	if ($_REQUEST['dir'] == "asc"):
		$sOrderDir = "asc";
		$sNewDir = "desc";
	else:
		$sOrderDir = "desc";
		$sNewDir = "asc";
	endif;
	
	/* Параметры сортировки для выборки списка */
	$arOrder = array($sOrderField => $sOrderDir);
	
	$totalNews = $obReleases->count();
	
	/* Для постраничной навигации */
	$smarty->assign("pages", $obPages->GetPages($totalNews));
	/* Количество отображаемых на странице строк */
	$smarty->assign("num_visible", $obPages->GetVisible());
	
	/* Параметры сортировки */
	$smarty->assign("order", array('newdir' => $sNewDir, 'curdir' => $sOrderDir, 'field' => $sOrderField));
	
	$listNews=$obReleases->GetNewslettersList(array('id'=>'asc'));
	
	$list=$obReleases->GetList($arOrder, $arFilter, $obPages->GetLimits($totalNews));
	if(is_array($list))
	foreach($list as $key=>$item)
	{
		if($list[$key]['newsletter']>=0)
		{
			foreach($listNews as $itemNews)
			{
				if($list[$key]['newsletter']==$itemNews['id'])
					$list[$key]['newsletter']=$itemNews['name'];
			}
			if((int)($list[$key]['newsletter']))
				$list[$key]['newsletter']=false;
		}	
		else
		$list[$key]['newsletter']=false;	
	}
	
	$smarty->assign("list", $list);
	
	}
else
{
	if($USER->GetLevel('subscribe')>5) throw new CAccessError('SUBSCRIBE_NOT_ACCESS_RELEASES');
	switch($action)
	{
		case "common":
			$request_ids = array();
			$input_array = $_POST;
			if (count($input_array))
				foreach ($input_array as $variable => $value)
					if (preg_match("#^common_([0-9]+)$#", $variable, $subpatterns))
						$request_ids[] = intval($subpatterns[1]);
			
			if (count($request_ids) > 0)
			{
				if (isset($_REQUEST['comdel']))
				{
					/* Удаление*/
					foreach ($request_ids as $id)
					{
						$obReleases->Delete($id);
					}
				}
				elseif (isset($_REQUEST['comact']))
				{
					/* Активация */
					foreach ($request_ids as $id)
						$obReleases->Update($id, array('active' => "1"));
				}
				elseif (isset($_REQUEST['comdea']))
				{
					/* Деактивация */
					foreach ($request_ids as $id)
						$obReleases->Update($id, array('active' => "0"));
				}
			}
			
			/* Возвращаемся к списку */
			CUrlParser::Redirect("admin.php?" . $KS_URL->GetUrl(array()));
		break;	
		/* Новое */
		case "new":
			$data['id']=-1;
			$data['newsletter']=-1;
			$data['newsletters']=$obReleases->GetNewslettersList();
			$data['encryption']=$KS_MODULES->GetConfigVar($module_name, "encryption");
			$data['from']=$KS_MODULES->GetConfigVar($module_name, "from");
			$data['groupslist']=$obGroups->GetList();
			
			$smarty->assign("data", $data);
		break;
		
		/* Редактирование */
		case "edit":
			/* Идентификатор */
			$id = intval($_REQUEST['id']);
			
			$data = $obReleases->GetRecord(array('id' => $id));
			if(!$obNews->GetRecord(array('id' => $data['newsletter'])))
			{
				$data['newsletter']=-1;
			}
			$newsletters=$obReleases->GetNewslettersList();
			$groupslist=$obGroups->GetList();
			
			if($groupslist && $data['groups'])
			foreach($groupslist as $key=>$elm)
			{
				foreach($data['groups'] as $group)
				{
					if($elm['id']==$group)
						$groupslist[$key]['select']=true;
				}
			}
			$data['groupslist']=$groupslist;
			if($newsletters && $data['newsletters'])
			foreach($newsletters as $key=>$elm)
			{
				foreach($data['newsletters'] as $newsletter)
				{
					if($elm['id']==$newsletter)
						$newsletters[$key]['select']=true;
				}
			}
			$data['newsletters']=$newsletters;
			$smarty->assign("data", $data);
			//$smarty->assign("access", $access);
		break;
		
		/* Сохранение */
		case "save":
			/* Идентификатор */
		
			/* Параметры для сохранения */
			$arData = $_POST;
			/* Попытка сохранения данных */
			try
			{
				
				/* Поле для автозаполнения */
				$obReleases->AddAutoField('id');
				
				if (strlen(trim($arData['SB_theme'])) == 0 || strlen(trim($arData['SB_theme']))>50)
					throw new CError("SUBSCRIBE_THEME_ERROR",0);
				if (strlen(trim($arData['SB_content'])) == 0)
					throw new CError("SUBSCRIBE_CONTENT_ERROR",0);
				if (strlen(trim($arData['SB_from'])) == 0)
					throw new CError("SUBSCRIBE_FROM_FIELD_ERROR",0);
				if (!preg_match("/^((\w)*(\s)){0,2}([a-z0-9_.\-]+)(@)([a-z0-9_.\-]+)((\.[a-z0-8_-]+)+)$/", $arData['SB_from']))
					throw new CError("SUBSCRIBE_MAIL_ERROR", 0, '"'.$arData['SB_from'].'"');
				if (!ereg("^(\w)*(\s)*(\w)*(\s)*([a-z0-9_.\-]+)(@)([a-z0-9_.\-]+)((\.[a-z0-8_-]+)+)$", $arData['SB_to']) && $arData['SB_to'])
					throw new CError("SUBSCRIBE_MAIL_ERROR", 0, '"'.$arData['SB_to'].'"');		
					
				
				/* Сохранение записи */
				$id = $obReleases->Save('SB_', $arData);
				
				/* Отправка */
				if (array_key_exists('send', $_REQUEST))
				{
					$newData=$obReleases->GetRecord(array('id' => $id));
					
					if($newData['newsletter']!=-1)
					{
						$newData['emails']=$obSubscribe->GetEmailByNewsletter($newData['newsletter']);
						$obMailSend->send($newData);
					}
					elseif(is_array($newData['groups']))
					{
						$newData['emails']=$obSubscribe->GetEmailByGroup($newData['groups']);
						$obMailSend->send($newData);
					}
					elseif(is_array($newData['newsletters']) && is_array($newData['list']))
					{
						$newData['list']=array_unique($newData['list']);
						
						
						$newData['newsletters']=$obSubscribe->GetEmailByNewsletters($newData['newsletters']);
						foreach($newData['newsletters'] as $newsletter)
						{
					
							if(($key = array_search(trim($newsletter['email']),$newData['list']))!==false)
							{
							
								unset($newData['list'][$key]);
							}
						}
						foreach($newData['list'] as $mail)
						{
							$mailList[]=array('email'=>$mail);
						}
						$newData['emails']=array_merge($newData['newsletters'],$mailList);
						$obMailSend->send($newData);
					}
					elseif(is_array($newData['newsletters']))
					{
						$newData['emails']=$obSubscribe->GetEmailByNewsletters($newData['newsletters']);
						$obMailSend->send($newData);
					}
					elseif(is_array($newData['list']))
					{
						
						foreach($newData['list'] as $mail)
						{
							$newData['emails'][]=array('email'=>$mail);
						}
						$obMailSend->send($newData);
					}
				$arData['SB_send']=1;
				$id = $obReleases->Save('SB_', $arData);
				}
				
				/* Осуществляем редирект после успешного сохранения */
				if (array_key_exists('update', $_REQUEST))
					CUrlParser::Redirect("admin.php?".$KS_URL->GetUrl(array('ACTION')).'&ACTION=edit&id='.$id);
					//CUrlParser::Redirect("admin.php?".$KS_URL->GetUrl('ACTION','CSC_id','CSC_catid').'&ACTION=edit'.$sAdd);
				else
					CUrlParser::Redirect("admin.php?".$KS_URL->GetUrl(array('ACTION','p')));
				
				
			}
			catch(CError $e)
			{
				
				$data=$obReleases->GetRecordFromPost('SB_',$_POST);
				$data['newsletters']=$obReleases->GetNewslettersList();
				$data['groupslist']=$obGroups->GetList();
				$smarty->assign('last_error', $e);
				$smarty->assign('data',$data);
				$page.='_edit';
			
			}
			
			
		break;
				
		/* Удаление */
		case "delete":
			/* Идентификатор голосования */
			$id = intval($_REQUEST['id']);
			
			
			$obReleases->Delete($id);
			
			/* В случае успеха (или не успеха - как повезёт) делаем редирект */
			CUrlParser::Redirect("admin.php?" . $KS_URL->GetUrl(array("ACTION", "id")));
		break;
		
		default:
		break;		
	}

}
?>