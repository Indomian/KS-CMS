<?php
/**
 * \file class.CCache.php
 * Файл для работы с кэшированием
 * Файл проекта kolos-cms.
 *
 * Создан 12.12.2009
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 2.5.3
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

abstract class CCache extends CBaseObject
{
	protected $module;
	protected $cacheTime;
	protected $cacheId;
	protected $isAlive;
	protected $sCacheFile;

	/**
	 * Конструктор инициализирует кэш по заданным параметрам и возвращает объект кэша
	 */
	function __construct($cacheId,$cacheTime,$module='common')
	{
		$this->module=$module;
		$this->cacheTime=$cacheTime;
		$this->cacheId=$cacheId;
		$this->sCacheFile=MODULES_DIR.'/main/cache/'.$this->module.'/'.$cacheId.'.php';
		$this->isAlive=false;
		if(defined('KS_SKIP_CACHE') && KS_SKIP_CACHE=='Y')
		{
			$this->isAlive=false;
		}
		else
		{
			if(file_exists($this->sCacheFile))
			{
				$this->isAlive=(filemtime($this->sCacheFile)+$cacheTime)>time();
			}
		}
	}

	/**
	 * Метод возвращает true если кэш еще не истек и false если истек (или не существует)
	 */
	abstract function Alive();

	/**
	 * Метод выполняет возвращение данных из кэша
	 */
	abstract function GetData();

	/**
	 * Метод выполняет сохранение данных в кэш
	 */
	abstract function SaveToCache($data);
}
?>