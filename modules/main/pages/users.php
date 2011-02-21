<?php

/**
 * Страница управления профилями пользователей ЦМС.
 * 
 * 10.09.09 Добавлена поддержка ответа в формате json. Добавлена поддержка уменьшенной формы.
 * 
 * @filesource users.php
 * @author BlaDe39 <blade39@kolosstudio.ru>, north-e <pushkov@kolosstudio.ru>
 * @version 1.1
 * @since 13.04.2009
 */

/* Защита от взлома */
if (!defined("KS_ENGINE"))
	die("Hacking attempt!");

include_once(MODULES_DIR . "/main/libs/class.CUserGroup.php");

/* Проверка прав доступа к редактированию пользователей */
if ($USER->GetLevel("main") > 9)
	throw new CAccessError("MAIN_NO_RIGHT_TO_VIEW_USERS");
	
/* Обрабатываем входные данные (постраничный вывод) */
$obPages = new CPageNavigation($USER);
global $KS_URL;
/* Обработка сортировки */
$arSortFields = array("id", "title", "group_id", "last_visit", "active");
$sOrderField = (in_array($_REQUEST["order"], $arSortFields)) ? $_REQUEST["order"] : "id";
if ($_REQUEST["dir"] == "asc") : $sOrderDir = "asc"; $sNewDir="desc"; else: $sOrderDir = "desc"; $sNewDir = "asc"; endif;
$arOrder = array($sOrderField => $sOrderDir);

if (array_key_exists('ACTION',$_POST)&&($_POST['ACTION']=='common'))
{
	if ($USER->GetLevel("main") > 3) throw new CAccessError("MAIN_NO_RIGHT_TO_MANAGE_USERS");
	$arElements=$_POST['sel']['elm'];
	$sAction='common';
	if(in_array($USER->ID(),$arElements))
	{
		$pos=array_search($USER->ID(),$arElements);
		unset($arElements[$pos]);
		$e=new CError("MAIN_OPERATIONS_NOT_APPLIED_YOUR_ACCOUNT",1);
		$smarty->assign("last_error",$e);
	}
	if (array_key_exists('comact',$_POST))
	{
		// Установка общей активности для выделенных элементов
		$USER->Update($arElements,Array('active'=>'1'));
	}
	elseif (array_key_exists('comdea',$_POST))
	{
		//Снятие общей активности для элементов
		$sCommonAction='deactivate';
		$USER->Update($arElements,Array('active'=>'0'));
	}
	elseif (array_key_exists('comdel',$_POST))
	{
		// Удаление выделенных элементов
		$sCommonAction='delete';
		$USER->DeleteByIds($arElements);
	}
	unset($_REQUEST['ACTION']);
}

