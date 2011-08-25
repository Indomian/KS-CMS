<?php
/**
 * Виджет, выполняет вывод уведомления о том, что браузер устарел и рекомендацию его обновить
 *
 * @filesource function.BrowserAttention.php
 * @author BlaDe39 <blade39@kolosstudio.ru>
 * @version 2.6
 * @since 15.07.2011
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

function smarty_function_BrowserAttention($params, &$subsmarty)
{
	global $KS_MODULES;

	$arBrowser=GetUserAgentData($_SERVER['HTTP_USER_AGENT']);
	$bOld=false;
	if($arBrowser['BROWSER']=='Firefox')
	{
		if($arBrowser['VERSION']<'4.0') $bOld=true;
	}
	elseif($arBrowser['BROWSER']=='Chrome')
	{
		if($arBrowser['VERSION']<'10') $bOld=true;
	}
	elseif($arBrowser['BROWSER']=='MSIE')
	{
		if($arBrowser['OS']=='Windows NT 6.1')
		{
			//7
			if($arBrowser['VERSION']<'9.0') $bOld=true;
			$subsmarty->assign('os','win7');
		}
		else
		{
			//older
			if($arBrowser['VERSION']<'9.0') $bOld=true;
			$subsmarty->assign('os','win');
		}
	}
	elseif($arBrowser['BROWSER']=='Safari')
	{
		if($arBrowser['VERSION']<'4.0') $bOld=true;
	}
	elseif($arBrowser['BROWSER']=='Opera')
	{
		if($arBrowser['VERSION']<'10.0') $bOld=true;
	}

	$subsmarty->assign('is_old',$bOld);
	/* Поиск шаблона для виджета и возвращение результата */
	return $KS_MODULES->RenderTemplate($subsmarty,'/interfaces/BrowserAttention',$params['global_template'],$params['tpl']);
}

/**
 * Функция выполняет определение параметров пользователя по данным переданным в адресной строке
 * @since 29.08.2009
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 1.0
 *
 * 1.0 - Базовая версия проверка взята из вики http://ru.wikipedia.org/wiki/User_Agent
 */
