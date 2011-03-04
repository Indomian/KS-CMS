<?php
if (!defined('KS_ENGINE')) die("Hacking attempt!");
/**
 * Базовый интерфейс для всех пользователей системы
 */

interface User
{
	function Activate($code);
	function GetByHash($code);
	function GenHash($id);
	function LoginByTitle($title);
	function CheckPasswordRequirements($password);
	function ConvertPassword($password);
	function login();
	function logout();
	function IsLogin();
	function SetUserGroup($uid, $gid);
	function GenPassword($length=6);
	function is_admin($id);
	function IsAdmin($id=false);
	function Save($prefix="KS_", $data="", $mytable="");
	function GetUserVar($var);
	function SetUserVar($var, $value);
	function WriteUserVars();
	function DeleteItems($arFilter);
	function GetGroups($user_id=0);
 	function ID();
 	function Email();
 	function GetAllGroups($id);
 	function GetLevel($module);
 	function SetAllUserGroups($iUserID, $arGroups);
 	function GetUserData();
}