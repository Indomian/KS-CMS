<?php
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/interfaces/libs/class.CSmile.php';

/**
 * Класс используется для обработки текстов в административном интерфейсе
 */
class CAdminTextParser extends CSmile
{
	function __construct($sTable='interfaces_smilies')
	{
		global $smarty;
		parent::__construct($sTable);
		$smarty->register_block("ksParseText", array($this,"_smarty_parse_text"));
	}

	function Parse($text)
	{
		$arSmiles=$this->GetList(array('smile'=>'asc'));
		if(is_array($arSmiles) && count($arSmiles)>0)
		{
			$arCodes=array();
			$arImages=array();
			foreach($arSmiles as $i=>$item)
			{
				$arCodes[]='<span>'.$item['smile'].'</span>';
				$arImages[]='<img src="/uploads/'.$item['img'].'" _mce_ks_smile="'.$item['smile'].'">';
			}
			return str_replace($arCodes,$arImages,$text);
		}
		return $text;
	}

	function _smarty_parse_text($params,$content,&$smarty,&$repeat)
	{
		if($content)
		{
			$content=$this->Parse($content);
			return $content;
		}
	}
}
