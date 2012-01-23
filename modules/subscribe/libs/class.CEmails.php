<?php
/**
 * @file class.CEmails.php
 * Файл с классом CEmails обеспечивающий операции над рассылаемыми письмами
 * Файл проекта kolos-cms.
 *
 * Создан 16.09.2011
 *
 * @author Konstantin Kuznetsov <lopikun@gmail.com>, blade39 <blade39@kolosstudio.ru>
 * @version 2.6
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once(MODULES_DIR . "/main/libs/class.CMessage.php");

class CEmails extends CEmailMessage
{
	function send($data)
	{
		if(isset($data['emails']))
			foreach($data['emails'] as $mail)
			{
				$name_to='Гость';
				if($mail['users_title'])$name_to=$mail['users_title'];
				if($mail['format']==1)
					$format='text/html';
				elseif($mail['format']==2)
					$format='text/plain';
				else
					$format='text/plain';
				$this->AddTemplate($mail['email'],$data,'subscribe.message.tpl',$format,$data['encryption'],$data['from'],$name_to,$data['theme']);
			}
	}
}
