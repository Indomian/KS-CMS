<?php
/**
 * \file function.Nofitfies.php
 * Файл виджета вывода уведомлений системы
 * Файл проекта kolos-cms.
 *
 * Создан 27.02.2010
 *
 * \author blade39
 * \version
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

/**
 * Виджет генерирует html код вывода уведомлений
 */
function smarty_function_SysNotice($params,&$smarty)
{
	global $KS_MODULES;
	$arList=$KS_MODULES->ShowNotifies();
	$code=0;
	if($params['type']=='W') $code=NOTIFY_WARNING;
	elseif($params['type']=='N') $code=NOTIFY_MESSAGE;
	if($code>0)
	{
		foreach($arList as $arItem)
		{
			if($arItem['type']==$code)
			{
				$arResult[]=$arItem;
			}
		}
	}
	else
	{
		$arResult=$arList;
	}
	foreach($arResult as $key=>$arItem)
	{
		$arResult[$key]['error']=$KS_MODULES->GetErrorText($arItem['msg']);
		if($arItem['type']==NOTIFY_WARNING)
		{
			$arResult[$key]['prefix']='<div class="warning" style="background:#FFF6C4 url(\'/uploads/templates/admin/images/atention.gif\') left 50% no-repeat; color:#D13B00; border: 1px solid #CC0000; margin: 0 0 6px; padding: 11px 0 11px 59px;">';
			$arResult[$key]['suffix']='</div>';
		}
		elseif($arItem['type']==NOTIFY_MESSAGE)
		{
			$arResult[$key]['prefix']='<div class="message" style="background:#FFF6C4 url(\'/uploads/templates/admin/images/ok.gif\') left 50% no-repeat; color:#D13B00; border: 1px solid #57D300; margin: 0 0 6px; padding: 11px 0 11px 59px;">';
			$arResult[$key]['suffix']='</div>';
		}
	}
	if(!IS_ADMIN)
	{
		try
		{
			$smarty->assign('list',$arResult);
			return $KS_MODULES->RenderTemplate($smarty,'/main/SysNotice',$params['global_template'],$params['tpl']);
		}
		catch(CError $e)
		{
			if($e->getMessage()!='MAIN_TEMPLATE_ERROR')
				throw $e;
		}
	}
	$content='';
	foreach($arResult as $arItem)
		$content.=$arItem['prefix'].$arItem['error'].' <b>'.$arItem['text'].'</b>'.$arItem['suffix']."\n";
	return $content;
}

function widget_params_SysNotice()
{
	$arFields = array
	(
		'type' => array
		(
			'title' => "Режим вывода",
			'type' => "select",
			'value' => array(
				'A'=>'Все',
				'N'=>'Уведомления',
				'W'=>'Предупреждения'
			),
		),
	);

	return array
	(
		'fields' => $arFields
	);
}
?>
