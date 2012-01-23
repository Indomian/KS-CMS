<?php
/**
 * @filesource subscribe/libs/interface.IReleaseSender.php
 * Файл с описанием интерфейса класса подготовки и выполнения рассылки
 * Файл проекта kolos-cms.
 *
 * @since 24.01.2012
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

interface IReleaseSender
{
	function __construct(CSubscribeAPI $obParent);
	/**
	 * Метод выполняет подготовку рассылки
	 * @param $arRelease - массив описывающий рассылку
	 */
	function Prepare(array $arRelease);
	/**
	 * Метод выполняет рассылку
	 * @return boolean - true - если рассылка отправлена, false - если рассылка отправлена частично
	 */
	function Send();
}