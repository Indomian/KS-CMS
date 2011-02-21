<?php
/**
 * @file class.CHTTPInterface.php
 * Файл класса для работы с данными передаваемыми по HTTP
 * Файл проекта kolos-cms.
 * 
 * Создан 18.02.2010
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.5.4 
 * @todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

class CHTTPInterface extends CBaseObject
{
	protected $sUrl;
	protected $sDomain;
	protected $sUri;
	protected $sPath;
	protected $sMode;
	protected $arData;
	protected $iPort;
	protected $arHeaders;
	
	/**
	 * Конструктор класса, пока ничего не делает
	 */
	function __construct($url,$mode='GET',$port='80')
	{
		$this->sUrl=$url;
		$this->sMode=$mode;
		$this->arData=array();
		$this->iPort=$port;
		$this->arHeaders=array();
		$this->ParseUrl($url);
	}
	
	/**
	 * Метод разбивает строку url на домен, строку запроса и GET параметры
	 */
	function ParseUrl($url)
	{
		if(preg_match('#^(http://)?([a-z\.]+)(/[^?]+)?(\?(.*))?$#i',$url,$matches))
		{
			$this->sDomain=$matches[2];
			$this->sPath=$matches[3]!=''?$matches[3]:'/';
			$this->arData=array_merge($this->arData,$this->ParseGetString($matches[5]));
		}
		else
		{
			$this->sPath='/';
			$this->sDomain='';
		}
	}
	
	/**
	 * Метод устанавливает значение заголовка для запроса
	 */
	function SetHeader($name,$value)
	{
		$this->arHeaders[$name]=$value;
	}
	
	/**
	 * Метод разбивает 
	 */
	function ParseGetString($get)
	{
		$arResult=array();
		$arParams=explode('&',$get);
		foreach($arParams as $sParam)
		{
			$arParam=explode('=',$sParam);
			$arResult[urldecode($arParam[0])]=urldecode($arParam[1]);	
		}
		return $arResult;
	}
	
	/**
	 * Метод устанавливает данные которые должны быть отправлены в запросе
	 */
	function SetData($arData,$value='')
	{
		if(is_array($arData))
		{
			$this->arData=array_merge($this->arData,$arData);
		}
		elseif(is_string($arData))
		{
			$this->arData[$arData]=$value;
		}
	}
	
	/**
	 * Метод отправляет запрос на сервер
	 */
	function Send()
	{
		if($this->sDomain=='') return false;
		$sock=fsockopen($this->sDomain,$this->iPort,$errorCode,$errorText,5);
		if($sock)
		{
			if($this->sMode=='GET')
			{
				$header = "GET ".$this->sPath."?".$this->EncodePostData($this->arData)." HTTP/1.0\r\n";
    			$header .= "Host: ".$this->sDomain."\r\n";
    			if(count($this->arHeaders)>0)
    			{
    				foreach($this->arHeaders as $key=>$value)
    				{
    					$header.=$key.':'.$value."\r\n";
    				}
    			}
	    		$header .= "Connection: Close\r\n\r\n";
	    		$body='';
			}
			elseif($this->sMode=='POST')
			{
				$header = "POST ".$this->sPath." HTTP/1.0\r\n";
    			$header .= "Host: ".$this->sDomain."\r\n";
    			if(count($this->arHeaders)>0)
    			{
    				foreach($this->arHeaders as $key=>$value)
    				{
    					$header.=$key.':'.$value."\r\n";
    				}
    			}
	    		//$header .= "Content-Length: ".strlen($command)."\r\n";
    			//$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	    		$header .= "Connection: Close\r\n\r\n";
	    		$body='';	
			}
			fwrite($sock,$header.$body."\r\n");
			$result='';
			while (!feof($sock)) 
			{
        		$result.=fgets($sock, 1024);
    		}
	    	if(strlen($result)>0)
	    	{
	    		$arData=$this->GetHeader($result);
	    	}
	    	fclose($sock);
	    	return $arData;
		}
		else
		{
			throw new CHTTPError('SYSTEM_HTTP_SOCKET_ERROR',$errorCode,$errorText);
		}
	}

	/**
	 * Метод выполняет скачивание файла с сервера
	 */
	function Download($to,$from=0,$timeout=30,$maxSize=5)
	{
		$begin=time();
		$maxSize*=1024*1024;
		if($this->sDomain=='') return false;
		$sock=fsockopen($this->sDomain,$this->iPort,$errorCode,$errorText,5);
		if($sock)
		{
			$header = "GET ".$this->sPath." HTTP/1.1\r\n";
    		$header .= "Host: ".$this->sDomain."\r\n";
    		if($from>0)
    		{
    			$header.='Range: bytes='.$from."-\r\n";
    		}
	    	$header .= "Connection: Close\r\n\r\n";
	    	$body='';
			fwrite($sock,$header.$body."\r\n");
			$result='';
			while (!feof($sock)) 
			{
        		$result.=fread($sock, 1024);
        		if(($begin+$timeout)<time()||strlen($result)>$maxSize)
        			break;
    		}
    		fclose($sock);
	    	if(strlen($result)>0)
	    	{
	    		$arData=$this->GetHeader($result);
	    		if(strlen($arData['body'])>0)
	    		{
	    			$file=fopen($to,'a+b');
	    			fwrite($file,$arData['body']);
	    			fclose($file);
	    			return filesize($to);
	    		}
	    	}
	    	return false;
		}
		else
		{
			throw new CHTTPError('SYSTEM_HTTP_SOCKET_ERROR',$errorCode,$errorText);
		}
	}

	/**
	 * Метод кодирует массив в формат x-www-form-encode
	 */
	function EncodePostData($data)
	{
		if(is_array($data))
		{
			$arResult=array();
			foreach($data as $key=>$value)
			{
				if($key!='')
				{
					$arResult[]=urlencode($key).'='.urlencode($value);
				}
			}
			if(is_array($arResult))
			{
				return join('&',$arResult);
			}
			return false;
		}
		return false;
	}

	/**
	 * Метод возвращает данные заголовков
	 */
	function GetHeader($sdata)
	{
		$arResult=array();
		if(strlen($sdata)>0)
		{
			if(strpos($sdata,"\r\n\r\n")>0)
			{
				$arItems=explode("\r\n\r\n",$sdata);
				$arHeaders=explode("\r\n",array_shift($arItems));
				$arResult['body']=join("\r\n\r\n",$arItems);
				foreach($arHeaders as $arRow)
				{
					$arRow=explode(":",$arRow);
					if(preg_match('#^([0-9]{3,3})(.*)#i',$arRow[0],$matches))
					{
						$arResult['headers']['RESULT']=array(
							'code'=>$matches[1],
							'answer'=>$matches[2],
						);
					}
					if($arRow[0]!='')
						$arResult['headers'][array_shift($arRow)]=join(':',$arRow);
				}
				return $arResult;
			}
		}
		return false;
	}
}
?>