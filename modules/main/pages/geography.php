<?php
/**
 * @file main/pages/geography.php
 * Файл работы с географическими объектами
 * Файл проекта kolos-cms.
 *
 * Изменен 03.03.2011
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CModuleAdmin.php';
require_once MODULES_DIR.'/main/libs/class.CGeographyAPI.php';

class CmainAIgeography extends CModuleAdmin
{
	private $obAPI;

	function __construct($module='main',&$smarty,&$parent)
	{
		global $USER;
		parent::__construct($module,$smarty,$parent);
		$this->obUser=$USER;
		$this->obAPI=CGeographyAPI::get_instance();
	}

	/**
	 * Метод выводит форму импорта стран в базу данных
	 */
	function ImportCountriesForm()
	{
		return '_geography_countries_import';
	}

	/**
	 * Метод выполняет импорт стран из списка в базу данных
	 */
	function DoImportCountriesForm()
	{
		$bError=0;
		if($_POST['mode']=='self')
		{
			$sFilePath=MODULES_DIR.'/main/install/countries.txt';
		}
		elseif($_POST['mode']=='file')
		{
			if($_FILES['values']['size']>0 && file_exists($_FILES['values']['tmp_name']))
			{
				$sFilePath=$_FILES['values']['tmp_name'];
			}
			else
			{
				$bError=$this->obModules->AddNotify('MAIN_GEOGRAPHY_IMPORT_FILE_REQUIRED');
			}
		}
		else
		{
			$bError=$this->obModules->AddNotify('MAIN_GEOGRAPHY_IMPORT_MODE_REQUIRED');
		}
		if(!file_exists($sFilePath))
		{
			$bError=$this->obModules->AddNotify('MAIN_GEOGRAPHY_IMPORT_FILE_REQUIRED');
		}
		if($bError==0)
		{
			if($_POST['clear']==1)
			{
				$this->obAPI->Country()->DeleteItems();
				$this->obAPI->City()->DeleteItems();
			}
			$sFile=file_get_contents($sFilePath);
			$arCountries=explode("\n",$sFile);
			if(is_array($arCountries) && count($arCountries)>0)
			{
				$obCountry=$this->obAPI->Country();
				foreach($arCountries as $sCountry)
				{
					$arFields=array(
						'title'=>trim($sCountry)
					);
					$obCountry->Save('',$arFields);
				}
				$this->obModules->AddNotify('MAIN_GEOGRAPHY_IMPORT_OK','',NOTIFY_MESSAGE);
				CUrlParser::get_instance()->Redirect('/admin.php?module=main&modpage=geography');
			}
			$bError=$this->obModules->AddNotify('MAIN_GEOGRAPHY_IMPORT_FILE_EMPTY');
		}
		return '_geography_countries_import';
	}

	/**
	 * Метод возвращает таблицу стран
	 */
	function TableCountries()
	{
		$obCountry=$this->obAPI->Country();
		$arSortFields=$obCountry->GetFields();
		// Обработка порядка вывода элементов
		list($sOrderField,$sOrderDir)=$this->InitSort($arSortFields,$_REQUEST['order'],$_REQUEST['dir']);
		$sNewDir=($sOrderDir=='desc')?'asc':'desc';
		$arFilter=array();
		$obCountry->Count($arFilter);
		$obPages = new CPageNavigation($obCountry);
		$arList=$obCountry->GetList(array($sOrderField=>$sOrderDir),$arFilter,$obPages->GetLimits());
		$this->smarty->assign('data',$arList);
		$this->smarty->assign('pages',$obPages->GetPages());
		$this->smarty->assign('order',Array('newdir'=>$sNewDir,'curdir'=>$sOrderDir,'field'=>$sOrderField));
		return '_geography_countries';
	}

	/**
	 * Метод возвращает таблицу городов определённой страны
	 */
	function TableCities($country_id)
	{
		$obCountry=$this->obAPI->Country();
		if($arCountry=$obCountry->GetRecord(array('id'=>intval($country_id))))
		{
			$obCity=$this->obAPI->City();
			$arSortFields=$obCity->GetFields();
			// Обработка порядка вывода элементов
			list($sOrderField,$sOrderDir)=$this->InitSort($arSortFields,$_REQUEST['order'],$_REQUEST['dir']);
			$sNewDir=($sOrderDir=='desc')?'asc':'desc';
			$arFilter=array(
				'country_id'=>$arCountry['id'],
			);
			$obCity->Count($arFilter);
			$obPages = new CPageNavigation($obCity);
			$arList=$obCity->GetList(array($sOrderField=>$sOrderDir),$arFilter,$obPages->GetLimits());
			$this->smarty->assign('data',$arList);
			$this->smarty->assign('country',$arCountry);
			$this->smarty->assign('pages',$obPages->GetPages());
			$this->smarty->assign('order',Array('newdir'=>$sNewDir,'curdir'=>$sOrderDir,'field'=>$sOrderField));
			return '_geography_cities';
		}
		else
		{
			$this->obModules->AddNotify('MAIN_GEOGRAPHY_COUNTRY_NOT_FOUND');
			CUrlParser::get_instance()->Redirect('/admin.php?module=main&action=geography');
		}
	}

	/**
	 * Метод выводит форму редактирования города
	 */
	function CityForm($city_id,$country_id)
	{
		global $KS_URL;
		$obCountry=$this->obAPI->Country();
		if($arCountry=$obCountry->GetRecord(array('id'=>intval($country_id))))
		{
			if($city_id>0)
			{
				if($arCity=$this->obAPI->City()->GetById(intval($city_id)))
				{
					$this->smarty->assign('data',$arCity);
				}
				else
				{
					$this->obModules->AddNotify('MAIN_GEOGRAPHY_CITY_NOT_FOUND');
					$KS_URL->Redirect($KS_URL->GetUrl(array('action','city_id')).'&action=cities');
				}
			}
			else
			{
				$arData=array(
					'id'=>-1,
					'country_id'=>$arCountry['id']
				);
				$this->smarty->assign('data',$arData);
			}
			$this->smarty->assign('country',$arCountry);
			return '_geography_city_edit';
		}
		else
		{
			$this->obModules->AddNotify('MAIN_GEOGRAPHY_COUNTRY_NOT_FOUND');
			CUrlParser::get_instance()->Redirect('/admin.php?module=main&action=geography');
		}
	}

	/**
	 * Метод выполняет сохранение записи о городе
	 */
	function SaveCity()
	{
		global $KS_URL;
		$arFields=array(
			'id'=>intval($_POST['city_id']),
			'title'=>$_POST['city_title'],
			'country_id'=>intval($_POST['city_country_id']),
		);
		$bError=0;
		if(IsEmpty($arFields['title']))
		{
			$bError=$this->obModules->AddNotify('MAIN_GEOGRAPHY_CITY_TITLE_REQUIRED');
		}
		if(!$this->obAPI->Country()->GetById($arFields['country_id']))
		{
			$this->obModules->AddNotify('MAIN_GEOGRAPHY_COUNTRY_NOT_FOUND');
			CUrlParser::get_instance()->Redirect('/admin.php?module=main&action=geography');
		}
		if($bError==0)
		{
			if($id=$this->obAPI->City()->Save('',$arFields))
			{
				if(!array_key_exists('update',$_REQUEST))
				{
					CUrlParser::get_instance()->Redirect("admin.php?".$KS_URL->GetUrl(Array('action','city_id')).'&action=cities');
				}
				else
				{
					CUrlParser::get_instance()->Redirect("admin.php?".$KS_URL->GetUrl('action','city_id').'&action=edit_city&city_id='.$id);
				}
			}
			else
			{
				$this->obModules->AddNotify('MAIN_GEOGRAPHY_CITY_SAVE_ERROR');
				CUrlParser::get_instance()->Redirect("admin.php?".$KS_URL->GetUrl(Array('action','city_id')));
			}
		}
		else
		{
			return $this->CityForm(intval($arFields['id']),intval($arFields['country_id']));
		}
	}

	/**
	 * Метод выводит форму редактирования страны
	 */
	function CountryForm($country_id)
	{
		global $KS_URL;
		$obCountry=$this->obAPI->Country();
		if($arCountry=$obCountry->GetRecord(array('id'=>intval($country_id))))
		{
			$this->smarty->assign('data',$arCountry);
			return '_geography_country_edit';
		}
		else
		{
			$this->obModules->AddNotify('MAIN_GEOGRAPHY_COUNTRY_NOT_FOUND');
			CUrlParser::get_instance()->Redirect('/admin.php?module=main&action=geography');
		}
	}

	/**
	 * Метод выполняет сохранение записи о стране
	 */
	function SaveCountry()
	{
		global $KS_URL;
		$arFields=array(
			'id'=>intval($_POST['country_id']),
			'title'=>$_POST['country_title'],
		);
		$bError=0;
		if(IsEmpty($arFields['title']))
		{
			$bError=$this->obModules->AddNotify('MAIN_GEOGRAPHY_COUNTRY_TITLE_REQUIRED');
		}
		if(!$this->obAPI->Country()->GetById($arFields['id']))
		{
			$this->obModules->AddNotify('MAIN_GEOGRAPHY_COUNTRY_NOT_FOUND');
			CUrlParser::get_instance()->Redirect('/admin.php?module=main&action=geography');
		}
		if($bError==0)
		{
			if($id=$this->obAPI->Country()->Save('',$arFields))
			{
				if(!array_key_exists('update',$_REQUEST))
				{
					CUrlParser::get_instance()->Redirect("admin.php?".$KS_URL->GetUrl(Array('action','country_id')));
				}
				else
				{
					CUrlParser::get_instance()->Redirect("admin.php?".$KS_URL->GetUrl('action','country_id').'&action=edit_country&country_id='.$id);
				}
			}
			else
			{
				$this->obModules->AddNotify('MAIN_GEOGRAPHY_COUNTRY_SAVE_ERROR');
				CUrlParser::get_instance()->Redirect("admin.php?".$KS_URL->GetUrl(Array('action','city_id')));
			}
		}
		else
		{
			return $this->CountryForm(intval($arFields['id']));
		}
	}

	function Run()
	{
		switch($_REQUEST['action'])
		{
			case 'import_countries':
				return $this->ImportCountriesForm();
			break;
			case 'do_import_countries':
				return $this->DoImportCountriesForm();
			break;
			case 'cities':
				return $this->TableCities(intval($_REQUEST['country_id']));
			break;
			case 'edit_city':
				return $this->CityForm(intval($_REQUEST['city_id']),intval($_REQUEST['country_id']));
			break;
			case 'save_city':
				return $this->SaveCity();
			break;
			case 'edit_country':
				return $this->CountryForm(intval($_REQUEST['country_id']));
			break;
			case 'save_country':
				return $this->SaveCountry();
			break;
		}
		return $this->TableCountries();
	}
}
