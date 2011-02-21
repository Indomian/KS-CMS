<?php
/**
 * @file class.CAjax.php
 * Класс для работы виджетов в режиме аякс
 * Файл проекта kolos-cms.
 *
 * Создан 27.05.2009
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 1.0
 *
 * @since v2.3-rc2
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

/**
 * Абстрактный класс для всех реализаций аякса
 */
abstract class CAjaxCommon extends CBaseObject
{
	static $iIncludeCount; 	/*<!Учитывает количество вызовов этого класса*/
	static $bIsLoad;	/*<!Ключ, показывает загружена библиотека аякса или нет*/
	var $iIndex;			/*<!Индекс текущего вызова*/

	/**
	 * Конструктор инициализирует объект класса и увеличивает счетчик вызова
	 */
	function __construct()
	{
		CAjaxCommon::$iIncludeCount++;
		CAjaxCommon::$bIsLoad=false;
		$this->iIndex=CAjaxCommon::$iIncludeCount;
	}

	/**
	 * Абстрактный метод который реализует включение аякса в страницу, подключаются необходимые библиотеки
	 */
	abstract function Load();
}

/**
 * Класс реализует работу с аякс.
 */
class CAjax extends CAjaxCommon
{
	var $hash;		/*<!хэш по которому определяется объект контейнер*/
	var $arParams;	/*<!Массив настроек виджета*/
	var $bAddAFunction;	/*!<Флаг указывающий на добавление функции на переход по ссылке*/
	var $bAddFormFunction; /*!<Флаг указывающий на добавление функции обработки формы*/
	static $arHashes; /*<!Массив виджетов, если такой виджет уже есть на странице, то в его хэш будет включен номер*/

	/**
	 * Конструктор, создает новый объект класса, инициализирует внутренние переменные
	 */
	function __construct($widget,$params)
	{
		parent::__construct();
		$this->Load();
		if(array_key_exists($widget,CAjax::$arHashes))
		{
			CAjax::$arHashes[$widget]++;
			$index=CAjax::$arHashes[$widget];
		}
		else
		{
			$index=1;
			CAjax::$arHashes[$widget]=1;
		}
		$this->hash='w'.substr(md5($widget.$index),1);
		$this->arParams=$params;
		$this->bAddAFunction=false;
		$this->bAddFormFunction=false;
	}

	/**
	 * Метод проверяет хэш на соответствие.
	 */
	function CheckHash($hash)
	{
		return $hash==$this->hash;
	}

	/**
	 * Метод выполняет замену тэгов a на тэги с аяксом в указанном контенте
	 * @param $matches
	 */
	protected function _ConvertA($matches)
	{
		//echo htmlentities($matches[0]).'<br/>';
		if(strlen($matches[3])>0)
		{
			$clue='&';
			$url=preg_replace(
				array(
					'#i=[0-9]+#i',
					'#p[0-9]+=([0-9]+)#i'
				),
				array(
					'i=1',
					'p1=$1',
				),
				$matches[2]);
		}
		else
		{
			$clue='?';
			$url=$matches[2];
		}
		//$matches[2]='#';
		//$sResult='<a href="'.$matches[2].'" onclick="return ksAjax'.$this->hash.'(\''.$url.$clue.'ajaxMode='.$this->hash.'\');"';
		$sResult='<a href="'.$matches[2].'" rel="'.$this->hash.'"';
		//Обработка оставшихся частей тэга
		$others=preg_split('#" #mi',$matches[5]);
		if(is_array($others)&&count($others)>0)
		{
			foreach($others as $sRow)
			{
				if(!preg_match('#onclick#i',$sRow))
				{
					$sResult.=' '.$sRow;
					if(substr($sRow,-1,1)!='"') $sResult.='"';
				}
				if(preg_match('#target=#i',$sRow))
				{
					$sResult=$matches[0];
					return $sResult;
				}
			}
		}
		$this->bAddAFunction=true;
		$sResult.='>';
		//echo htmlentities($sResult).'<br/>';
		return $sResult;
	}

