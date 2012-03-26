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

include_once MODULES_DIR.'/main/libs/class.CBaseAPI.php';

//==================== Блок констант для уровней доступа ==============================
define('KS_ACCESS_BANNERS_FULL',0);
define('KS_ACCESS_BANNERS_CLIENTS',4);
define('KS_ACCESS_BANNERS_TYPES',5);
define('KS_ACCESS_BANNERS',7);
define('KS_ACCESS_BANNERS_VIEW',8);
define('KS_ACCESS_BANNERS_DENIED',10);
define('DEFAULT_SHOW_RATE',1000);

class CBannersAPI extends CBaseAPI
{
	private $obBanners;
	private $obBannerTypes;
	private $totalThisBanners;
	private $obBannerLinks;
	private $obBannerHits;
	private $obBannerClient;
	private $obBannerTimes;

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
		$this->obBannerTimes=false;
	}

	/**
	 * Метод возвращает объект баннера
	 */
	function Banner()
	{
		if(!$this->obBanners)
		{
			$this->obBanners=new CFieldsObject('banners','/banners','banners');
			$this->obBanners->AddFileField('img');
		}
		return $this->obBanners;
	}

	/**
	 * Метод возвращает баннеропозиции
	 */
	function Type()
	{
		if(!$this->obBannerTypes)
			$this->obBannerTypes=new CFieldsObject('banners_types','/banners','banners');
		return $this->obBannerTypes;
	}

	/**
	 * Метод возвращает линки на пути
	 */
	protected function Link()
	{
		if(!$this->obBannerLinks)
			$this->obBannerLinks=new CObject('banners_links');
		return $this->obBannerLinks;
	}

	/**
	 * Метод возвращает объект обеспечивающий доступ к хитам по баннеру
	 */
	function Hit()
	{
		if(!$this->obBannerHits)
			$this->obBannerHits=new CObject('banners_hits');
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
			$this->obBannerClient=new CObject('banners_clients');
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
		if($arBanner=$this->Banner()->GetRecord(array('id'=>$id)))
		{
			$arPath=$this->Link()->GetList(false,array('banner_id'=>$id));
			if(is_array($arPath)&&count($arPath)>0)
			{
				$arBanner['exc_path']='';
				$arBanner['inc_path']='';
				foreach($arPath as $arRow)
					$arBanner[$arRow['type'].'_path'].=$arRow['path']."\n";
			}
			if($arTimes=$this->Time()->GetList(false,array('banner_id'=>$arBanner['id'])))
			{
				$arBanner['times']=array();
				foreach($arTimes as $arRow)
				{
					if(!array_key_exists($arRow['wday']-1,$arBanner['times']))
						$arBanner['times'][$arRow['wday']-1]=array();
					$arBanner['times'][$arRow['wday']-1][$arRow['hour']-1]=1;
				}
			}
		}
		return $arBanner;
	}

	/**
	 * Метод разбирает список путей, фильтрует его и возвращает в виде подходящем для
	 * сохранения в базу данных
	 */
	private function ParsePath($input)
	{
		if(is_string($input))
			$input=explode("\n",$input);
		if(is_array($input))
		{
			$output=array();
			foreach($input as $sLine)
			{
				$sLine=preg_replace('#[^a-z0-9/-_+?&.]#i','',$sLine);
				if(strlen($sLine)>0) $output[]=$sLine;
			}
			return $output;
		}
		return array();
	}

	/**
	 * Метод сохраняет запись баннера. Обрабатывает ключевые слова и адреса вывода
	 */
	function SaveBanner($prefix="",$data=false)
	{
		if(!$data) $data=$_POST;
		//Пути включаемые
		if(array_key_exists($prefix.'inc_path',$data))
			$arIncPath=$this->ParsePath($data[$prefix.'inc_path']);
		else
			$arIncPath=array();
		//Пути исключаемые
		if(array_key_exists($prefix.'exc_path',$data))
			$arExcPath=$this->ParsePath($data[$prefix.'exc_path']);
		else
			$arExcPath=array();
		//Время показа
		if(array_key_exists('times',$data) && is_array($data['times']))
		{
			$arTimes=array();
			for($i=1;$i<8;$i++)
			{
				$arTimes[$i-1]=array();
				if(array_key_exists($i,$data['times']) && is_array($data['times'][$i]))
				{
					for($j=1;$j<25;$j++)
						if(array_key_exists($j,$data['times'][$i]))
							$arTimes[$i-1][$j-1]=1;
						else
							$arTimes[$i-1][$j-1]=0;
				}
				else
				{
					for($j=1;$j<25;$j++)
						$arTimes[$i-1][$j-1]=0;
				}
			}
		}
		else
		{
			$arTimes=array();
			for($i=1;$i<8;$i++)
				for($j=1;$j<25;$j++)
					$arTimes[$i-1][$j-1]=1;
		}
		//Собственно баннер
		if($id=$this->Banner()->Save($prefix,$data))
		{
			$this->Link()->DeleteItems(array('banner_id'=>$id));
			foreach($arIncPath as $sPath)
			{
				$arFields=array(
					'banner_id'=>$id,
					'path'=>$sPath,
					'type'=>'inc',
				);
				$this->Link()->Save('',$arFields);
			}
			foreach($arExcPath as $sPath)
			{
				$arFields=array(
					'banner_id'=>$id,
					'path'=>$sPath,
					'type'=>'exc',
				);
				$this->Link()->Save('',$arFields);
			}
			//Времечко
			$this->Time()->DeleteItems(array('banner_id'=>$id));
			for($i=1;$i<8;$i++)
				for($j=1;$j<25;$j++)
				{
					if($arTimes[$i-1][$j-1]==1)
					{
						$arFields=array(
							'banner_id'=>$id,
							'wday'=>$i,
							'hour'=>$j,
						);
						$this->Time()->Save('',$arFields);
					}
				}
			return $id;
		}
		return false;
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
			$this->Link()->DeleteItems(array('banner_id'=>$id));
			$this->Hit()->DeleteItems(array('banner_id'=>$id));
			$this->Banner()->DeleteItems(array('id'=>$id));
		}
	}

	/**
	 * Метод добавляет баннеропоказ
	 */
	function AddView($bannerId)
	{
		$now=mktime(date('H'),0,0);
		$arHit=$this->Hit()->GetRecord(array('banner_id'=>$bannerId,'date'=>$now));
		if(is_array($arHit))
			$this->Hit()->Update($arHit['id'],array('views'=>$arHit['views']+1));
		else
			$this->Hit()->Save('',array('banner_id'=>$bannerId,'date'=>$now,'hits'=>0,'views'=>1));
	}

	/**
	 * Метод добавляет баннероклик
	 */
	function AddHit($bannerId)
	{
		$now=floor(time()/3600)*3600;
		$arHit=$this->Hit()->GetRecord(array('banner_id'=>$bannerId,'date'=>intval($now)));
		if(is_array($arHit))
			$this->Hit()->Update($arHit['id'],array('hits'=>$arHit['hits']+1));
		else
			$this->Hit()->Save('',array('banner_id'=>$bannerId,'date'=>$now,'hits'=>1,'views'=>1));
	}

	/**
	 * Метод возвращает статистику по показам и кликам по баннеру
	 * @param $bannerId - номер баннера
	 * @param $dateFrom - с какой даты выводить статистику
	 * @param $dateTo - по какую дату выводить статистику
	 * @return array
	 */
	function GetStatistics($bannerId,$dateFrom=false,$dateTo=false)
	{
		if($this->Banner()->GetRecord(array('id'=>$bannerId)))
		{
			if($dateFrom==false && $dateTo==false)
			{
				$dateFrom=strtotime("7 days ago");;
				$dateTo=time();
			}
			elseif($dateFrom==false && $dateTo>0)
			{
				$dateFrom=$dateTo-strtotime("7 days ago");
			}
			elseif($dateTo==false && $dateFrom>0)
			{
				$dateTo=$dateFrom+strtotime("7 days ago");
			}
			if($dateFrom>$dateTo)
			{
				$tmp=$dateFrom;
				$dateFrom=$dateTo;
				$dateTo=$tmp;
			}
			$arFilter=array(
				'banner_id'=>$bannerId,
				'>=date'=>$dateFrom,
				'<=date'=>$dateTo,
			);
			$arList=$this->Hit()->GetList(array('date'=>'desc'),$arFilter);
			return array('list'=>$arList,'date_from'=>$dateFrom,'date_to'=>$dateTo);
		}
		return false;
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
			$arTypes=$this->Type()->GetList(false,array('active'=>1));
			foreach($arTypes as $arBT)
				$this->arBannerTypes[$arBT['text_ident']]=$arBT;
		}
		$arType=$this->arBannerTypes[$type];
		if(is_array($arType))
		{
			$sLinkTable=$this->Link()->GetTable();
			$arFilter=array(
				'<?'.$this->Banner()->GetTable().'.id'=>$sLinkTable.'.banner_id',
				'type_id'=>$arType['id'],
				//$this->obBannerLinks->sTable.'.type'=>'inc',
				'active'=>1,
				'AND'=>array(
					array('OR'=>array('>=active_to'=>time(),'active_to'=>0)),
					array('OR'=>array('<=active_from'=>time(),'active_from'=>0)),
				)
			);
			$arSelect=$this->Banner()->GetFields();
			$arSelect[$sLinkTable.'.path']='path';
			$arSelect[$sLinkTable.'.type']='path_type';
			$bOldMode=$this->Banner()->SetKeyMode(true);
			$arBanners=$this->Banner()->GetList(array($sLinkTable.'.path'=>'desc'),$arFilter,false,$arSelect);
			if(is_array($arBanners)&&count($arBanners)>0)
			{
				$path=$KS_URL->GetPath();
				$maxLength=0;
				foreach($arBanners as $arBanner)
				{
					if($arBanner['path_type']=='inc' && $arBanner[$sLinkTable.'_path']!='')
					{
						//Если строка - регулярное выражение
						if(substr($arBanner[$sLinkTable.'_path'],0,1)=='#')
						{
							if(preg_match($arBanner[$sLinkTable.'_path'],$path))
							{
								$arResultBanners[1][$arBanner['id']]=$arBanner;
								if($maxLength<1) $maxLength=1;
							}
						}
						elseif(strpos($path,$arBanner[$sLinkTable.'_path'])!==false)
						{
							$arResultBanners[strlen($arBanner[$sLinkTable.'_path'])][$arBanner['id']]=$arBanner;
							if(strlen($arBanner[$sLinkTable.'_path'])>$maxLength) $maxLength=strlen($arBanner[$sLinkTable.'_path']);
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
						if($arBanner['save_stats']==1)
						{
							$this->AddView($arBanner['id']);
						}
						$_SESSION['banner_views'][$arType['text_ident']][$arBanner['id']]=time();
					}
					return $arResultBanners;
				}
			}
			$this->Banner()->SetKeyMode($bOldMode);
		}
		return false;
	}

	/**
	 * Метод выполняет расчёт уменьшения коэффициента показа баннера
	 * @param $arBanner - массив описания баннера подготавливаемый методом CBannersAPI::SelectBanner2()
	 * @return integer - число на которое следует уменьшить коэффициент в случае показа данного баннера
	 */
	private function RecountBannerCoeff($arBanner)
	{
		global $KS_MODULES;
		if(array_key_exists('inc',$arBanner) && count($arBanner['inc'])>0)
			foreach($arBanner['inc'] as $sPath)
				if($KS_MODULES->CheckPath($sPath))
					return 3;
		return 5;
	}

	/**
	 * Метод выбирает баннер используя прогрессивный алгоритм
	 */
	function SelectBanner2($type)
	{
		global $KS_MODULES,$KS_EVENTS_HANDLER;
		$KS_URL=CUrlParser::get_instance();
		if(!is_array($this->arBannerTypes))
		{
			$arTypes=$this->Type()->GetList(false,array('active'=>1));
			foreach($arTypes as $arBT)
				$this->arBannerTypes[$arBT['text_ident']]=$arBT;
		}
		$arType=$this->arBannerTypes[$type];
		if(is_array($arType))
		{
			$sLinkTable=$this->Link()->GetTable();
			$sBannerTable=$this->Banner()->GetTable();
			$sTimeTable=$this->Time()->GetTable();
			$wDay=date('w');
			if($wDay==0) $wDay=7;
			$h=date('G')+1;
			$arFilter=array(
				'<?'.$sBannerTable.'.id'=>$sLinkTable.'.banner_id',
				'type_id'=>$arType['id'],
				'active'=>1,
				'?'.$sTimeTable.'.banner_id'=>$sBannerTable.'.id',
				$sTimeTable.'.wday'=>$wDay,
				$sTimeTable.'.hour'=>$h,
				'AND'=>array(
					array('OR'=>array('>=active_to'=>time(),'active_to'=>0)),
					array('OR'=>array('<=active_from'=>time(),'active_from'=>0)),
				)
			);
			$arSelect=$this->Banner()->GetFields();
			$arSelect[$sLinkTable.'.path']='path';
			$arSelect[$sLinkTable.'.type']='path_type';
			$bOldMode=$this->Banner()->SetKeyMode(true);
			if($arBannersTmp=$this->Banner()->GetList(array('show_rate'=>'desc'),$arFilter,false,$arSelect))
			{
				$arBanners=array();
				$arExcluded=array();
				foreach($arBannersTmp as $arBanner)
				{
					if(in_array($arBanner['id'],$arExcluded)) continue;
					if($arBanner['path_type']=='exc' && $KS_MODULES->CheckPath($arBanner['path'],false))
					{
						$arExcluded[]=$arBanner['id'];
						continue;
					}
					if(!array_key_exists($arBanner['id'],$arBanners))
					{
						$arBanners[$arBanner['id']]=$arBanner;
						$arBanners[$arBanner['id']]['path']=array();
						$arBanners[$arBanner['id']]['path']['inc']=array();
						if($arBanner['path_type']=='inc')
							$arBanners[$arBanner['id']]['path']['inc'][]=$arBanner['path'];
					}
					else
					{
						if($arBanner['path_type']=='inc')
							$arBanners[$arBanner['id']]['path']['inc'][]=$arBanner['path'];
					}
				}
			}
			if(count($arBanners)>0)
			{
				$arBanner=array_shift($arBanners);
				if($KS_EVENTS_HANDLER->HasHandler('banners','onRecountShowCoeff'))
				{
					$arCheckArray=array(
						'banner'=>$arBanner,
					);
					$iCoeff=$KS_EVENTS_HANDLER->Execute("banners", "onRecountShowCoeff",$arCheckArray);
				}
				else
				{
					$iCoeff=$this->RecountBannerCoeff($arBanner);
				}
				if($arBanner['show_rate']-$iCoeff>0)
				{
					$this->Banner()->Update($arBanner['id'],array('show_rate'=>$arBanner['show_rate']-$iCoeff));
				}
				else
				{
					//Коэффициент самого удачного баннера упал до нуля, по идее надо сбросить коэффициент всех баннеров на данную позицию
					$this->Banner()->Update(array('type_id'=>$arBanner['type_id']),array('show_rate'=>DEFAULT_SHOW_RATE));
				}
				if($arBanner['save_stats']==1)
					$this->AddView($arBanner['id']);
				return $arBanner;
			}
			$this->Banner()->SetKeyMode($bOldMode);
		}
		return false;
	}

	/**
	 * Метод выполняет пересчёт коэффициента показа баннера
	 * @param $arBanner array - массив описания баннера
	 */
	function RecountCoeff($arBanner)
	{
		global $KS_EVENTS_HANDLER;
		if($KS_EVENTS_HANDLER->HasHandler('banners','onRecountShowCoeff'))
		{
			$arCheckArray=array(
				'banner'=>$arBanner,
			);
			$iCoeff=$KS_EVENTS_HANDLER->Execute("banners", "onRecountShowCoeff",$arCheckArray);
		}
		else
		{
			$iCoeff=$this->RecountBannerCoeff($arBanner);
		}
		if($arBanner['show_rate']-$iCoeff>0)
		{
			$this->Banner()->Update($arBanner['id'],array('show_rate'=>$arBanner['show_rate']-$iCoeff));
			$arBanner['show_rate']-=$iCoeff;
		}
		else
		{
			//Коэффициент самого удачного баннера упал до нуля, по идее надо сбросить коэффициент всех баннеров на данную позицию
			$this->Banner()->Update(array('type_id'=>$arBanner['type_id']),array('show_rate'=>DEFAULT_SHOW_RATE));
			$arBanner['show_rate']=DEFAULT_SHOW_RATE;
		}
		return $arBanner;
	}

	/**
	 * Метод выбирает баннеры используя прогрессивный алгоритм
	 */
	function SelectBanners($type,$count)
	{
		global $KS_MODULES,$KS_EVENTS_HANDLER;
		$KS_URL=CUrlParser::get_instance();
		if(!is_array($this->arBannerTypes))
		{
			$arTypes=$this->Type()->GetList(false,array('active'=>1));
			foreach($arTypes as $arBT)
				$this->arBannerTypes[$arBT['text_ident']]=$arBT;
		}
		$arType=$this->arBannerTypes[$type];
		if(is_array($arType))
		{
			$sLinkTable=$this->Link()->GetTable();
			$sBannerTable=$this->Banner()->GetTable();
			$sTimeTable=$this->Time()->GetTable();
			$wDay=date('w');
			if($wDay==0) $wDay=7;
			$h=date('G')+1;
			$arFilter=array(
				'<?'.$sBannerTable.'.id'=>$sLinkTable.'.banner_id',
				'type_id'=>$arType['id'],
				'active'=>1,
				'?'.$sTimeTable.'.banner_id'=>$sBannerTable.'.id',
				$sTimeTable.'.wday'=>$wDay,
				$sTimeTable.'.hour'=>$h,
				'AND'=>array(
					array('OR'=>array('>=active_to'=>time(),'active_to'=>0)),
					array('OR'=>array('<=active_from'=>time(),'active_from'=>0)),
				)
			);
			$arSelect=$this->Banner()->GetFields();
			$arSelect[$sLinkTable.'.path']='path';
			$arSelect[$sLinkTable.'.type']='path_type';
			$bOldMode=$this->Banner()->SetKeyMode(true);
			if($arBanners=$this->Banner()->GetList(array('show_rate'=>'desc'),$arFilter,$count,$arSelect))
			{
				$bHasHandler=$KS_EVENTS_HANDLER->HasHandler('banners','onRecountShowCoeff');
				$sPath=$KS_MODULES->GetSitePath('banners');
				foreach($arBanners as $key=>$arBanner)
				{
					if($bHasHandler)
					{
						$arCheckArray=array(
							'banner'=>$arBanner,
						);
						$iCoeff=$KS_EVENTS_HANDLER->Execute("banners", "onRecountShowCoeff",$arCheckArray);
					}
					else
					{
						$iCoeff=$this->RecountBannerCoeff($arBanner);
					}
					if($arBanner['show_rate']-$iCoeff>0)
					{
						$this->Banner()->Update($arBanner['id'],array('show_rate'=>$arBanner['show_rate']-$iCoeff));
						$arBanner['show_rate']-=$iCoeff;
					}
					else
					{
						//Коэффициент самого удачного баннера упал до нуля, по идее надо сбросить коэффициент всех баннеров на данную позицию
						$this->Banner()->Update(array('type_id'=>$arBanner['type_id']),array('show_rate'=>DEFAULT_SHOW_RATE));
						$arBanner['show_rate']=DEFAULT_SHOW_RATE;
					}
					$arBanner['path']=$sPath;
					if($arBanner['save_stats']==1)
						$this->AddView($arBanner['id']);
					$arBanners[$key]=$arBanner;
				}
				$this->Banner()->SetKeyMode($bOldMode);
				return $arBanners;
			}
			$this->Banner()->SetKeyMode($bOldMode);
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

