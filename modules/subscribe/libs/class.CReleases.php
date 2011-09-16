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
	private $obNews;

	function __construct($sTable="subscribe_releases")
	{
		parent::__construct($sTable);
		$this->obNews= new CObject('subscribe_newsletters');
	}

	/**
	 * Метод возвращает объект рассылок
	 */
	function News()
	{
		return $this->obNews;
	}

	/**
	 * Метод возвращает список активных рассылок
	 */
	function GetNewslettersList($arOrder=array('name'=>'asc'))
	{
		return $this->obNews->GetList($arOrder,array('active'=>1), false, array('id','name'));
	}

	function Save($prefix = "KS_", $data = "")
	{
		if($data['SB_newsletter']==-1)
		{
			$recipients="";
			if($data['SB_news'])
			{
				$recipients.='newsletters=';
				foreach($data['SB_news'] as $elm)
				{
					$recipients.=$elm.",";
				}
				$recipients=substr_replace($recipients,'&',-1);
				unset($data['SB_news']);
			}
			if($data['SB_groups'])
			{
				$recipients.='groups=';
				foreach($data['SB_groups'] as $elm)
				{
					$recipients.=$elm.",";
				}
				$recipients=substr_replace($recipients,'&',-1);
				unset($data['SB_groups']);
			}
			if($data['SB_list'])
			{
				$recipients.='list='.str_replace("\r\n",",",$data['SB_list']).'&';
				unset($data['SB_list']);
			}
			if($recipients)
			{
				$data['SB_recipients']=$recipients;
			}
			else
			{
				throw new CError("SUBSCRIBE_RECIPIENTS_ERROR");
			}
		}
		return parent::Save($prefix, $data);
	}


	function GetRecord($where=false)
	{
		$data=parent::GetRecord($where);
		if($data['recipients'] && $data['newsletter']==-1)
		{
			 parse_str($data['recipients'],$res);
			 foreach($res as $key=>$elm)
			 {
				 $res[$key]=explode(',',$res[$key]);
			 }
			 $data=array_merge($data,$res);
		}
		return $data;
	}
}
