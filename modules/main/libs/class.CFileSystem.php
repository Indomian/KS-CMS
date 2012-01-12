<?php
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}
/**
 * Используется в качестве заготовки для внутренней файловой системы. На данный момент просто содержит ряд полезных функций.
 * @version 2.6
 * @since 17.10.2008
 * @author blade39 <blade39@kolosstudio.ru>, D. Konev, <d.konev@kolosstudio.ru>
 */

abstract class CFileSystem extends CBaseObject
{
	abstract function MakeDir($path);
	abstract function Remove($path);
	abstract function Rename($old, $new);
	abstract function RemDir($path);
	abstract function DirCopy($srcdir, $dstdir);
	abstract function CopyFile($from,$to,$absolute='');
	abstract function CmpPath($old,$new);
	abstract function ChangePath($old,$new);
	abstract function GetDirItems($dir);
	abstract function CountDirFiles($dir);
	abstract function GetDirList($dir);
	abstract function ClearDir($path);
}