/* Выполняем действие над профилем пользователя */
if (array_key_exists("ACTION", $_REQUEST))
{
	if ($USER->GetLevel("main") > 3) throw new CAccessError("MAIN_NO_RIGHT_TO_MANAGE_USERS");
	/* Создание нового пользователя */
	if ($_REQUEST["ACTION"] == "new")
	{
		$userdata["id"] = -1;
		if (class_exists("CFields"))
		{
			/* Чтение пользовательских полей модуля users */
			$obFields = new CFields();
			$arFields = $obFields->GetList(array("id" => "asc"), array("module" => "users", "type" => $USER->sTable));
			if (is_array($arFields))
				foreach($arFields as $item)
					$userdata['ext_'.$item["title"]] = $item["default"];
		}
		if($KS_MODULES->IsActive('forum'))
		{
			$userdata['FORUM']=array();
		}
		if($KS_MODULES->IsActive('messages'))
		{
			$userdata['MESSAGES']=array();
		}
		/* Отправляем на страницу редактирования профиля */
		$page="_edit";
	}
	
	/* Редактирование профиля существующего пользователя */
	if ($_REQUEST["ACTION"] == "edit")
	{
		$id = intval($_REQUEST["id"]);
		$userdata = $USER->GetRecord(array("id" => $id));
		$userdata["GROUPS"] = $USER->GetAllGroups($id);
		if($KS_MODULES->IsActive('forum'))
		{
			include_once MODULES_DIR.'/forum/libs/class.CForumUser.php';
			$obUser=new CForumUser();
			$userdata['FORUM']=$obUser->GetRecord(array('user_id'=>$id));
		}
		if($KS_MODULES->IsActive('messages'))
		{
			include_once MODULES_DIR.'/messages/libs/class.CMessagesUser.php';
			$obUser=new CMessagesUser();
			$userdata['MESSAGES']=$obUser->GetById($id);
		}
		$page = "_edit";
	}
	
	if ($_REQUEST["ACTION"]=="save")
	{
		try
		{
			$USER->AddAutoField("id");
			$USER->sWidth=$KS_MODULES->GetConfigVar('users','avasizex',0);
			$USER->sHeight=$KS_MODULES->GetConfigVar('users','avasizey',0);
			$USER->sSize=$KS_MODULES->GetConfigVar('users','avasize',0);
			$USER->sRatio=true;
			$USER->sRatio_wb=false;
			$datefrom=$_POST["CU_blocked_from"];
			if(strlen($datefrom)>0)
			{
				if(preg_match("#^([\d]{2})\.([\d]{2})\.([\d]{4})( ([\d]{2}):([\d]{2}))?#",$datefrom,$matches))
				{
					$datefrom=mktime($matches[5],$matches[6],0,$matches[2],$matches[1],$matches[3]);
				}
				else
				{
					$datefrom=time();
				}
			}
			else
			{
				$datefrom=0;
			}
			$_POST['CU_blocked_from']=$datefrom;
			$dateto=$_POST["CU_blocked_till"];
			if (strlen($dateto)>0)
			{	
				if(preg_match("#^([\d]{2})\.([\d]{2})\.([\d]{4})( ([\d]{2}):([\d]{2}))?#",$dateto,$matches))
				{
					$dateto=mktime($matches[5],$matches[6],0,$matches[2],$matches[1],$matches[3]);
				}
				else
				{
					$dateto=time();
				}
			}
			else
			{
				$dateto=0;
			}
			if($dateto<$datefrom) $dateto=$datefrom+1;
			$_POST['CU_blocked_till']=$dateto;
			if($id = $USER->Save("CU_",$_POST))
			{
				global $ks_db;
				$usergroups=$USER->GetAllGroups($id);
				if(is_array($_POST["CU_groups"]))
				{
					foreach($_POST["CU_groups"] as $group_id)
					{
						$datefrom=$_POST["CU_groups_from".$group_id];
						if(strlen($datefrom)>0)
						{
							if(preg_match("#^([\d]{2})\.([\d]{2})\.([\d]{4})( ([\d]{2}):([\d]{2}))?#",$datefrom,$matches))
							{
								$datefrom=mktime($matches[5],$matches[6],0,$matches[2],$matches[1],$matches[3]);
							}
							else
							{
								$datefrom=time();
							}
						}
						else
						{
							$datefrom=0;
						}
						$dateto=$_POST["CU_groups_to".$group_id];
						if (strlen($dateto)>0)
						{	
							if(preg_match("#^([\d]{2})\.([\d]{2})\.([\d]{4})( ([\d]{2}):([\d]{2}))?#",$dateto,$matches))
							{
								$dateto=mktime($matches[5],$matches[6],0,$matches[2],$matches[1],$matches[3]);
							}
							else
							{
								$dateto=time();
							}
						}
						else
						{
							$dateto=0;
						}
						if (array_key_exists($group_id,$usergroups))
						{
							$ks_db->query("UPDATE " . PREFIX . $USER->sLinksTable
									. " SET date_start = '" . $datefrom . "',"
									. " date_end = '" . $dateto . "'"
									. " WHERE user_id = '" . $id . "' AND group_id = '" . $group_id . "'");
						}
						else
						{
							$ks_db->query("INSERT INTO " . PREFIX . $USER->sLinksTable
									. " (user_id, group_id, date_start, date_end) VALUES "
									. " ('" . $id . "', '" . $group_id . "', '" . $datefrom . "', '" . $dateto . "')");
						}
						unset($usergroups[$group_id]);
					}
				}
				if(count($usergroups)>0)
				{
					$ks_db->query("DELETE FROM " . PREFIX . $USER->sLinksTable
						. " WHERE user_id = '" . $id . "' AND group_id IN (" . join(",", array_keys($usergroups)) . ")");
				}
				if($KS_MODULES->IsActive('forum'))
				{
					include_once MODULES_DIR.'/forum/libs/class.CForumUser.php';
					$obUser=new CForumUser();
					$obUser->AddAutoField('id');
					$_POST['ff_user_id']=$id;
					$obUser->Save('ff_');
				}
				if($KS_MODULES->IsActive('messages'))
				{
					include_once MODULES_DIR.'/messages/libs/class.CMessagesUser.php';
					$obUser=new CMessagesUser();
					$obUser->AddAutoField('id');
					$_POST['mp_id']=$id;
					$obUser->Save('mp_');
				}
				if(!array_key_exists('update',$_REQUEST))
			    {
	    			CUrlParser::Redirect("/admin.php?".$KS_URL->GetUrl(Array('ACTION','id')));
	    		}
	    		else
	    		{
	    			CUrlParser::Redirect("/admin.php?".$KS_URL->GetUrl(array('ACTION','id')).'&ACTION=edit&id='.$id);
	    		}
			}
		}
		catch(CError $e)
		{
			$id = intval($_REQUEST["id"]);
			//$userdata=$USER->GetRecordFromPost('CU_',$_POST);
			$userdata=$USER->GetRecord(array('id'=>$id));
			$userdata["GROUPS"] = $USER->GetAllGroups($id);
			if($KS_MODULES->IsActive('forum'))
			{
				include_once MODULES_DIR.'/forum/libs/class.CForumUser.php';
				$obUser=new CForumUser();
				$userdata['FORUM']=$obUser->GetRecord(array('user_id'=>$id));
			}
			if($KS_MODULES->IsActive('messages'))
			{
				include_once MODULES_DIR.'/messages/libs/class.CMessagesUser.php';
				$obUser=new CMessagesUser();
				$userdata['MESSAGES']=$obUser->GetById($id);
			}
			$smarty->assign("last_error",$e);
			$page="_edit";
		}
	}
	if ($_REQUEST["ACTION"]=="delete")
	{
		if($_REQUEST["id"]!=$USER->ID())
		{
			$id=intval($_REQUEST["id"]);
			$USER->Delete($id);
			/*$id=intval($_REQUEST["id"]);
			$userdata=$USER->GetRecord(array("id"=>$id));
			$smarty->assign("message","Вы действительно хотите удалить пользователя: ".$userdata["title"].", имеющего номер: ".$userdata["id"]);
			$smarty->assign("userdata",$userdata);
			$page="_msgbox";*/
		}
		else
		{
			$e=new CError("MAIN_NOT_DELETE_YOURSELF",0);
			$smarty->assign("last_error",$e);
		}
	}
	if ($_REQUEST["ACTION"]=="delete_confirm")
	{
		if($_REQUEST["id"]!=$USER->ID())
		{
			$id=intval($_REQUEST["id"]);
			$USER->Delete($id);
		}
		else
		{
			$e=new CError("MAIN_NOT_DELETE_YOURSELF",0);
			$smarty->assign("last_error",$e);
		}
	}
}

