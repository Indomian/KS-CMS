<?php
/*
 * CMS-local
 *
 * Created on 10.11.2008
 *
 * Developed by blade39
 *
 */

include_once MODULES_DIR.'/main/libs/class.CEventTemplates.php';

/**
 * Класс отвечает за управление сообщениями в системе управления событиями. Фактически выполняет
 * действия связанные с добавлением событий.
 */
class CMessage extends CObject
{
	var $sType;

	function __construct($sTable='main_events',$id=0)
	{
		parent::__construct($sTable);
		$this->sType='plain';
	}

	function AddString($to,$title,$message)
	{
		global $USER;
		$data=array('address'=>$to,'title'=>$title,'content'=>$message,'status'=>'new','type'=>$this->sType,'date_add'=>time());
		if($USER->IsLogin())
		{
			$data['author']=$USER->ID();
		}
		return parent::Save('',$data);
	}

	function AddTemplate($to,$data,$tpl,$format='text/plain',$encoding='utf-8',$email_from='',$name_to='',$title='')
	{
		global $USER,$smarty;
		try
		{

			$ob=new CEventTemplates();
			if($res=$ob->GetTemplate($tpl))
			{
				$data['date_add']=time();
				$smarty->assign('data',$data);
				$message=$smarty->fetch(EVENT_TEMPLATES_DIR.'/'.$tpl);
				if($format=='text/plain')
				{
					$message=strip_tags($message);
				}

				$data=array(
					'address'=>$res['address'],
					'title'=>($title!='')?strip_tags($title):strip_tags($res['title']),
					'status'=>'new',
					'type'=>$this->sType,
					'date_add'=>$data['date_add'],
					'content'=>$message,
					'format'=>$format,
					'encoding'=>$encoding,
					'email_from'=>$email_from,
					'name_to'=>$name_to
				);

				if(!IsEmail($res['address']))
				{
					$data['address']=trim($to);
				}
				else
				{
					$data['address']=$res['address'];
				}
				if(!IsEmail($data['address'])) return false;
				if($USER->IsLogin())
				{
					$data['author']=$USER->ID();
				}

				return parent::Save('',$data);
			}
		}
		catch (CError $e)
		{
			return false;
		}
		return false;
	}
}

/*функция затычка. Заокмментировать!!!!*/
function mail1($to,$subject,$message,$headers)
{
	$filename=ROOT_DIR.'/mail.txt';
	$file=fopen($filename,'a');
	if($file)
	{
		fwrite($file,date('%r')."\n");
		fwrite($file,$to."\n");
		fwrite($file,$subject."\n");
		fwrite($file,$message."\n");
		fclose($file);
		return true;
	}
	else
	{
		return false;
	}
}

class CEmailMessage extends CMessage
{
	function __construct($sTable='main_events',$id=0)
	{
		parent::__construct($sTable);
		$this->sType='Email';
	}

	/**
	 * Функция выполняет отсылку сообщение по электронной почте.
	 * @param $data - массив с полями необходимыми для отправки.
	 * возвращает true в случае успешного завершения.
	 */
	function Run($data)
	{

		global $KS_MODULES;
		$data_charset='utf-8';
		$format=$data['format'];
		$encoding=$data['encoding'];
		$name_from=$data['author'];
		$name_to=$data['name_to'];
		$email_from=$data['email_from'];
		$to=$this->mime_header_encode($name_to, $data_charset, $encoding).' <'.$data['address'].'>';
		if($data_charset != $encoding)
		{
    		$message = iconv($data_charset, $encoding, $data['content']);
  		}
  		else
  		{
			$message=$data['content'];
  		}
  		$subject = $this->mime_header_encode($data['title'], $data_charset, $encoding);
  		try
		{
			if(!IsEmail($email_from))
			{
				$email_from=$KS_MODULES->GetConfigVar('main','emailFrom');
			}
			if($email_from!='')
			{
				$headers = 'From: '.$email_from."\r\n" .
							'Reply-To: '.$email_from. "\r\n" .
							'Content-Type: '.$format.'; charset='.$encoding."\r\n".
							'X-Mailer: PHP/' . phpversion();
			}
			else
			{
				$headers='Content-Type: '.$format.'; charset='.$encoding;
			}
			if(KS_MAIL_DEBUG==1)
			{
				$res=mail1($to,$subject,$message,$headers);
			}
			else
			{
				$res=mail($to,$subject,$message,$headers);
			}
			if(!$res)
			{
				$this->Update($data['id'],array('date_end'=>time(),'status'=>'error'));
				throw new CError("MAIN_SEND_ERROR");
			}
			else
			{
				$this->Update($data['id'],array('date_end'=>time(),'status'=>'done'));
			}
		}
		catch (CError $e)
		{
			throw $e;
		}
		return true;
	}
	/**
	 *
	 * @param  $str - исходные данные
	 * @param  $data_charset - кодировка исходных данных
	 * @param  $send_charset - кодировка на выходе
	 * @return перекодированные данные
	 */
	function mime_header_encode($str, $data_charset, $send_charset) {
	  if($data_charset != $send_charset)
	  {
	    $str = iconv($data_charset, $send_charset, $str);
	  }
	  return '=?'.$send_charset.'?B?'.base64_encode($str).'?=';
	}
}

