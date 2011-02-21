<?php
/**
 * \file function.Captcha.php
 * Файл виджета вставки каптчи в страницу
 * Файл проекта kolos-cms.
 * 
 * Создан 26.02.2010
 *
 * \author blade39@kolosstudio.ru
 * \version 1.0
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

/**
 * Виджет генерирует html код для вывода динамической каптчи
 */
function smarty_function_Captcha($params,&$smarty)
{
	$surl=CCaptcha::GetCaptchaUrl();
	$sResult='<img src="'.CCaptcha::GetCaptchaUrl().'" alt="Если вы не видите изображение обновите страницу или нажмите на ссылку справа" border="0"/>';
	if($params['reload']!='N')
	{
		if($params['img']=='') $params['img']='<img src="/uploads/templates/admin/images/icons2/reload.gif" alt="Обновить" border="0"/>';
		$sResult.='<a href="#" onclick="this.previousSibling.src=\''.$surl.'&r=\'+Math.floor(Math.random() * (99999 - 10000 + 1)) + 10000;return false;">';
		$sResult.=$params['img'].'</a>';
	}
	return $sResult;
}

function widget_params_Captcha()
{
	$arFields = array
	(
		'reload' => array
		(
			'title' => "Скрыть кнопку &quot;Обновить&quot;",
			'type' => "checklist",
			'value' => array(
				'Y'=>'Да'
			),
		),
		'img' => array
		(
			'title' => "HTML выводимый на кнопке &quot;Обновить&quot;. По умолчанию картинка.",
			'type' => "text",
			'value' => ''
		)
	);
	
	return array
	(
		'fields' => $arFields
	);
}
?>
