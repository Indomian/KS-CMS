<?php
/**
 * @file class.CHTMLCache.php
 * Файл для работы с кэшированием HTML результатов
 * Файл проекта kolos-cms.
 *
 * Создан 12.12.2009
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.5.3
 * @todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}
include_once MODULES_DIR.'/main/libs/class.CCache.php';
class CHTMLCache extends CCache
{
	protected $data;
	/**
	 * Конструктор инициализирует кэш по заданным параметрам и возвращает объект кэша
	 */
	function __construct($cacheId,$cacheTime,$module='common')
	{
		if(empty($module)) throw new CError('SYSTEM_STRANGE_ERROR',__LINE__,'Имя кешируемого модуля не может быть пустой строкой');
		$this->module=$module;
		$this->cacheTime=$cacheTime;
		$this->cacheId=$cacheId;
		if(defined('KS_CACHE_HTML_DIR'))
		{
			$this->sCacheDir=KS_CACHE_DIR.$this->module.'/';
			$this->sCacheFile=KS_CACHE_DIR.$this->module.'/'.$cacheId.'.html';
		}
		else
		{
			$this->sCacheDir=MODULES_DIR.'/main/cache/'.$this->module.'/';
			$this->sCacheFile=MODULES_DIR.'/main/cache/'.$this->module.'/'.$cacheId.'.html';
		}
		$this->isAlive=false;
		if($cacheTime>0)
		{
			if(file_exists($this->sCacheFile))
			{
				$this->isAlive=(filemtime($this->sCacheFile)+$cacheTime)>time();
			}
		}
		else
		{
			$this->isAlive=false;
		}
		if(KS_SKIP_HTMLCACHE=='Y')
		{
			$this->isAlive=false;
			$this->cacheTime=0;
		}
		//parent::__construct($cacheId,$cacheTime,$module);
	}

	/**
	 * Метод возвращает true если кэш еще не истек и false если истек (или не существует)
	 */
	function Alive()
	{
		return $this->isAlive;
	}

	/**
	 * Метод выполняет возвращение данных из кэша
	 */
	function GetData()
	{
		return file_get_contents($this->sCacheFile);
	}

	/**
	 * Метод выполняет сохранение данных в кэш
	 */
	function SaveToCache($data)
	{
		if($this->cacheTime==0) return false;
		if(!file_exists($this->sCacheDir) || !is_dir($this->sCacheDir))
		{
			if(!@mkdir($this->sCacheDir,0755,true))
			{
				throw new CError("SYSTEM_CACHE_WRITE_ERROR",1);
			}
		}
		$size = file_put_contents($this->sCacheFile, '<?php /**/?>'.$data);
		if ($size == 0)
		{
			throw new CError("SYSTEM_CACHE_WRITE_ERROR",0);
		}
	}
}
?>
