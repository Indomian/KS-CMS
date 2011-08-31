<?php
/**
 * @file navigation/pages/menu.php
 * Файл используется при редактировании и выводе элементов меню
 * Файл проекта kolos-cms.
 *
 * Изменен 14.01.2011
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CModuleAdmin.php';
require_once MODULES_DIR.'/navigation/libs/class.CNav.php';

class CnavigationAImenu extends CModuleAdmin
{
	private $iCurSection;
	private $iParentId;
	private $iId;
	private $oElement;
	private $oType;

	function __construct($module='navigation',&$smarty,&$parent)
	{
		parent::__construct($module,$smarty,$parent);
		$this->oElement=new CNavElement();
		$this->oType=new CNavTypes();
		$this->iCurSection=0;
		$this->iParentId=0;
		$this->iId=0;
	}

	function EditForm($data=false)
	{
		if(!$data)
		{
			if($arData=$this->oElement->GetList(
				array('orderation'=>'desc'),
				array('parent_id'=>$this->iParentId,'type_id'=>$this->iCurSection),
				array(1),
				array('orderation')
			))
			{
				$arItem=array_pop($arData);
			}
			else
			{
				$arItem['orderation']=0;
			}
			$data=array();
			$data['orderation']=intval($arItem['orderation'])+10;
			$data['id']=-1;
			$data['parent_id']=$this->iParentId;
			$data['type_id']=$this->iCurSection;
		}
		$arSectionRes=$this->oType->GetByID($this->iCurSection);
		$data['SECTION']=$arSectionRes;
		// Если есть пользовательские поля, то надобы получить их список
		if (class_exists('CFields'))
		{
			$obFields=new CFields();
			$arFields=$obFields->GetList(Array('id'=>'asc'),Array('module'=>"navigation",'type'=>$this->oElement->sTable));
			$this->smarty->assign('addFields',$arFields);
		}
		$this->smarty->assign('data',$data);
		$this->smarty->assign('groups_list',$this->oType->GetScriptList());
		return '_edit';
	}

	function Table()
	{
		$arSectionRes=$this->oType->GetByID($this->iCurSection);
		$arChildElements = Array();
		$arSelect=Array('id','name','text_ident','description','script_name');
		$arFilter=Array('type_id'=>$this->iCurSection,'parent_id'=>$this->iParentId);
		$arOrder=Array('orderation'=>'asc');
		if($arResult['ITEMS'] = $this->oElement->GetList($arOrder,$arFilter))
		{

		}
		else
		{
			$arData=$this->oElement->GetById($this->iParentId);
			$this->iParentId = $arData['parent_id'];
		}
		$arResult['ITEMS'] = Array();
		while($this->iParentId >= 0)
		{
			$arFilter=Array('type_id'=>$this->iCurSection,'parent_id'=>$this->iParentId);
			/*Ищем элементы меню по $iParentId*/
			if($arChildElements = $this->oElement->GetList($arOrder,$arFilter))
			{
				if(!empty($arResult['ITEMS']))
				{
					//если НЕ первая итерация
					/**
					* Один из элементов массива $arChildElements будет являться
					* родителем для всего массива $arChildElements на данной итерации
					*/
					foreach($arChildElements as $key => $arElement)
					{
						if($arElement['id'] == $this->iId)
						{
							//если найден элемент id которого равен parent_id для предыдущей итерации...
							//дописываем этому элементу массив дочерних
							$arChildElements[$key]['ITEMS'] = $arResult['ITEMS'];
						}
					}
					$arResult['ITEMS'] = $arChildElements;	//сохраняем новый массив
				}
				else
				{
					//если первая итерация
					//просто сохраняем результат
					$arResult['ITEMS'] = $arChildElements;
				}
			}
			else
			{
				break;
			}
			if($arResult['ITEMS']===false)
			{
				//если предыдущей операцией было удаление
				//сбрасываем значения для вывода списка элементов меню нулевого уровня
				$iParentId = 0;
				$arResult['ITEMS']=array();
				continue;
			}
			//если предыдущей операцией было НЕ удаление и нет возможности выйти на уровень выше
			if(($this->oElement->GetById($this->iParentId)===false)&&($this->iParentId == 0))
			{
				//значит дерево элементов построено.
				break;
			}
			else
			{
				//возможности выйти на уровень выше есть
				$this->iId = $this->iParentId;
				$this->iParentId = $this->oElement->GetById($this->iParentId);
				$this->iParentId = intval($this->iParentId['parent_id']);
			}
		}
		$arResult['SECTION']=$arSectionRes;
		$this->smarty->assign('dataList',$arResult);
		return '';
	}

	function Run()
	{
		global $KS_URL;
		if($this->obUser->GetLevel($this->module)>0) throw new CAccessError('NAVIGATION_ACCESS_DENIED');
		$sAction='';
		if(isset($_REQUEST['ACTION']))
			$sAction=$_REQUEST['ACTION'];
		$arResult=array();
		/* Определение типа меню (его id) */
		if(isset($_REQUEST['typeid']))
			$this->iCurSection=intval($_REQUEST['typeid']);
		/* Определение id пункта меню, который разворачиваем */
		if(isset($_REQUEST['CSC_parid']))
			$this->iParentId=intval($_REQUEST['CSC_parid']);
		/* Определение номера текущего элемента */
		if(isset($_REQUEST['CSC_elmid']))
			$this->iId=intval($_REQUEST['CSC_elmid']);
		$data=false;
		switch($sAction)
		{
			case "edit":
				$data=$this->oElement->GetById($this->iId);
			case "new":
				$page=$this->EditForm($data);
			break;
			case "save":
				$this->oElement->AddAutoField('id');
				$this->oElement->AddFileField('img');

				$_POST['CSC_active']=intval($_POST['CSC_active']);

				if($id=$this->oElement->Save('CSC_',$_POST))
				{
					$iParentId = $this->oElement->GetByID($id);
					$iParentId = intval($iParentId['parent_id']);
					$this->obModules->AddNotify('NAVIGATION_MENU_ITEM_SAVED','',NOTIFY_MESSAGE);
					if(!array_key_exists('update',$_REQUEST))
					{
						CUrlParser::get_instance()->Redirect("/admin.php?".$KS_URL->GetUrl(Array('ACTION','CSC_elmid')).'&CSC_parid='.$iParentId);
					}
					else
					{
						CUrlParser::get_instance()->Redirect("/admin.php?".$KS_URL->GetUrl(array('ACTION','CSC_elmid')).'&ACTION=edit&CSC_elmid='.$id);
					}
				}
				else
				{
					throw new CError('SYSTEM_SAVE_ERROR');
				}
			break;
			case "delete":
				if($arData=$this->oElement->GetById($this->iId))
				{
					$this->oElement->Delete($this->iId);
					$this->obModules->AddNotify('NAVIGATION_MENU_ITEM_DELETED','',NOTIFY_MESSAGE);
					$this->iParentId = $arData['parent_id'];
					$KS_URL->Set('CSC_parid',$arData['parent_id']);
					$KS_URL->Redirect("admin.php?".$KS_URL->GetUrl(Array('ACTION')).'&CSC_parid='.$iParentId);
				}
				else
				{
					$this->obModules->AddNotify('NAVIGATION_MENU_ITEM_NOT_FOUND');
				}
			break;
			case "hide":

				if($arData=$this->oElement->GetById($this->iId))
				{
					$arData['active']=0;
					if($id=$this->oElement->Save('',$arData))
					{
						$this->obModules->AddNotify('NAVIGATION_MENU_ITEM_NOT_ACTIVE','',NOTIFY_MESSAGE);
						$this->iParentId = $arData['parent_id'];
						$KS_URL->Set('CSC_parid',$arData['parent_id']);
						$KS_URL->Redirect("admin.php?".$KS_URL->GetUrl(Array('ACTION')));
					}
					else
					{
						throw new CError('SYSTEM_SAVE_ERROR');
					}

				}
				else
				{
					$this->obModules->AddNotify('NAVIGATION_MENU_ITEM_NOT_FOUND');
				}
			break;
			case "show":
				if($arData=$this->oElement->GetById($this->iId))
				{
					$arData['active']=1;
					if($id=$this->oElement->Save('',$arData))
					{
						$this->obModules->AddNotify('NAVIGATION_MENU_ITEM_ACTIVE','',NOTIFY_MESSAGE);
						$this->iParentId = $arData['parent_id'];
						$KS_URL->Set('CSC_parid',$arData['parent_id']);
						$KS_URL->Redirect("admin.php?".$KS_URL->GetUrl(Array('ACTION')));
					}
					else
					{
						throw new CError('SYSTEM_SAVE_ERROR');
					}

				}
				else
				{
					$this->obModules->AddNotify('NAVIGATION_MENU_ITEM_NOT_FOUND');
				}
			break;
			default:
				$page=$this->Table();
			break;
		}
		return '_elements'.$page;
	}
}