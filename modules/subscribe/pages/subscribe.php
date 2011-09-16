<?php
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

global $KS_URL, $USER;
$module_name='subscribe';
/* Чтение списка допустимых действий */	
$acceptable_actions = $KS_MODULES->GetConfigVar($module_name, "acceptable_actions");

/* Определение действия */
$action = "";
if (isset($_REQUEST["ACTION"]))
	if (in_array($_REQUEST["ACTION"], $acceptable_actions))
		$action = $_REQUEST["ACTION"];

$obSubscribe = new CSubscribe();
$obReleases = new CReleases();
$obNews = new CNewsletters();
$obUsers = new CSubUsers();
$obPages = new CPageNavigation($obSubscribe);
$page='_subscribe';

if ($action === "new" || $action === "edit" || $action === "update")
	$page .= "_edit";		// Создание или редактирования элемента (формы или поля формы)

if(!$action)
{
	/* Поля, по которым можно отсортировать */
	$arSortFields = array("id", "email", "date_add", "date_active","active");
	
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
	
	$totalNews = $obSubscribe->count();
	
	/* Для постраничной навигации */
	$smarty->assign("pages", $obPages->GetPages($totalNews));
	/* Количество отображаемых на странице строк*/
	$smarty->assign("num_visible", $obPages->GetVisible());
	
	/* Параметры сортировки */
	$smarty->assign("order", array('newdir' => $sNewDir, 'curdir' => $sOrderDir, 'field' => $sOrderField));
	
	$listUsers=$obSubscribe->GetUsers(array('id'=>'asc'));
	
	$listNews=$obReleases->GetNewslettersList(array('id'=>'asc'));
		
	$list=$obSubscribe->GetList($arOrder, $arFilter, $obPages->GetLimits($totalNews));
	if($list)
	foreach($list as $key=>$item)
	{
		if($list[$key]['uin']>=0)
		{
			foreach($listUsers as $itemUsers)
			{
				if($list[$key]['uin']==$itemUsers['id'])
					$list[$key]['uin']=$itemUsers['title'];
			}
			if((int)($list[$key]['uin']))
			{
				$list[$key]['uin']=false;
			}
		}	
		else
		$list[$key]['uin']=false;
		
	}
	
	$smarty->assign("list", $list);
	
	}
