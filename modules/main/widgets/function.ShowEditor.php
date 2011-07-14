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
	$sResult.='<script type="text/javascript">'.
	'$(document).bind("InitTiny",function(event)'.
	'{'.
		'if($(\''.$params['object'].'\').attr("isTiny")!=1){' .
			'$(\''.$params['object'].'\').each(function(){$(this).after(\'<br/><a href="#" class="showEditor">'.$KS_MODULES->GetText('show_editor').'</a><a href="#" class="hideEditor">'.$KS_MODULES->GetText('hide_editor').'</a>\');});'.
			'$(\'.showEditor\').unbind("click").click(function(e){$(this).hide().parent().children(\'.hideEditor\').show().parent().children(\'textarea:tinymce\').tinymce().show();e.preventDefault()});'.
			'$(\'.hideEditor\').unbind("click").click(function(e){$(this).hide().parent().children(\'.showEditor\').show().parent().children(\'textarea:tinymce\').tinymce().hide();e.preventDefault()});'.
			'$(\'.showEditor\').hide();'.
		'$(\''.$params['object'].'\').attr("isTiny","1").tinymce({'."\n".
			'script_url : "/js/tiny_mce/tiny_mce.js",';
	if($params['theme']=='advanced')
	{
		$sResult.='theme : "advanced",'.
			'plugins : "safari,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,media,searchreplace,print,contextmenu,paste,directionality,fullscreen",'.
			'theme_advanced_buttons1_add_before : "save,separator",'.
			'theme_advanced_buttons1_add : "fontselect,fontsizeselect,typograf",'.
			'theme_advanced_buttons2_add : "separator,insertdate,inserttime,preview,zoom,separator,forecolor,backcolor",'.
			'theme_advanced_buttons2_add_before: "cut,copy,paste,pastetext,pasteword,separator,search,replace,separator",'.
			'theme_advanced_buttons3_add_before : "tablecontrols,separator",'.
			'theme_advanced_buttons3_add : "iespell,media,advhr,separator,print,separator,ltr,rtl,separator,fullscreen,ksSmile",'.
			'theme_advanced_toolbar_location : "top",'.
			'theme_advanced_toolbar_align : "left",'.
			'theme_advanced_statusbar_location : "bottom",'.
			'plugi2n_insertdate_dateFormat : "%Y-%m-%d",'.
		    'plugi2n_insertdate_timeFormat : "%H:%M:%S",'.
			'paste_use_dialog : false,'.
			'theme_advanced_resizing : true,'.
			'theme_advanced_resize_horizontal : false,';
	}
	$sResult.=$sContentCss.'paste_auto_cleanup_on_paste : true,'.
			'paste_convert_headers_to_strong : false,'.
			'paste_strip_class_attributes : "all",'.
			'paste_remove_spans : false,'.
			'paste_remove_styles : false,'.
			'relative_urls:false,'.
			'language : "ru",'.
			/*'valid_elements : "@[id|class|style|title|dir<ltr?rtl|lang|xml::lang|onclick|ondblclick|'.
			'onmousedown|onmouseup|onmouseover|onmousemove|onmouseout|onkeypress|'.
			'onkeydown|onkeyup],a[rel|rev|charset|hreflang|tabindex|accesskey|type|'.
			"name|href|target|title|class|onfocus|onblur],strong/b,em/i,strike,u,".
			"#p,-ol[type|compact],-ul[type|compact],-li,br,img[longdesc|usemap|".
			"src|border|alt=|title|hspace|vspace|width|height|align],-sub,-sup,".
			"-blockquote,-table[border=0|cellspacing|cellpadding|width|frame|rules|".
			"height|align|summary|bgcolor|background|bordercolor],-tr[rowspan|width|".
			"height|align|valign|bgcolor|background|bordercolor],tbody,thead,tfoot,".
			"#td[colspan|rowspan|width|height|align|valign|bgcolor|background|bordercolor".
			"|scope],#th[colspan|rowspan|width|height|align|valign|scope],caption,-div,".
			"-span,-code,-pre,address,-h1,-h2,-h3,-h4,-h5,-h6,hr[size|noshade],-font[face".
			"|size|color],dd,dl,dt,cite,abbr,acronym,del[datetime|cite],ins[datetime|cite],".
			"object[classid|width|height|codebase|*],param[name|value|_value],embed[type|width".
			"|height|src|*],script[src|type],map[name],area[shape|coords|href|alt|target],bdo,".
			"button,col[align|char|charoff|span|valign|width],colgroup[align|char|charoff|span|".
			"valign|width],dfn,fieldset,form[action|accept|accept-charset|enctype|method],".
			"input[accept|alt|checked|disabled|maxlength|name|readonly|size|src|type|value],".
			"kbd,label[for],legend,noscript,optgroup[label|disabled],option[disabled|label|selected|value],".
			"q[cite],samp,select[disabled|multiple|name|size],small,".
			'textarea[cols|rows|disabled|name|readonly],tt,var,big",'.*/
			'file_browser_callback : "myFileBrowser"'.
		'});}});'.
	'$(document).ready(function(){$(document).trigger("InitTiny")});'.
	'</script>';
	return $sResult;
}