if($page=="_edit")
{
	$obGroups=new CUserGroup();
	$smarty->assign("groupslist",$obGroups->GetList());
	$smarty->assign("userdata",$userdata);
	if (class_exists("CFields"))
	{
		$obFields=new CFields();
		$arFields=$obFields->GetList(Array("id"=>"asc"),Array("module"=>"users","type"=>$USER->sTable));
		$smarty->assign("addFields",$arFields);
	}
}
else
{
	if(class_exists('CFilterFrame'))
	{
		$obGroups=new CUserGroup();
		$arGroups=$obGroups->GetList();
		foreach($arGroups as $arGroup)
		{
			$arRes[$arGroup['id']]=$arGroup['title'];
		}
		$obFilter=new CFilterFrame();
		$obFilter->AddField(array('FIELD'=>'title','METHOD'=>'~'));
		$obFilter->AddField(array('FIELD'=>'email','METHOD'=>'~'));
		$obFilter->AddField(array(
			'FIELD'=>'active',
			'TYPE'=>'SELECT',
			'VALUES'=>array(
				''=>'Любой',
				'1'=>'Активен',
				'0'=>'Неактивен'),
		));
		$obFilter->AddField(array('FIELD'=>'group_id','METHOD'=>'->','TYPE'=>'SELECT','VALUES'=>$arRes));
		$obFilter->AddField(array('FIELD'=>'last_visit','TYPE'=>'DATE','METHOD'=>'<>'));
		$arFilter=$obFilter->GetFilter();
		if(strlen($arFilter['->group_id'])>0)
		{
			$arFilter['->users_grouplinks.group_id']=$arFilter['->group_id'];
			$arFilter['?users_grouplinks.user_id']='users.id';
			unset($arFilter['->group_id']);
		}
		$obFilter->SetSmartyFilter('filter');
		$arTitles=array(
				'title'=>'Логин',
				'email'=>'Адрес электронной почты',
				'active'=>'Активность',
				'last_visit'=>'Дата последней активности',
				'date_add'=>'Дата добавления',
				'group_id'=>'Группа');
		$smarty->assign('ftitles',$arTitles);
	}
	else $arFilter=false;

	$totalUsers=$USER->count($arFilter);
	$list=$USER->GetList($arOrder,$arFilter,$obPages->GetLimits($totalUsers));
	if($_GET['mode']=='ajax') 
	{
		$data=array(
			'list'=>$list,
			'level'=>$USER->GetLevel('main'),
			'pages'=>$obPages->GetPages($totalUsers),
			'num_visible'=>$obPages->GetVisible(),
			'groups_num'=>$USER->GetNum(),
			'order'=>Array("newdir"=>$sNewDir,"curdir"=>$sOrderDir,"field"=>$sOrderField)
		);
		echo json_encode($data);
		die();
	}
	else
	{
		$smarty->assign("list",$list);
		$smarty->assign("groups_num",$USER->GetNum());
		$smarty->assign("pages",$obPages->GetPages($totalUsers));
		$smarty->assign("num_visible",$obPages->GetVisible());
		$smarty->assign('level',$USER->GetLevel('main'));
		$smarty->assign("order",Array("newdir"=>$sNewDir,"curdir"=>$sOrderDir,"field"=>$sOrderField));
	}
}
if ($page!="_msgbox")
{
	$page="_users".$page;
}
if($_GET['mode']=='small')
{
	echo $smarty->get_template_vars('last_error');
	$smarty->display('admin/main'.$page.'.tpl');
	die();
}

?>
