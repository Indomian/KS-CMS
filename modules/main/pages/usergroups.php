<?php
/**
 * Страница управления группами пользователей ЦМС.
 *
 * @filesource usergroups.php
 * @author BlaDe39 <blade39@kolosstudio.ru>
 * @version 2.6
 * @since 2008
 */
if( !defined('KS_ENGINE') ){ die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CUserGroup.php';
require_once MODULES_DIR.'/main/libs/class.CUGModules.php';
require_once MODULES_DIR.'/main/libs/class.CModulesAccess.php';
require_once MODULES_DIR.'/main/libs/class.CModuleAdmin.php';

class CmainAIusergroups extends CModuleAdmin
{
	private $obGroups;

	function __construct($module='main',&$smarty,&$parent)
	{
		parent::__construct($module,$smarty,$parent);
		$this->obGroups=new CUserGroup();
		//Устанавливаем значение для правильного вывода меню в верхней части шаблона
		$this->smarty->assign('modpage','users');
		//Проверка прав доступа
		if($this->obUser->GetLevel('main')>2) throw new CAccessError("MAIN_NOT_RIGHTS_MANAGE_USER_GROUPS");
	}

	function Table()
	{
		$arSortFields=$this->obGroups->GetFields();
		// Обработка порядка вывода элементов
		list($sOrderField,$sOrderDir)=$this->InitSort($arSortFields,$_REQUEST['order'],$_REQUEST['dir']);
		$sNewDir=($sOrderDir=='desc')?'asc':'desc';

		$obPages = $this->InitPages();
		$iCount=$this->obGroups->count();
		$arOrder=array($sOrderField=>$sOrderDir);
		if($arList=$this->obGroups->GetList($arOrder,false,$obPages->GetLimits($iCount)))
		{
			$this->smarty->assign("list",$arList);
			$this->smarty->assign("pages",$obPages->GetPages($iCount));
			$this->smarty->assign('groups_num',$iCount);
			$this->smarty->assign('level',$this->obUser->GetLevel('main'));
			$this->smarty->assign('num_visible',$obPages->GetVisible());
			$this->smarty->assign("order",Array("newdir"=>$sNewDir,"curdir"=>$sOrderDir,"field"=>$sOrderField));
		}
		return '';
	}

	function EditForm($data=false)
	{
		$arGroup=array();
		if($data)
		{
			$arGroup=$data;
			if(array_key_exists('id',$arGroup) && $arGroup['id']>0)
			{
				$obAccess=new CModulesAccess();
				$arGroup['ACCESS']=$obAccess->GetList(array('id'=>'asc'),array('group_id'=>$arGroup['id']));
			}
		}
		else
		{
			$arGroup['id']=-1;
		}
		$obModule=new CUGModules();
		$arGroup['MODULES']=$obModule->GetList();
		//Добавляем в список главный модуль
		$arGroup['MODULES'][]=array(
			'name'=>$this->obModules->GetText('main_module_title'),
			'directory'=>'main',
			'LEVELS'=>$this->obModules->GetAccessArray('main')
		);
		$this->smarty->assign('userdata',$arGroup);
		return '_edit';
	}

	function Save()
	{
		$KS_URL=CUrlParser::get_instance();
		try
		{
			$bError=0;
			$arGroup=array(
				'title'=>EscapeHTML($_POST['CUG_title']),
				'description'=>EscapeHTML($_POST['CUG_description']),
				'number_of_log_tries'=>intval($_POST['CUG_number_of_log_tries'])
			);
			if(array_key_exists('CUG_id',$_POST))
				$arGroup['id']=intval($_POST['CUG_id']);
			if(strlen($arGroup['title'])==0)
				$bError=$this->obModules->AddNotify('MAIN_GROUP_TITLE_REQUIRED');
			if($bError==0)
			{
				$this->obGroups->AddAutoField('id');
				if($id=$this->obGroups->Save('',$arGroup))
				{
					$obAccess=new CModulesAccess();
					foreach($_POST['CUG_level'] as $key=>$value)
					{
						$obAccess->Set($id,$key,min($value));
					}
				}
				if(!array_key_exists('update',$_REQUEST))
				{
					$this->obModules->AddNotify('MAIN_GROUP_SAVE_OK','',NOTIFY_MESSAGE);
					CUrlParser::get_instance()->Redirect("/admin.php?".$KS_URL->GetUrl(Array('ACTION','id')));
				}
				else
				{
					$this->obModules->AddNotify('MAIN_GROUP_SAVE_OK','',NOTIFY_MESSAGE);
					CUrlParser::get_instance()->Redirect("/admin.php?".$KS_URL->GetUrl(array('ACTION','id')).'&ACTION=edit&id='.$id);
				}
			}
			else
			{
				throw new CError('MAIN_GROUP_SAVE_ERROR');
			}
		}
		catch (CError $e)
		{
			$this->smarty->assign("last_error",$e->__toString());
			return $this->EditForm($arGroup);
		}
	}

	function Run()
	{
		$KS_URL=CUrlParser::get_instance();
		$action='';
		if(array_key_exists('ACTION',$_REQUEST))
			$action=$_REQUEST['ACTION'];
		$data=false;
		switch($action)
		{
			case 'edit':
				if(array_key_exists('id',$_REQUEST))
				{
					if($arGroup=$this->obGroups->GetById(intval($_REQUEST['id'])))
					{
						$data=$arGroup;
					}
					else
					{
						$this->obModules->AddNotify('MAIN_GROUP_NOT_FOUND');
					}
				}
				else
				{
					$this->obModules->AddNotify('MAIN_GROUP_ID_REQUIRED');
					break;
				}
			case 'new':
				$page=$this->EditForm($data);
			break;
			case 'save':
				$page=$this->Save();
			break;
			case 'delete':
				if(array_key_exists('id',$_REQUEST))
				{
					if($arGroup=$this->obGroups->GetById(intval($_REQUEST['id'])))
					{
						if(in_array($arGroup['id'],$this->obUser->GetGroups()))
						{
							$this->obModules->AddNotify("MAIN_NOT_DELETE_GROUP_YOU_BELONG");
						}
						else
						{
							$this->obGroups->Delete($arGroup['id']);
							$this->obUser->OnDeleteUserGroup($arGroup['id']);
							$obAccess=new CModulesAccess();
							$obAccess->DeleteItems(array('group_id'=>$arGroup['id']));
							$this->obModules->AddNotify('MAIN_GROUP_DELETE_OK','',NOTIFY_MESSAGE);
							CUrlParser::get_instance()->Redirect("/admin.php?".$KS_URL->GetUrl(Array('ACTION','id')));
						}
					}
					else
					{
						$this->obModules->AddNotify('MAIN_GROUP_NOT_FOUND');
					}
				}
				else
				{
					$this->obModules->AddNotify('MAIN_GROUP_ID_REQUIRED');
				}
			default:
				$page=$this->Table();
		}
		return '_usersgroup'.$page;
	}
}