<?php
/**
 * Страница управления подсказками
 *
 * @filesource modules/hints/pages/index.php
 * @author BlaDe39 <blade39@kolosstudio.ru>
 * @version 2.6
 * @since 25.05.2011
 */

/* Защита от взлома */
if (!defined("KS_ENGINE")) die("Hacking attempt!");

require_once MODULES_DIR.'/main/libs/class.CModuleAdmin.php';
require_once MODULES_DIR.'/hints/libs/class.CHintsAPI.php';

class ChintsAIindex extends CModuleAdmin
{
	private $obAPI;
	private $iAccess;
	private $iMode;

	function __construct($module='hints',&$smarty,&$parent)
	{
		parent::__construct($module,$smarty,$parent);
		$this->obAPI=CHintsAPI::get_instance();
		$this->iAccess=$this->obUser->GetLevel($this->module);
	}

	function Table()
	{
		$obHints=$this->obAPI->Hints();
		$arSortFields=$obHints->GetFields();
		// Обработка порядка вывода элементов
		list($sOrderField,$sOrderDir)=$this->InitSort($arSortFields,$_REQUEST['order'],$_REQUEST['dir']);
		$sNewDir=($sOrderDir=='desc')?'asc':'desc';
		if(class_exists('CFilterFrame'))
		{
			$obFilter=new CFilterFrame();
			$obFilter->AddField(array('FIELD'=>'text_ident','METHOD'=>'~'));
			$obFilter->AddField(array('FIELD'=>'id'));
			$arFilter=$obFilter->GetFilter();
			$obFilter->SetSmartyFilter('filter');
			$arTitles=array(
				'text_ident'=>$this->obModules->GetText('filter_text_ident'),
				'id'=>$this->obModules->GetText('filter_id'),
			);
			$this->smarty->assign('ftitles',$arTitles);
		}
		else $arFilter=false;

		$obPages = new CPages(20);
		$iCount=$obHints->count($arFilter);
		$arOrder=array($sOrderField=>$sOrderDir);
		if($arList=$obHints->GetList($arOrder,$arFilter,$obPages->GetLimits($iCount)))
		{
			$this->smarty->assign("list",$arList);
			$this->smarty->assign("pages",$obPages->GetPages($iCount));
			$this->smarty->assign('level',$this->iAccess);
			$this->smarty->assign("order",Array("newdir"=>$sNewDir,"curdir"=>$sOrderDir,"field"=>$sOrderField));
		}
		return '';
	}

	/**
	 * Форма добавления/редактирования пользователя
	 */
	function EditForm($data=false)
	{
		if(!$data)
		{
			$data=array(
				'id'=>-1,
				'text_ident'=>$this->obModules->GetText('new_text_ident'),
				'content'=>$this->obModules->GetText('new_content')
			);
		}
		$this->smarty->assign("data",$data);
		return '_edit';
	}

	/**
	 * Метод обеспечивает сохранение учётной записи пользователя
	 */
	function Save()
	{
		global $KS_URL;
		try
		{
			if($this->iAccess>1)
			{
				$this->obModules->AddNotify('HINTS_ACCESS_DENIED');
				return $this->Table();
			}
			$bError=0;
			//Проверка кода
			if(strlen($_POST['text_ident'])==0)
			{
				$bError+=$this->obModules->AddNotify('HINTS_TEXT_IDENT_REQUIRED');
			}
			elseif(!IsTextIdent($_POST['text_ident']))
			{
				$bError+=$this->obModules->AddNotify('HINTS_TEXT_IDENT_WRONG');
			}
			else
			{
				if($arUser=$this->obAPI->Hints()->GetRecord(array('text_ident'=>$_POST['text_ident'],'!id'=>$_POST['id'])))
				{
					$bError+=$this->obModules->AddNotify('HINTS_TEXT_IDENT_ALREADY');
				}
			}
			if($bError==0)
			{
				if($id = $this->obAPI->Hints()->Save('',$_POST))
				{
					if(!array_key_exists('update',$_REQUEST))
					{
						$this->obModules->AddNotify('HINTS_SAVE_OK','',NOTIFY_MESSAGE);
						CUrlParser::get_instance()->Redirect("/admin.php?".$KS_URL->GetUrl(Array('action','id')));
					}
					else
					{
						$this->obModules->AddNotify('HINTS_SAVE_OK','',NOTIFY_MESSAGE);
						CUrlParser::get_instance()->Redirect("/admin.php?".$KS_URL->GetUrl(array('action','id')).'&action=edit&id='.$id);
					}
				}
				else
				{
					throw new CError('HINTS_SAVE_ERROR');
				}
			}
			else
			{
				throw new CDataError('HINTS_FIELDS_ERROR');
			}
		}
		catch(CError $e)
		{
			$data=$this->obAPI->Hints()->GetFromPost('',$_POST);
			$this->smarty->assign("last_error",$e->__toString());
			return $this->EditForm($data);
		}
	}

	function Run()
	{
		global $KS_URL;
		/* Проверка прав доступа к редактированию пользователей */
		if($this->iAccess > 0) throw new CAccessError("HINTS_ACCESS_DENIED");
		$userdata=false;
		if(array_key_exists('action',$_REQUEST))
			$sAction=$_REQUEST['action'];
		else
			$sAction='';
		if(array_key_exists('id',$_REQUEST))
			$iId=intval($_REQUEST['id']);
		else
			$iId=0;
		$data=false;
		try
		{
			switch($sAction)
			{
				case 'edit':
					$data=$this->obAPI->Hints()->GetRecord(array('id'=>$iId));
					if(!$data) throw new CError('HINTS_NOT_FOUND');
				case 'new':
					$page=$this->EditForm($data);
				break;
				case 'save':
					$page=$this->Save();
				break;
				case 'delete':
					if($arHint=$this->obAPI->Hints()->GetById($iId))
					{
						$this->obAPI->Hints()->DeleteItems(array('id'=>$iId));
						$this->obModules->AddNotify('HINTS_DELETE_OK','',NOTIFY_MESSAGE);
						CUrlParser::get_instance()->Redirect("/admin.php?".$KS_URL->GetUrl(Array('action','id')));
					}
					else
					{
						throw new CError('HINTS_NOT_FOUND');
					}
				break;
				default:
					$page=$this->Table();
			}
		}
		catch(CError $e)
		{
			$this->obModules->AddNotify($e->getMessage());
			$page=$this->Table();
		}
		$this->smarty->assign('mode',$this->iMode);
		return $page;
	}
}



