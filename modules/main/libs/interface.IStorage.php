<?php
if (!defined('KS_ENGINE')) die("Hacking attempt!");
/**
 * Интерфейс описывающий объекты обладающие возможностями хранения записей с известным хэшем и выборкой и восстановлением таких записей
 */

interface IStorage
{
	function Put($arHash,$sData);
	function Get($arHash);
	function Delete($arHash);
	function Clear();
}