else
{
	if($USER->GetLevel('subscribe')>1) throw new CAccessError('SUBSCRIBE_NOT_ACCESS_USERS');
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
						$obSubscribe->Delete($id);
						$obUsers->DeleteItems(array('uin'=>$id));
					}
				}
				elseif (isset($_REQUEST['comact']))
				{
					/* Активация */
					foreach ($request_ids as $id)
						$obSubscribe->Update($id, array('active' => "1"));
				}
				elseif (isset($_REQUEST['comdea']))
				{
					/* Деактивация */
					foreach ($request_ids as $id)
						$obSubscribe->Update($id, array('active' => "0"));
				}
			}
			
			/* Возвращаемся к списку  */
			CUrlParser::Redirect("admin.php?" . $KS_URL->GetUrl(array()));
		break;	
		/* Новое */
		case "new":
			$data['id']=-1;
			$data['uin']=-1;
		
			$data['newsletters']=$obNews->GetList(array('name'=>'asc'), $arFilter);
			$data['format']=$KS_MODULES->GetConfigVar($module_name, "format");
		
			$smarty->assign("data", $data);
		break;
		
		/* Редактирование */
		case "edit":
			/* Идентификатор */
			$id = intval($_REQUEST['id']);
			$data = $obSubscribe->GetRecord(array('id' => $id));
			$data['users']=$obSubscribe->GetUsers();
			$data['newsletters']=$obNews->GetList(array('name'=>'asc'), $arFilter);
			$user_news=$obUsers->GetList(array('name'=>'asc'), array('uin'=>$id));
			if($data['newsletters'])
			foreach($data['newsletters'] as $key=>$item)
			{
				$data['newsletters'][$key]['select']=false;
				if($user_news)
				foreach($user_news as $itemUsers)
				{
					if($item['id']==$itemUsers['newsletter'])
					{
						$data['newsletters'][$key]['select']=true;
					}
				}
			
			}
			$users=$obSubscribe->GetList();
			foreach($data['users'] as $key=>$item)
			{
				foreach($users as $itemUsers)
				{
					if($item['id']==$itemUsers['uin'] && $data['uin']!=$item['id'])
					{
						unset($data['users'][$key]);	
					}
				}
			}
			$smarty->assign("data", $data);
	
		break;
		
		/* Сохранение */
		case "save":
			/* Идентификатор */
			
			$id = intval($_REQUEST['id']);
			/* Параметры для сохранения */
			$arData = $_POST;
			
			/* Попытка сохранения данных */
			try
			{
				/* Поле для автозаполнения */
				if (!ereg("^([a-z0-9_.\-]+)(@)([a-z0-9_.\-]+)((\.[a-z0-8_-]+)+)$", $arData['SB_email']))
					throw new CError("SUBSCRIBE_MAIL_ERROR", 0, '"'.$arData['SB_email'].'"');
				
				$obSubscribe->AddCheckField('email');
				$obSubscribe->AddAutoField('id');
				$arData['SB_date_add']=time();
				
				if($arData['SB_date_active']!='' && $arData['SB_active']==1) 
						$arData['SB_date_active']=strtotime($arData['SB_date_active']);
				elseif($arData['SB_date_active']=='' && $arData['SB_active']==1)
					$arData['SB_date_active']=time();
				else
					unset($arData['SB_date_active']);
				$id = $obSubscribe->Save('SB_', $arData);
				$obUsers->Save($id,$arData['SB_news']);
				/* Осуществляем редирект после успешного сохранения */
				if (array_key_exists('update', $_REQUEST))
					CUrlParser::Redirect("admin.php?".$KS_URL->GetUrl(array('ACTION')).'&ACTION=edit&id='.$id);
				else
					CUrlParser::Redirect("admin.php?".$KS_URL->GetUrl(array('ACTION','p')));
					
			}
			catch(CError $e)
			{
				if($e->GetCode()==KS_ERROR_MAIN_ALREADY_EXISTS)
	   			{
	   				$e=new CError("MAIN_RECORD_ALREADY_EXISTS",0, 'Почта '.$arData['SB_email']);	
	   			}
				$data=$obSubscribe->GetRecordFromPost('SB_',$_POST);
				
				$smarty->assign('last_error', $e);
				
				$data['users']=$obSubscribe->GetUsers();
				$data['newsletters']=$obNews->GetList(array('name'=>'asc'), $arFilter);
				$user_news=$obUsers->GetList(array('name'=>'asc'), array('uin'=>$data['id']));
				if($data['newsletters'])
				foreach($data['newsletters'] as $key=>$item)
				{
					$data['newsletters'][$key]['select']=false;
					if($user_news)
					foreach($user_news as $itemUsers)
					{
						if($item['id']==$itemUsers['newsletter'])
						{
							$data['newsletters'][$key]['select']=true;
						}
					}
				
				}
				/*$users=$obSubscribe->GetList();
				foreach($data['users'] as $key=>$item)
				{
					foreach($users as $itemUsers)
					{
						
						if($item['id']==$itemUsers['uin'] && $data['uin']!=$item['id'])
						{
							unset($data['users'][$key]);	
						}
					}
				}*/
				
		
			
				$smarty->assign('data',$data);
				$page.='_edit';
			
				
			
			}
			
			
		break;
		
		/* Удаление */
		case "delete":
	
			$id = intval($_REQUEST['id']);
			
			
			$obSubscribe->Delete($id);
			$obUsers->DeleteItems(array('uin'=>$id));
			/* В случае успеха (или не успеха - как повезёт) делаем редирект */
			CUrlParser::Redirect("admin.php?" . $KS_URL->GetUrl(array("ACTION", "id")));
		break;
		
		default:
		break;		
	}

}
?>