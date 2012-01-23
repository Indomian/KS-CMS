<?php
/**
 * @file class.CReleases.php
 * Файл с классом CReleases обеспечивающим операции с рассылками
 * Файл проекта kolos-cms.
 *
 * Создан 31.08.2011
 *
 * @author Konstantin Kuznetsov <lopikun@gmail.com>, blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

class CReleases extends CObject
{
	function __construct($sTable="subscribe_releases")
	{
		parent::__construct($sTable);
	}

	function Save($prefix = "SB_", $data = false)
	{
		if(!$data) $data=$_POST;
		if($data[$prefix.'newsletter']==-1)
		{
			$recipients="";
			if(isset($data[$prefix.'news']))
			{
				$recipients.='newsletters=';
				foreach($data[$prefix.'news'] as $elm)
					$recipients.=$elm.",";
				$recipients=substr_replace($recipients,'&',-1);
				unset($data[$prefix.'news']);
			}
			if(isset($data[$prefix.'groups']))
			{
				$recipients.='groups=';
				foreach($data[$prefix.'groups'] as $elm)
					$recipients.=$elm.",";
				$recipients=substr_replace($recipients,'&',-1);
				unset($data[$prefix.'groups']);
			}
			if(isset($data[$prefix.'list']))
			{
				$recipients.='list='.str_replace(array("\n","\r"),array(",",''),$data[$prefix.'list']).'&';
				unset($data[$prefix.'list']);
			}
			if($recipients)
				$data[$prefix.'recipients']=$recipients;
			else
				throw new CError("SUBSCRIBE_RECIPIENTS_ERROR");
		}
		return parent::Save($prefix, $data);
	}


	function GetRecord($where=false)
	{
		if($data=parent::GetRecord($where))
		{
			if($data['recipients'] && $data['newsletter']==-1)
			{
				parse_str($data['recipients'],$res);
				foreach($res as $key=>$elm)
					$res[$key]=explode(',',$res[$key]);
				$data=array_merge($data,$res);
			}
		}
		return $data;
	}
}