	protected function _ConvertInput($matches)
	{
		$regexp = 'onclick="([^<>]*)"';
		if(!preg_match("#".$regexp."#mi",$matches[0])){
			$sResult = $matches[1] . 'type="'.$matches[2].'"' . $matches[3] . 'name="' . $matches[4] . '"';
			$sResult .= " onclick=\"this.isActive=true;return true;\"";
			$sResult .= $matches[5];
		} else {
			//Если в инпуте уже есть обработчик onclick - выдаем полный текст инпута назад
			$sResult = $matches[0];
		}
		return $sResult;
	}

	/**
	 * Метод выполняет замену отправки формы на функцию аякс
	 */
	protected function _ConvertForm($matches)
	{
		$sResult='<form action="'.$matches[3].'" onsubmit="return ksAjaxForm'.$this->hash.'(this);"';
		//Обработка оставшихся частей тэга
		$others=preg_split('#" #mi',$matches[6].' '.$matches[2]);
		if(is_array($others)&&count($others)>0)
		{
			foreach($others as $sRow)
			{
				if(!preg_match('#onsubmit#i',$sRow))
					$sResult.=' '.$sRow.(substr($sRow,-1,1)!='"'?'"':'');
				else
				{
					$sResult=$matches[0];
					return $sResult;
				}
			}
		}
		$this->bAddFormFunction=true;
		$sResult.='><input type="hidden" name="ajaxMode" value="'.$this->hash.'"/>';
		return $sResult;
	}

	/**
	 * Метод возвращает код html который будет вставлен в страницу для обеспечения работы ajax
	 * @param string $content - содержимое блока которое будет работать в аякс режиме
	 * @param $bOnlyUrl - ключ указывает, необходимо обновление блока или нет.
	 * @return содержимое блока с новыми ссылками.
	 */
	function GetCode($content,$bOnlyUrl=false)
	{
		global $KS_MODULES,$KS_URL;
		$path=substr($KS_URL->GetPath(),1);
		//Проверяем ссылки
		$regexp='<(a [^>]*href="(\/'.$path.'(\?(.*?)))"( ?.*?))>';
		$content=preg_replace_callback('#'.$regexp.'#mi',array($this,'_ConvertA'),$content);
		//Проверяем формы
		$regexp='<(form ([^<>]*?)action="(/(?:'.$path.'|index\.php\?path=/'.$path.')(\??([^"]*))?)"( ?[^>]*))>';
		$content=preg_replace_callback('#'.$regexp.'#mi',array($this,'_ConvertForm'),$content);
		$regexp='(<input [^<>]*)type="(submit|image)"(.*)name="([^\s]*)"([^<]*>)';
		$content=preg_replace_callback('#'.$regexp.'#mi',array($this,'_ConvertInput'),$content);
		//die;
		if(!$bOnlyUrl)
		{
			$content='<div id="'.$this->hash.'" class="ajaxOk">'.$content;
			$content.='</div><script type="text/javascript">';
			if($this->bAddAFunction)
			{
				$content.='function ksAjax'.$this->hash.'(){
						$(\'a[rel='.$this->hash.']\').click(function(e){
							if(e.isDefaultPrevented()) 
							{
								return;
							}
							e.preventDefault();
							ajaxShadow("'.$this->hash.'");
							$.get($(this).attr(\'href\'),{ajaxMode:\''.$this->hash.'\'},function(data)
							{
								$("#'.$this->hash.'").html(data);
								ajaxHideShadow("'.$this->hash.'");
								ksAjax'.$this->hash.'();
							});
						});
						return false;}';
				$content.='ksAjax'.$this->hash.'();';
			}
			if($this->bAddFormFunction)
			{
				$content.='function ksAjaxForm'.$this->hash.'(form){
						if(typeof(form)!="object") return true;
						var data=ajaxGetFormData(form);
						var url=form.action;
						var sign="?";
						if(url.indexOf("?")>0) sign="&";
						if(form.method=="get")
							url+=sign+data+"&ajaxMode='.$this->hash.'";
						else url+=sign+"&ajaxMode='.$this->hash.'";
						ajaxShadow("'.$this->hash.'");
						$.post(url,data,function(data)
							{
								$("#'.$this->hash.'").html(data);							
								ajaxHideShadow("'.$this->hash.'");
							});
						return false;}';
			}
			$content.='</script>';
		}
		return $content;
	}

	/**
	 * Метод производит загрузку и инициализация необходимых библиотек аякса
	 */
	function Load()
	{
		global $KS_MODULES;
		if(parent::$bIsLoad) return true;
		$KS_MODULES->AddHeadString('<script type="text/javascript" src="/js/jquery/jquery.js"></script>');
		$KS_MODULES->AddHeadString('<script type="text/javascript" src="/js/ajax.js"></script>');
		parent::$bIsLoad=true;
	}
	
	/**
	 * Метод подгружает яваскрипт библиотеки критичные для данного виджета,
	 * системные библиотеки не подключаются.
	 * @todo избавиться от сравнения строк
	 */
	function GetHeads()
	{
		global $KS_MODULES;
		$sResult='';
		foreach($KS_MODULES->arHeads as $sHead)
		{
			if($sHead!='<script type="text/javascript" src="/js/ajax.js"></script>' &&
			   $sHead!='<script type="text/javascript" src="/js/jquery/jquery.js"></script>')
			{
				$sResult.=$sHead."\n";
			}
		}
		return $sResult;
	}
}

