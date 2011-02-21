<?php
/**
 * \file function.Parse.php
 * Виджет обработки текста
 * Файл проекта kolos-cms.
 * 
 * Создан 08.06.2009
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 1.0
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/interfaces/libs/class.CSmile.php';

function smarty_function_TextParser($params,&$smarty)
{
	global $MODULE_interfaces_db_config,$obBBParser,$arSmilies;
	include MODULES_DIR.'/interfaces/config.php';
	$obSmile=new CSmile($MODULE_interfaces_db_config['smilies']);
	if($params['text']=='') return $params['text'];
	if($params['parse']=='') return $params['text'];
	if(!is_object($obBBParser)) $obBBParser=new CBBParser();
	switch($params['parse'])
	{
		case 'no':return $params['text'];break;
		case 'bb':return $obBBParser->Parse($params['text']);break;
		case 'smile':return $obSmile->Parse($params['text']);break;
		case 'bb+smile':
			if(!is_array($arSmilies)) $arSmilies=$obSmile->GetList();
			for($i=0;$i<count($arSmilies);$i++)
			{
				$obBBParser->obBBCode->addSmiley($arSmilies[$i]['smile'],'/uploads/'.$arSmilies[$i]['img']);
			}
			return $obBBParser->Parse($params['text']);
		break;
		case 'nohtml':
			return strip_tags($params['text']);
		break;
		case 'full':
			$text=strip_tags($params['text']);
			if(!is_array($arSmilies)) $arSmilies=$obSmile->GetList();
			for($i=0;$i<count($arSmilies);$i++)
			{
				$obBBParser->obBBCode->addSmiley($arSmilies[$i]['smile'],'/uploads/'.$arSmilies[$i]['img']);
			}
			return $obBBParser->Parse($text);
		break;
	}
}

function widget_params_TextParser($arParams)
{
	$arFields = array
	(
		"parse" => array
		(
			"title" => "Обработать как",
			"type" => "select",
			"value" => array(
				'no'=>'без обработки',
				"bb" => "ВВ код", 
				"smile" => "Смайлики", 
				'bb+smile'=>'ВВ код+смайлики',
				'nohtml'=>'Удалить HTML',
				'full'=>'Удалить HTML+BB коды+смайлики')
		),
		'text'=>array(
			'title'=>'Текст для обработки',
			'type'=>'text',
			'value'=>'',
		),
	);
	return array
	(
		"fields" => $arFields,
	);
}
?>
