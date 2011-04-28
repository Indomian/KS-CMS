<?php
/**
 * @file class.CBannersApi.php
 * Файл с базовыми функциями модуля Баннеры
 * Файл проекта kolos-cms.
 *
 * Создан 07.12.2009
 *
 * @author blade39
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

//==================== Блок констант для уровней доступа ==============================
define('KS_ACCESS_BANNERS_FULL',0);
define('KS_ACCESS_BANNERS_CLIENTS',4);
define('KS_ACCESS_BANNERS_TYPES',5);
define('KS_ACCESS_BANNERS',7);
define('KS_ACCESS_BANNERS_VIEW',8);
define('KS_ACCESS_BANNERS_DENIED',10);

class CBannersAPI extends CBaseAPI
{
	private $obBanners;
	private $obBannerTypes;
	private $totalThisBanners;
	private $obBannerLinks;
	private $obBannerHits;
	private $obBannerClient;

	private $arBannerTypes;

	static private $instance;

	/**
	 * This implements the 'singleton' design pattern
	 *
	 * @return object CEShopAPI The one and only instance
	 */
	static function get_instance()
	{
		if (!self::$instance)
		{
			self::$instance = new CBannersAPI();
			self::$instance->startup();  // init AFTER object was linked with self::$instance
		}
		return self::$instance;
	}

	function __construct()
	{
	}

	/**
	 * Метод отвечает за инициализацию объекта класса
	 */
	private function startup()
	{
		$this->obBanners=false;
		$this->obBannerTypes=false;
		$this->obBannerLinks=false;
		$this->obBannerHits=false;
		$this->arBannerTypes=false;
		$this->obBannerClient=false;
	}

	/**
	 * Метод возвращает объект баннера
	 */
	function Banner()
	{
		if(!$this->obBanners)
		{
			$this->obBanners=new CFieldsObject('banners','/banners','banners');
		}
		return $this->obBanners;
	}

	/**
	 * Метод возвращает баннеропозиции
	 */
	function Type()
	{
		if(!$this->obBannerTypes)
		{
			$this->obBannerTypes=new CFieldsObject('banners_types','/banners','banners');
		}
		return $this->obBannerTypes;
	}

	/**
	 * Метод возвращает линки на пути
	 */
	protected function Link()
	{
		if(!$this->obBannerLinks)
		{
			$this->obBannerLinks=new CObject('banners_links');
		}
		return $this->obBannerLinks;
	}

	/**
	 * Метод возвращает объект обеспечивающий доступ к хитам по баннеру
	 */
	function Hit()
	{
		if(!$this->obBannerHits)
		{
			$this->obBannerHits=new CObject('banners_hits');
		}
		return $this->obBannerHits;
	}

	/**
	 * Метод возвращает объект обеспечивающий доступ к датам вывода баннеров
	 */
	function Time()
	{
		if(!$this->obBannerTimes)
		{
			$this->obBannerTimes=new CObject('banners_times');
		}
		return $this->obBannerTimes;
	}

	/**
	 * Метод возвращает объект обеспечивающий доступ к рекламной кампании
	 */
	function Client()
	{
		if(!$this->obBannerClient)
		{
			$this->obBannerClient=new CObject('banners_clients');
		}
		return $this->obBannerClient;
	}

	/**
	 * Метод возвращает список полей типов баннеров
	 */
	function GetTypesFields()
	{
		return $this->Type()->GetFields();
	}

	/**
	 * Метод возвращает список полей типов баннеров
	 */
	function GetBannerFields()
	{
		return $this->Banner()->GetFields();
	}

	/**
	 * Метод возвращает баннер по его номеру
	 */
	function GetBanner($id)
	{
		$arBanner=$this->Banner()->GetRecord(array('id'=>$id));
		if(is_array($arBanner))
		{
			$arPath=$this->Link()->GetList(false,array('banner_id'=>$id));
			if(is_array($arPath)&&count($arPath)>0)
			{
				foreach($arPath as $arRow)
				{
					$arBanner[$arRow['type'].'_path'].=$arRow['path']."\n";
				}
			}
		}
		return $arBanner;
	}
	/**
	 * Метод сохраняет запись баннера. Обрабатывает ключевые слова и адреса вывода
	 */
	function SaveBanner($prefix="",$data=false)
	{
		if(!$data) $data=$_POST;
		$arIncPath=explode("\n",$data[$prefix.'inc_path']);
		$arExcPath=explode("\n",$data[$prefix.'exc_path']);
		$arIncKeywords=explode(',',$data[$prefix.'inc_keywords']);
		$arExcKeywords=explode(',',$data[$prefix.'exc_keywords']);
		foreach($arIncKeywords as $key=>$sKeyword)
		{
			$arIncKeywords[$key]=trim($sKeyword);
		}
		foreach($arExcKeywords as $key=>$sKeyword)
		{
			$arExcKeywords[$key]=trim($sKeyword);
		}
		$data[$prefix.'inc_keywords']=join(', ',$arIncKeywords);
		$data[$prefix.'exc_keywords']=join(', ',$arExcKeywords);
		$this->obBanners->AddFileField('img');
		$id=$this->obBanners->Save($prefix,$data);
		if($id>0)
		{
			$this->obBannerLinks->DeleteItems(array('banner_id'=>$id));
			foreach($arIncPath as $sPath)
			{
				$sPath=trim($sPath);
				$sPath=str_replace(array('\n','\r'),'',$sPath);
				if($sPath!='')
				{
					$arFields=array(
						'banner_id'=>$id,
						'path'=>$sPath,
						'type'=>'inc',
					);
					$this->obBannerLinks->Save('',$arFields);
				}
			}
			/**
			 * @todo Разобраться с исключением баннеров

			foreach($arExcPath as $sPath)
			{
				$arFields=array(
					'banner_id'=>$id,
					'path'=>$sPath,
					'type'=>'exc',
				);
				$this->obBannerLinks->Save('',$arFields);
			}*/
		}
	}

	/**
	 * Метод выполняет удаление баннеров (соответственно, все записи о данном баннере)
	 * @param $id mixed - массив или номер типа на удаление
	 * @return
	 */
	function DeleteBanners($ids)
	{
		if(is_numeric($ids)) $ids=array($ids);
		foreach($ids as $id)
		{
			$this->obBannerLinks->DeleteItems(array('banner_id'=>$id));
			$this->obBannerHits->DeleteItems(array('banner_id'=>$id));
			$this->obBanners->DeleteItems(array('id'=>$id));
		}
	}

	/**
	 * Метод добавляет баннеропоказ
	 */
	function AddView($bannerId)
	{
		global $ks_db;
		$now=mktime(date('H'),0,0);
		$arHit=$this->obBannerHits->GetRecord(array('banner_id'=>$bannerId,'date'=>$now));
		if(is_array($arHit))
		{
			$this->obBannerHits->Update($arHit['id'],array('views'=>$arHit['views']+1));
		}
		else
		{
			$this->obBannerHits->Save('',array('banner_id'=>$bannerId,'date'=>$now,'hits'=>0,'views'=>1));
		}
	}

	/**
	 * Метод добавляет баннероклик
	 */
	function AddHit($bannerId)
	{
		$now=floor(time()/3600)*3600;
		$arHit=$this->obBannerHits->GetRecord(array('banner_id'=>$bannerId,'date'=>intval($now)));
		if(is_array($arHit))
		{
			$this->obBannerHits->Update($arHit['id'],array('hits'=>$arHit['hits']+1));
		}
		else
		{
			$this->obBannerHits->Save('',array('banner_id'=>$bannerId,'date'=>$now,'hits'=>1,'views'=>1));
		}
	}

	/**
	 * Метод производит выбор баннера из базы данных и выводит его на сайте
	 * @param $type - код баннеропозиции
	 * @param $count - количество баннеров которые необходимо выбрать
	 * @todo разобраться с типом адреса размещения баннера exclude
	 */
	function SelectBanner($type,$count=1)
	{
		global $KS_MODULES,$KS_URL;
		if(!is_array($this->arBannerTypes))
		{
			$arTypes=$this->obBannerTypes->GetList(false,array('active'=>1));
			foreach($arTypes as $arBT)
				$this->arBannerTypes[$arBT['text_ident']]=$arBT;
		}
		$arType=$this->arBannerTypes[$type];
		if(is_array($arType))
		{
			$arFilter=array(
				'<?'.$this->obBanners->sTable.'.id'=>$this->obBannerLinks->sTable.'.banner_id',
				'type_id'=>$arType['id'],
				//$this->obBannerLinks->sTable.'.type'=>'inc',
				'active'=>1,
				'AND'=>array(
					array('OR'=>array('>=active_to'=>time(),'active_to'=>0)),
					array('OR'=>array('<=active_from'=>time(),'active_from'=>0)),
				)

			);
			$arSelect=array(
				'id',
				'text_ident',
				'title',
				'img',
				'content',
				'href',
				$this->obBannerLinks->sTable.'.path',
			);
			$arBanners=$this->obBanners->GetList(array($this->obBannerLinks->sTable.'.path'=>'desc'),$arFilter,false,$arSelect);
			if(is_array($arBanners)&&count($arBanners)>0)
			{
				$path=$KS_URL->GetPath();
				$maxLength=0;
				foreach($arBanners as $arBanner)
				{
					if($arBanner['type']=='exc') continue;
					if($arBanner[$this->obBannerLinks->sTable.'_path']!='')
					{
						//Если строка - регулярное выражение
						if(substr($arBanner[$this->obBannerLinks->sTable.'_path'],0,1)=='#')
						{
							if(preg_match($arBanner[$this->obBannerLinks->sTable.'_path'],$path))
							{
								$arResultBanners[1][$arBanner['id']]=$arBanner;
								if($maxLength<1) $maxLength=1;
							}
						}
						elseif(strpos($path,$arBanner[$this->obBannerLinks->sTable.'_path'])!==false)
						{
							$arResultBanners[strlen($arBanner[$this->obBannerLinks->sTable.'_path'])][$arBanner['id']]=$arBanner;
							if(strlen($arBanner[$this->obBannerLinks->sTable.'_path'])>$maxLength) $maxLength=strlen($arBanner[$this->obBannerLinks->sTable.'_path']);
						}
					}
					else
					{
						$arResultBanners[0][$arBanner['id']]=$arBanner;
					}
				}
				if(count($arResultBanners)>0)
				{
					$this->totalThisBanners=count($arResultBanners[$maxLength]);
					$arBanners=array();
					$arCookiedBanners=$arResultBanners[$maxLength];
					$arBannersIDs=array();
					$arCIDs=array();
					foreach($arCookiedBanners as $id=>$arBanner)
					{
						$arCIDs[$_SESSION['banner_views'][$arType['text_ident']][$id]][]=$arBanner;
					}
					ksort($arCIDs);
					foreach($arCIDs as $arBIDs)
					{
						$arBanners=array_merge($arBanners,$arBIDs);
					}
					$arBanners=array_slice($arBanners,0,$count);
					$arResultBanners=array();
					foreach($arBanners as $arBanner)
					{
						$arBanner['path']=$KS_MODULES->GetSitePath('banners');
						$arResultBanners[]=$arBanner;
						$_SESSION['banner_views'][$arType['text_ident']][$arBanner['id']]=time();
					}
					return $arResultBanners;
				}
			}
		}
		return false;
	}

	/**
	 * Метод выполняет удаление типов баннеров (соответственно, все баннеры принадлежащие данному типу)
	 * @param $id mixed - массив или номер типа на удаление
	 * @return
	 */
	function DeleteTypes($ids)
	{
		if(is_numeric($ids)) $ids=array($ids);
		foreach($ids as $id)
		{
			if(is_numeric($id))
			{
				$arBanners=$this->Banner()->GetList(false,array('type_id'=>$id),false,array('id'));
				if(is_array($arBanners)&&count($arBanners)>0)
				{
					$arBIds=array();
					foreach($arBanners as $arRow)
					{
						$arBIds[]=$arRow['id'];
					}
					$this->DeleteBanners($arBIds);
				}
				$this->Type()->DeleteItems(array('id'=>$id));
			}
			else
			{
				throw new CDataError('BANNERS_ID_NOT_INTEGER',0,$id);
			}
		}
		return true;
	}

	/**
	 * Метод выполняет удаление рекламных кампаний, при этом все баннеры привязанные к указанным компаниям, получают
	 * привязку к 0-ой рекламной кампании
	 */
	function DeleteClients($ids)
	{
		if(is_numeric($ids)) $ids=array($ids);
		foreach($ids as $id)
		{
			if(is_numeric($id))
			{
				$this->Banner()->Update(array('client_id'=>$id),array('client_id'=>0));
				$this->Client()->DeleteItems(array('id'=>$id));
			}
			else
			{
				throw new CDataError('BANNERS_ID_NOT_INTEGER',0,$id);
			}
		}
		return true;
	}
}