class CAjaxEvent extends CAjaxCommon
{
	var $hash;		/*<!хэш по которому определяется объект контейнер*/
	var $arParams;	/*<!Массив настроек виджета*/
	var $bAddAFunction;	/*!<Флаг указывающий на добавление функции на переход по ссылке*/
	var $bAddFormFunction; /*!<Флаг указывающий на добавление функции обработки формы*/
	var $smarty;
	static $arHashes; /*<!Массив виджетов, если такой виджет уже есть на странице, то в его хэш будет включен номер*/

	function __construct(&$smarty)
	{
		global $smarty;
		parent::__construct();
		$this->Load();
		$this->smarty=$smarty;
		$this->bAddAFunction=false;
		$this->bAddFormFunction=false;
		$this->smarty->register_function('ajaxEvent',array($this,'_ajaxEvent'));
	}

	function _ajaxEvent($params,&$smarty)
	{
		if($params['id']=='') return '';
		if($params['event']=='') return '';
		if($params['handler']=='') return '';
		if($params['prepare']=='') return '';
		$this->hash=md5($params['id'].time());
		$content='
		<script type="text/javascript">
		function on'.$params['event'].'Handler'.$params['id'].'()
		{
			var url="/index.php";
			if(!document.KS_AJAX)
			{
				document.KS_AJAX=new Object();
			}
			var data=objectToURIString(document.KS_AJAX.data'.$params['id'].');
			url+="?"+data+"&type=AJAX";
			$.post(url,data,function(data)
				{
					'.$params['handler'].'(data);
				});
			return false;
		};
		function on'.$params['event'].$params['id'].'()
		{
			if(!document.KS_AJAX)
			{
				document.KS_AJAX=new Object();
			}
			document.KS_AJAX.data'.$params['id'].'='.$params['prepare'].'();
			setTimeout("on'.$params['event'].'Handler'.$params['id'].'()",'.$params['timeout'].');
		}
		var ob=document.getElementById("'.$params['id'].'");
		if (ob)
		{
			if (ob.addEventListener)
			{
				ob.addEventListener("'.$params['event'].'", on'.$params['event'].$params['id'].', false);
			}
			else
			{
				ob.attachEvent("on'.$params['event'].'", on'.$params['event'].$params['id'].');
			}
		}
		</script>';
		return $content;
	}

	function Load()
	{
		global $KS_MODULES;
		if(parent::$bIsLoad) return true;
		$KS_MODULES->AddHeadString('<script type="text/javascript" src="/js/jquery/jquery.js"></script>');
		$KS_MODULES->AddHeadString('<script type="text/javascript" src="/js/ajax.js"></script>');
		parent::$bIsLoad=true;
	}
}
?>
