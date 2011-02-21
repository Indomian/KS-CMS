<?php
/*
 * CMS-remote
 *
 * Created on 27.10.2008
 *
 * Developed by blade39
 *
 */
 //die();
if( !defined('KS_ENGINE') )
{
  die("Hacking attempt!");
}

//Проверка прав доступа
if($USER->GetLevel('main')>1) throw new CAccessError("MAIN_NO_RIGHT_VIEW_PHP");

phpinfo();
die();
?>
