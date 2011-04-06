<?php
if (!defined('KS_ENGINE')) die("Hacking attempt!");
/**
 * Базовый интерфейс для работы с системой прав
 */

interface BaseAccess
{
	/**
	 * Метод выполняет запись прав в базу данных.
	 * @param $group_id идентификатор группы
	 * @param $module идентификатор элемента для которого устанавливаются права
	 * @param $leve уровень прав доступа
	 */
	function Set($group_id,$module,$level);
}
