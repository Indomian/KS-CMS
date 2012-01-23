<?php
/**
 * @filesource subscribe/libs/class.CSimpleSender.php
 * Файл с описанием интерфейса класса подготовки и выполнения рассылки
 * Файл проекта kolos-cms.
 *
 * @since 24.01.2012
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/subscribe/libs/interface.IReleaseSender.php';

class CSimpleSender extends CBaseObject implements IReleaseSender
{
	private $obEvent;
	private $obAPI;
	private $arEmails;
	private $arRelease;

	function __construct(CSubscribeAPI $obParent)
	{
		$this->obEvent=new CEmailMessage();
		$this->obAPI=$obParent;
		$this->arEmails=false;
	}

	function Prepare(array $arRelease)
	{
		global $KS_MODULES;
		$this->arEmails=false;
		$this->arRelease=$arRelease;
		if($arRelease['newsletter']>0)
			$this->arEmails=$this->obAPI->GetEmailByNewsletter($arRelease['newsletter']);
		else
		{
			if(isset($arRelease['groups'])&&is_array($arRelease['groups']))
				$this->arEmails=$this->obAPI->GetEmailByGroup($arRelease['groups']);
			elseif(isset($arRelease['newsletters'])&&is_array($arRelease['newsletters'])&&isset($arRelease['list'])&&is_array($arRelease['list']))
			{
				$this->arEmails=array();
				$arRelease['list']=array_unique($arRelease['list']);
				if($arRelease['newsletters']=$this->obAPI->GetEmailByNewsletters($arRelease['newsletters']))
					foreach($arRelease['newsletters'] as $arItem)
						$this->arEmails[$arItem['email']]=$arItem;
				foreach($arRelease['list'] as $mail)
					if(!array_key_exists($mail,$this->arEmails))
						$this->arEmails[$mail]=array('email'=>$mail);
				$this->arEmails=array_values($this->arEmails);
			}
			elseif(isset($arRelease['newsletters'])&&is_array($arRelease['newsletters']))
				$this->arEmails=$this->obAPI->GetEmailByNewsletters($arRelease['newsletters']);
			elseif(isset($arRelease['list'])&&is_array($arRelease['list']))
			{
				$this->arEmails=array();
				foreach($arRelease['list'] as $mail)
					$this->arEmails[]=array('email'=>$mail);
			}
		}
	}

	function Send()
	{
		global $KS_MODULES;
		if($this->arEmails && count($this->arEmails)>0)
		{
			$sDefaultName=$KS_MODULES->GetConfigVar('subscribe','default_subscriber_name',$KS_MODULES->GetText('subcribe_default_user_name'));
			foreach($this->arEmails as $arSend)
			{
				$sNameTo=$sDefaultName;
				if(isset($arSend['users_title']) && $arSend['users_title']!='')
					$sNameTo=$arSend['users_title'];
				$sFormat='text/plain';
				if(isset($arSend['format']) && $arSend['format']==1)
					$sFormat='text/html';
				elseif(isset($arSend['format']) && $arSend['format']==2)
					$sFormat='text/plain';
				$this->obEvent->AddTemplate($arSend['email'],$this->arRelease,'subscribe.message.tpl',$sFormat,$this->arRelease['encryption'],$this->arRelease['from'],$sNameTo,$this->arRelease['theme']);
			}
			return true;
		}
		return false;
	}
}