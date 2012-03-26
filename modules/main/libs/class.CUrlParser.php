<?php
/**
 * Класс обеспечивает работу с адресами системы, переделан в singleton объект
 *
 * @since 1.0
 *
 * @author BlaDe39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

class CUrlParser
{
	private $items;
	private $query;
	private $path;
	static protected $bHasHash;
	static private $obSelf;

	static function get_instance()
	{
		if(!is_object(self::$obSelf))
		{
			self::$obSelf=new CUrlParser();
			self::$obSelf->Init();
		}
		return self::$obSelf;
	}

	protected function Init()
	{
		global $smarty;
		$this->items=Array();
		$this->query=$_SERVER['QUERY_STRING'];
		if(array_key_exists('path',$_GET) && strlen($_GET['path'])>0)
		{
			$this->path=$_GET['path'];
		}
		elseif (($pos=strpos($_SERVER['REQUEST_URI'],'?'))>0)
		{
			$this->path=substr($_SERVER['REQUEST_URI'],0,$pos);
		}
		else
		{
			$this->path=$_SERVER['REQUEST_URI'];
		}
		$params=explode("&",$this->query);
		foreach($params as $item)
		{
			$ar=explode("=",$item);
			if($ar[0]!='')
			{
				$this->items[$ar[0]]=$ar[1];
			}
		}
		$smarty->register_function("get_url",array($this,"_smarty_get_url"));
		$smarty->register_function("get_getform",array($this,"_smarty_get_getform"));
		$smarty->register_function("get_get",array($this,"_smarty_get_get"));
		$smarty->register_function("get_posturl",array($this,"_smarty_get_posturl"));
		$smarty->register_function("gen_post_hash",array($this,"_smarty_gen_post_hash"));
		$smarty->register_function("redirect",array($this,"_smarty_redirect"));
	}

	/**
	 * Метод выполняет обработку URL адреса, отделяет его основную часть от get переменных
	 * и формирует данные находящиеся в get параметрах
	 * @param $sUrl - адрес который требуется разобрать. Если не указан - подставляется $_SERVER['REQUEST_URI']
	 */
	static function ParseUrl($sUrl=false)
	{
		if(!$sUrl) $sUrl=$_SERVER['REQUEST_URI'];
		if(preg_match('#^(([a-z]{3,4})://([a-z0-9\.\-]+))?/(.*)$#i',$sUrl,$matches))
		{
			$sUrl='/'.$matches[4];
			$sDomain=$matches[3];
			$sProtocol=$matches[2];
		}
		else
		{
			$sProtocol='http';
			$sDomain=$_SERVER['SERVER_NAME'];
		}
		$arGetResult=array();
		if(($pos=strpos($sUrl,'?'))>0)
		{
			$sQuery=substr($sUrl,$pos+1);
			$sUrl=substr($sUrl,0,$pos);
			$arGet=explode('&',$sQuery);
			foreach($arGet as $arOneGet)
			{
				$arItem=explode('=',$arOneGet);
				$arItem[0]=urldecode($arItem[0]);
				if(preg_match('#^(.*)\[(.*)\]$#i',$arItem[0],$matches))
				{
					if($matches[2]=='')
					{
						if(!isset($arGetResult[$matches[1]])) $arGetResult[$matches[1]]=array();
						array_push($arGetResult[$matches[1]],urldecode($arItem[1]));
					}
					else
					{
						$arGetResult[$matches[1]][$matches[2]]=urldecode($arItem[1]);
					}
				}
				else
				{
					if(is_array($arItem) && count($arItem)>1)
					{
						$arGetResult[urldecode($arItem[0])]=urldecode($arItem[1]);
					}
				}
			}
		}
		return array(
			'domain'=>$sDomain,
			'protocol'=>$sProtocol,
			'params'=>$arGetResult,
			'url'=>$sUrl,
		);
	}

	/**
	 * Метод проверяет переданный хэш поста и возвращает true в случае если хэш верен
	 * и false если нет
	 * @param $hash - необязательный параметр, значение хэша, если не указан используется стандартный
	 * отключил т.к. если на странице несколько форм проиходит обновление хэша.
	 */
	function CheckPostHash($hash=false)
	{
		return true;
		if(!CUrlParser::$bHasHash) return true;
		if(!$hash) $hash=$_POST['post_hash'];
		return $hash==$_SESSION['POST_HASH'];
	}

	/**
	 * Метод генерирует строку смарти для вывода случайного хэша данного пост запроса
	 * для защиты от повторных запросов
	 */
	function _smarty_gen_post_hash($params)
	{
		global $USER;
		if(!CUrlParser::$bHasHash)
		{
			$_SESSION['POST_HASH']=md5(serialize($USER->GetUserData()).time().rand(0,1000));
			CUrlParser::$bHasHash=true;
		}
		if($params['name']=='') $params['name']="post_hash";
		return '<input type="hidden" name="'.$params['name'].'" value="'.$_SESSION['POST_HASH'].'"/>';
	}

	function Set($param_name,$value)
	{
		$this->items[$param_name]=$value;
	}

	function SetPath($path)
	{
		$this->path = $path;
	}

	function Get($param_name)
	{
		return $this->items[$param_name];
	}


	/*Возвращает для смарти обработанную строку*/
	/*todo: посмотреть можно ли оптимизировать :)*/
	function _smarty_get_url($params)
	{
		$arParams=$this->ParseParams($params);
		if(count($arParams)>0)
		{
			$path="?".join("&amp;",$arParams);
		}
		$sRootPath=$this->path;
		if(isset($params['part']) && is_numeric($params['part']))
		{
			$params['part']=intval($params['part']);
			$arPath=explode('/',$this->path);
			if(is_array($arPath) && count($arPath)>0)
			{
				$arPath=array_slice($arPath,0,$params['part']+1);
			}
			$sRootPath=join('/',$arPath).'/';
		}
        return $sRootPath.$path;
	}

	/**
	 * Метод выполняет обработку параметров get
	 * @param $params array новые параметры get строки, а также _CLEAR массив параметров для удаления
	 * @param $item mixed элементы для которых проводить обработку, если не указано, то берется текущий массив ($this->items)
	 */
	private function ParseParams($params,$items=false)
	{
		$clear=0;
		$clearList=array();
		if (array_key_exists('_CLEAR',$params))
		{
			$clear=$params['_CLEAR'];
			$clear="/^$clear$/";
			$clear=str_replace(" ","$/ /^",$clear);
			$clearList=explode(" ",$clear);
			unset($params['_CLEAR']);
			$clear=1;
		}
		if(!in_array("/^ajaxMode$/",$clearList)){
			$clearList[]="/^ajaxMode$/";
			$clear=1;
		}
		if(!is_array($items))
			$items=$this->items;
        foreach ($params as $key=>$value)
        	$items[$key]=$value;
        //Подчищаем команду юзверя, если она не указанна принудительно
        if(!array_key_exists('CU_ACTION',$params)) unset($items['CU_ACTION']);
        $uri=array();
        foreach($items as $key=>$item)
		{
			$res=0;
			if ($clear)
				foreach($clearList as $pattern)
				{
					$res=preg_match($pattern,$key);
					if ($res==1) break;
				}
			if($key!='path' && $key!='part')
				if ($res==0)
				{
					if(is_array($item))
						foreach($item as $value)
							$uri[]= urlencode(urldecode($key)).'[]='.urlencode(urldecode($value));
					else
						$uri[]= urlencode(urldecode($key)).'='.urlencode(urldecode($item));
				}
		}
		return $uri;
	}

	/**
	 * Метод пытается выполнить редирект по адресу хранящемуся в $_SERVER['backurl'];
	 */
	function GoBack()
	{
		if(array_key_exists('backurl',$_SESSION) && $_SESSION['backurl']!='')
		{
			$arUrl=$this->ParseUrl($_SESSION['backurl']);
			$arParams=array();
			if(is_array($arUrl['params']))
			{
				$arParams=$this->ParseParams(array('_CLEAR'=>'CU_ACTION'),$arUrl['params']);
			}
			$path=$arUrl['url'].(count($arParams)>0?'?'.join('&amp;',$arParams):'');
			unset($_SESSION['backurl']);
			if($path!='')
			{
				CUrlParser::get_instance()->Redirect($path);
			}
		}
	}

	/**
	 * Метод возвращает список параметров get в виде hidden полей input
	 */
	function _smarty_get_getform($params)
	{
		$arParams=$this->ParseParams($params);
		$path='';
		foreach($arParams as $value)
		{
			$value = explode('=',$value);
			$path.='<input type="hidden" name="'.$value[0].'" value="'.$value[1].'"/>'."\n";
		}
        return $path;
	}

	/*Возвращает для смарти обработанную строку GET запроса без адреса страницы*/
	/*todo: посмотреть можно ли оптимизировать :)*/
	function _smarty_get_get($params)
	{
		$arParams=$this->ParseParams($params);
		if(count($arParams)>0)
		{
			$path=join("&",$arParams);
		}
        return $path;
	}

	function _smarty_get_posturl($params)
	{
		$newpath=$this->_smarty_get_url($params);
		$newpath=explode('?',$newpath);
		if($_GET['path']!='') $newpath[0]=$_GET['path'];
		$path='/index.php?path='.$newpath[0].'&amp;'.$newpath[1];
		return $path;
	}

	/**
	 * Метод выполняет редирект страницы по команде из шаблона
	 */
	function _smarty_redirect($params)
	{
		if($params['url']=='') $params['url']=$this->GetUrl();
		$this->redirect($params['url']);
	}

	function GetUrl($clearList=0)
	{
		$uri=Array();
		if (is_array($clearList)) {$clear=1;} else {$clear=0;}
		foreach($this->items as $key=>$item)
		{
			if (($clear)&&in_array($key,$clearList)) continue;
			$uri[]=urlencode(urldecode($key))."=".urlencode(urldecode($item));
		}
		return join("&",$uri);
	}

	/**
	 * Метод возвращает путь к текущей странице
	 */
	function GetPath()
	{
		return $this->path;
	}

	/**
	 * Функция redirect выполняет перевод на страницу указанную в параметре. Переход осуществляется
	 * путем установки заголовка Location.
	 * @param $URL -- урл на который требуется перейти.
	 */
	function redirect($URL='')
	{
		$ar = $this->AjaxReq();
		if(array_key_exists('ajaxreq',$ar))
		{
			 $this->GetJS($ar);
			 die();
		}
		if($URL=='') $URL=$this->path;
		header("Location: $URL");
   		die();
 	}

	function AjaxReq()
	{
		$array = array();
		$query = $_SERVER['QUERY_STRING'];
		$params=explode("&",$query);
		foreach($params as $item)
		{
			$ar=explode("=",$item);
			if($ar[0]!='')
			{
				if(($ar[0] == "ajaxreq") || ($ar[0] == "module" && strlen($ar[1]) > 0) || ($ar[0] == "liid"))
				{
					$array[$ar[0]]=$ar[1];
				}
			}
		}
		return $array;
	}

	function GetJS($array)
	{
		echo "<html>
		<script type=\"text/javascript\">
			function CloseReload()
			{
				//alert(self.parent.MyNode);
				self.parent.nextStep('".$array["module"]."','".$array["ajaxreq"]."','".$array["liid"]."',true);
				self.parent.kstb_remove();
			}
		</script>
		<body onLoad=\"return CloseReload();\"></body>
		</html>";
	}
}

