<?php

/**
 * @file class.CGeographyAPI.php
 * Файл содержит класс для работы со списком стран, городов и других географических объектов
 * Файл проекта kolos-cms.
 *
 * Создан 03.03.2011
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/main/libs/class.CBaseAPI.php';

class CGeographyAPI extends CBaseAPI
{
	private $obCountry;
	private $obCity;

	static private $obInstance;

	function __construct()
	{}

	static function get_instance()
	{
		if(!is_object(self::$obInstance))
		{
			self::$obInstance=new CGeographyAPI();
			self::$obInstance->Init();
		}
		return self::$obInstance;
	}

	private function Init()
	{
		$this->obCountry=new CObject('geography_countries');
		$this->obCountry->AddAutoField('id');
		$this->obCity=new CObject('geography_cities');
		$this->obCity->AddAutoField('id');
	}

	function Country()
	{
		return $this->obCountry;
	}

	function City()
	{
		return $this->obCity;
	}
}