function GetUserAgentData($useragent)
{
	//echo $useragent;
	if(preg_match('#Mozilla\/([0-9\.]+) (\(([^;\)]+(; |)?)+\))(.*)#i',$useragent,$matches))
	{
		//Нормальный браузер который косит под мозилу
		if($pos=strpos($matches[0],'MSIE'))
		{
			//Косим под ИЕ
			if($pos1=strpos($matches[0],'Netscape'))
			{
				$arResult['BROWSER']='Netscape Navigator';
				preg_match('#Netscape\/| ([0-9\.]+)#i',$matches[0],$arVer);
				$arResult['VERSION']=$arVer[1];
			}
			elseif($pos1=strpos($matches[0],'Opera'))
			{
				$arResult['BROWSER']='Opera';
				preg_match('#Opera ([0-9\.]+)#',$matches[0],$arVer);
				$arResult['VERSION']=$arVer[1];
			}
			else
			{
				$arResult['VERSION']=substr($matches[0],$pos+5,strpos($matches[0],';',$pos)-$pos-5);
				$arResult['BROWSER']='MSIE';
			}
			$arResult['OS']='Other';
			//Определяем операционку
			if(preg_match("#Windows ([a-z\.0-9 ]+)(;|\))#i",$matches[2],$arOs))
			{
				$arResult['OS']='Windows '.$arOs[1];
			}
			elseif($pos1=strpos($matches[2],'Mac'))
			{
				$arResult['OS']='Mac OS';
			}
			elseif($pos1=strpos($matches[2],'Linux'))
			{
				$arResult['OS']='Linux';
			}
			elseif($pos1=strpos($matches[2],'Nitro'))
			{
				$arResult['OS']='Nintendo DS';
			}
			elseif($pos1=strpos($matches[2],'Symbian'))
			{
				$arResult['OS']='Symbian OS';
			}
		}
		elseif($pos=strpos($matches[0],'Camino'))
		{
			$arResult['OS']='Mac OS';
			$arResult['BROWSER']='Camino';
			$arResult['VERSION']=substr($matches[0],$pos+7);
		}
		elseif($pos=strpos($matches[0],'Epiphany'))
		{
			$arResult['OS']='Linux';
			$arResult['BROWSER']='Epiphany';
			$arResult['VERSION']=substr($matches[0],$pos+9);
		}
		elseif($pos=strpos($matches[0],'Flock'))
		{
			$arResult['OS']='Linux';
			$arResult['BROWSER']='Flock';
			$arResult['VERSION']=substr($matches[0],$pos+6);
		}
		elseif($pos=strpos($matches[0],'Chrome'))
		{
			$parts=explode(';',substr($matches[2],1,-1));
			$arResult['OS']=$parts[0].$parts[2];
			$arResult['BROWSER']='Chrome';
			$arResult['VERSION']=substr($matches[0],$pos+7,strpos($matches[0],' ',$pos+7)-$pos-7);
		}
		elseif($pos=strpos($matches[0],'Iceweasel'))
		{
			$arResult['OS']='Linux';
			$arResult['BROWSER']='Iceweasel';
			$arResult['VERSION']=substr($matches[0],$pos+10,strpos($matches[0],' ',$pos+10)-$pos-10);
		}
		elseif($pos=strpos($matches[0],'Icecat'))
		{
			$arResult['OS']='Linux';
			$arResult['BROWSER']='Icecat';
			$arResult['VERSION']=substr($matches[0],$pos+7);
		}
		elseif($pos=strpos($matches[0],'K-Meleon'))
		{
			$pos1=strpos($matches[2],'Windows ');
			$arResult['OS']='Windows '.substr($matches[2],$pos1+8,strpos($matches[2],';',$pos1+8)-$pos1-8);
			$arResult['BROWSER']='K-Meleon';
			$arResult['VERSION']=substr($matches[0],$pos+9);
		}
		elseif($pos=strpos($matches[0],'Minimo'))
		{
			$pos1=strpos($matches[2],'Windows ');
			$arResult['OS']='Windows '.substr($matches[2],$pos1+8,strpos($matches[2],';',$pos1+8)-$pos1-8);
			$arResult['BROWSER']='Minimo';
			$arResult['VERSION']=substr($matches[0],$pos+7);
		}
		elseif($pos=strpos($matches[0],'Firefox'))
		{
			if($pos1=strpos($matches[0],'Opera'))
			{
				$arResult['BROWSER']='Opera';
				$arResult['VERSION']=substr($matches[0],$pos1+6,strpos($matches[0],' ',$pos1+6)-$pos1-6);
			}
			else
			{
				$arResult['BROWSER']='Firefox';
				preg_match('#^([0-9\.]+)#i',substr($matches[0],$pos+8),$arVer);
				$arResult['VERSION']=$arVer[1];
			}
			if($pos1=strpos($matches[2],'Windows '))
			{
				$arResult['OS']='Windows '.substr($matches[2],$pos1+8,strpos($matches[2],';',$pos1+8)-$pos1-8);
			}
			elseif($pos1=strpos($matches[2],'Linux'))
			{
				$arResult['OS']='Linux';
			}
			elseif($pos1=strpos($matches[2],'Mac'))
			{
				$arResult['OS']='Mac OS X';
			}

		}
		elseif($pos=strpos($matches[0],'Netscape'))
		{
			$arResult['BROWSER']='Netscape Navigator';
			if($pos1=strpos($matches[2],'Windows '))
			{
				$arResult['OS']='Windows '.substr($matches[2],$pos1+8,strpos($matches[2],';',$pos1+8)-$pos1-8);
			}
			elseif($pos1=strpos($matches[2],'SunOS'))
			{
				$arResult['OS']='SunOS';
			}
			elseif($pos1=strpos($matches[2],'Mac'))
			{
				$arResult['OS']='Mac OS X';
			}
			$arResult['VERSION']=substr($matches[0],$pos+9);
		}
		elseif($pos=strpos($matches[0],'Safari'))
		{
			$arResult['BROWSER']='Safari';
			if($pos1=strpos($matches[2],'Windows '))
			{
				$arResult['OS']='Windows '.substr($matches[2],$pos1+8,strpos($matches[2],';',$pos1+8)-$pos1-8);
			}
			elseif($pos1=strpos($matches[2],'iPhone'))
			{
				$arResult['OS']='iPhone OS';
			}
			elseif($pos1=strpos($matches[2],'SymbianOS'))
			{
				$arResult['OS']='Symbian OS';
			}
			elseif($pos1=strpos($matches[2],'Mac'))
			{
				$arResult['OS']='Mac OS X';
			}
			$arResult['VERSION']=substr($matches[0],$pos+7);
			if(preg_match('#Version/([0-9\.]+)#',$matches[5],$arVMatch))
			{
				$arResult['VERSION']=$arVMatch[1];
			}
		}
		elseif($pos=strpos($matches[0],'SeaMonkey'))
		{
			$arResult['BROWSER']='SeaMonkey';
			if($pos1=strpos($matches[2],'Windows '))
			{
				$arResult['OS']='Windows '.substr($matches[2],$pos1+8,strpos($matches[2],';',$pos1+8)-$pos1-8);
			}
			elseif($pos1=strpos($matches[2],'Win98'))
			{
				$arResult['OS']='Windows 98';
			}
			elseif($pos1=strpos($matches[2],'Linux'))
			{
				$arResult['OS']='Linux';
			}
			elseif($pos1=strpos($matches[2],'Mac'))
			{
				$arResult['OS']='Mac OS X';
			}
			$arResult['VERSION']=substr($matches[0],$pos+10);
		}
		elseif($pos=strpos($matches[0],'Konqueror'))
		{
			$arResult['BROWSER']='Konqueror';
			if($pos1=strpos($matches[2],'Windows '))
			{
				$arResult['OS']='Windows '.substr($matches[2],$pos1+8,strpos($matches[2],';',$pos1+8)-$pos1-8);
			}
			elseif($pos1=strpos($matches[2],'Win98'))
			{
				$arResult['OS']='Windows 98';
			}
			elseif($pos1=strpos($matches[2],'Linux'))
			{
				$arResult['OS']='Linux';
			}
			elseif($pos1=strpos($matches[2],'Mac'))
			{
				$arResult['OS']='Mac OS X';
			}
			$arResult['VERSION']=substr($matches[0],$pos+10,3);
		}
		elseif($pos=strpos($matches[0],'Gecko'))
		{
			$arResult['OS']='Linux';
			$arResult['BROWSER']='Mozilla';
			$pos2=strpos($matches[0],' ',$pos);
			if(!$pos2)
			{
				$arResult['VERSION']=substr($matches[0],$pos+6);
			}
			else
			{
				$arResult['VERSION']=substr($matches[0],$pos+6,$pos2-$pos-6);
			}
		}
		else
		{
			if(preg_match('#Twiceler-([0-9\.]+)#',$matches[0],$arVer))
			{
				$arResult['BROWSER']='Twiceler';
				$arResult['VERSION']=$arVer[1];
				$arResult['OS']='ROBOT';
			}
			elseif(preg_match('#Yahoo! Slurp\/([0-9\.]+)#',$matches[0],$arVer))
			{
				$arResult['BROWSER']='Yahoo! Slurp';
				$arResult['VERSION']=$arVer[1];
				$arResult['OS']='ROBOT';
			}
			elseif(preg_match('#Googlebot\/([0-9\.]+)#',$matches[0],$arVer))
			{
				$arResult['BROWSER']='Googlebot';
				$arResult['VERSION']=$arVer[1];
				$arResult['OS']='ROBOT';
			}
			else
			{
				$arResult['BROWSER']='Mozilla compatable';
				$arResult['VERSION']=$matches[1];
				if($pos1=strpos($matches[2],'Windows '))
				{
					$arResult['OS']='Windows '.substr($matches[2],$pos1+8,strpos($matches[2],';',$pos1+8)-$pos1-8);
				}
				elseif($pos1=strpos($matches[2],'Win98'))
				{
					$arResult['OS']='Windows 98';
				}
				elseif($pos1=strpos($matches[2],'Linux'))
				{
					$arResult['OS']='Linux';
				}
				elseif($pos1=strpos($matches[2],'Mac'))
				{
					$arResult['OS']='Mac OS X';
				}
			}
		}
	}
	elseif(preg_match('#Opera\/([0-9\.]+) (\(([^;\)]+(; |)?)+\))(.*)#i',$useragent,$matches))
	{
		//Какаято из опер
		$arResult['BROWSER']='Opera';
		$arResult['VERSION']=$matches[1];
		if(preg_match('#Version/([0-9\.]+)#',$matches[5],$arVMatch))
		{
			$arResult['VERSION']=$arVMatch[1];
		}
		if($pos1=strpos($matches[2],'Opera Mini'))
		{
			$arResult['BROWSER']='Opera Mini';
			preg_match('#Opera mini\/([a-z0-9\.]+)\/#',$matches[2],$arVer);
			$arResult['VERSION']=$arVer[1];
			$arResult['OS']='J2ME/MIDP';
		}
		elseif($pos1=strpos($matches[2],'Windows '))
		{
			$arResult['OS']='Windows '.substr($matches[2],$pos1+8,strpos($matches[2],';',$pos1+8)-$pos1-8);
		}
		elseif($pos1=strpos($matches[2],'Win98'))
		{
			$arResult['OS']='Windows 98';
		}
		elseif($pos1=strpos($matches[2],'Linux'))
		{
			$arResult['OS']='Linux';
		}
		elseif($pos1=strpos($matches[2],'Mac'))
		{
			$arResult['OS']='Mac OS X';
		}
	}
	elseif(preg_match('#^Yandex(something)?\/([0-9\.]+)#i',$useragent,$matches))
	{
		//яндекс робот
		$arResult['BROWSER']='Yandex';
		$arResult['VERSION']=$matches[2];
		$arResult['OS']='ROBOT';
	}
	elseif(preg_match('#^Mail\.Ru\/([0-9\.]+)#i',$useragent,$matches))
	{
		//мэйл ру робот
		$arResult['BROWSER']='Mail.ru';
		$arResult['VERSION']=$matches[2];
		$arResult['OS']='ROBOT';
	}
	elseif(preg_match('#^StackRambler\/([0-9\.]+)#i',$useragent,$matches))
	{
		//рамблер робот
		$arResult['BROWSER']='Rambler';
		$arResult['VERSION']=$matches[2];
		$arResult['OS']='ROBOT';
	}
	elseif(preg_match('#^msnbot\/([0-9\.]+)#i',$useragent,$matches))
	{
		//мелкософт
		$arResult['BROWSER']='MSN bot';
		$arResult['VERSION']=$matches[2];
		$arResult['OS']='ROBOT';
	}
	elseif(preg_match('#^FlickySearchBot\/([0-9\.]+)#i',$useragent,$matches))
	{
		//не знаю
		$arResult['BROWSER']='FlickySearchBot';
		$arResult['VERSION']=$matches[2];
		$arResult['OS']='ROBOT';
	}
	elseif(preg_match('#^Yanga WorldSearch Bot v([0-9\.]+)#i',$useragent,$matches))
	{
		//Какаято из опер
		$arResult['BROWSER']='Yanga WorldSearch Bot';
		$arResult['VERSION']=$matches[2];
		$arResult['OS']='ROBOT';
	}
	elseif(preg_match('#^Yeti\/([0-9\.]+)#i',$useragent,$matches))
	{
		//Какаято из опер
		$arResult['BROWSER']='Yeti';
		$arResult['VERSION']=$matches[2];
		$arResult['OS']='ROBOT';
	}
	else
	{
		$arResult['BROWSER']='Other';
		$arResult['VERSION']='1.0';
		//Определяем операционку
		if($pos1=strpos($matches[0],'Windows'))
		{
			$pos2=strpos($matches[0],';',$pos1);
			if(!$pos2)
			{
				$pos2=strpos($matches[0],')',$pos1);
			}
			$arResult['OS']='Windows '.substr($matches[0],$pos1+8,$pos2-$pos1-8);
		}
		elseif($pos1=strpos($matches[0],'Mac'))
		{
			$arResult['OS']='Mac OS';
		}
		elseif($pos1=strpos($matches[0],'Linux'))
		{
			$arResult['OS']='Linux';
		}
		elseif($pos1=strpos($matches[0],'Nitro'))
		{
			$arResult['OS']='Nintendo DS';
		}
		elseif($pos1=strpos($matches[0],'Symbian'))
		{
			$arResult['OS']='Symbian OS';
		}
	}
	return $arResult;
}
