<?php
/**
 * @file function.ShowEditor.php
 * Плагин для смарти чтобы отобразить текстовый редактор
 * Файл проекта kolos-cms.
 *
 * Создан 11.11.2009
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.5.5
 * @todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

function smarty_function_ShowEditor($params,&$smarty)
{
	global $KS_MODULES;
	if(!isset($params['theme'])) $params['theme']='advanced';
	if(!isset($params['path'])) $params['path']='/';
	$sTemplate=$KS_MODULES->GetTemplate($params['path']);
	$sContentCss='';
	if(file_exists(TEMPLATES_DIR.'/'.$sTemplate.'/css/content.css'))
	{
		$sContentCss='content_css:"'.str_replace(ROOT_DIR,'',TEMPLATES_DIR.'/'.$sTemplate.'/css/content.css').'",';
	}
	$sResult='';
	if(array_key_exists('field',$params))
	{
		$sResult='<textarea name="'.$params['field'].'" class="form_textarea">'.(isset($params['value'])?htmlspecialchars($params['value']):'').'</textarea>';
		if(!isset($params['object']))
			$params['object']='textarea[name='.$params['field'].']';
	}
	if(!isset($params['object'])) $params['object']='textarea';
	$KS_MODULES->UseJavaScript('/main/admin_editor.js',20);
	$sResult.='<script type="text/javascript">$(document).bind("InitTiny",function(event){';
	if($params['theme']=='advanced')
		$sResult.='ShowTinyBig("'.$params['object'].'");';
	else
		$sResult.='ShowTinySmall("'.$params['object'].'");';
	$sResult.='$(document).trigger("AfterInitTiny");});$(document).ready(function(){$(document).trigger("InitTiny")});</script>';
	return $sResult;
}
