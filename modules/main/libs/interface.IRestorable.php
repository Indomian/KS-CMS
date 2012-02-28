<?php
if (!defined('KS_ENGINE')) die("Hacking attempt!");
/**
 * Интерфейс описывающий объекты обладающие возможностями удаления в "корзину" и восстановления из неё
 */

interface IRestorable
{
	function Serialize($arRecord);
	function DeSerialize($sRecrod);
	function DeleteToBasket(array $arFilter);
	function RestoreFromBasket(array $arFilter);
}