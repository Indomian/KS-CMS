<?php
if($params['field']['option_1']<256&&$params['field']['option_1']>0)
{
	$sResult="<input type=\"text\" class=\"form_input\" id=\"".$params['prefix']."ext_".$params['field']['title']."\" name=\"".$params['prefix']."ext_".$params['field']['title']."\" value=\"".$params['value']."\" style=\"width:100%\">";
}
else
{
	$sResult='<textarea id="'.$params['prefix']."ext_".$params['field']['title'].'" name="'.$params['prefix']."ext_".$params['field']['title'].'" style="width:100%;height:200px;">';
	$sResult.=$params['value'].'</textarea>';
	if($params['field']['option_2']==1)
	{
		$sResult.='<script language="javascript" type="text/javascript">
		$().ready(function(){	
			$("textarea#'.$params['prefix']."ext_".$params['field']['title'].'").tinymce({
			// Location of TinyMCE script
			script_url : "/js/tiny_mce/tiny_mce.js",
			
			theme : "advanced",
			plugins : "safari,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen",
			theme_advanced_buttons1_add_before : "save,newdocument,separator",
			theme_advanced_buttons1_add : "fontselect,fontsizeselect",
			theme_advanced_buttons2_add : "separator,insertdate,inserttime,preview,zoom,separator,forecolor,backcolor",
			theme_advanced_buttons2_add_before: "cut,copy,paste,pastetext,pasteword,separator,search,replace,separator",
			theme_advanced_buttons3_add_before : "tablecontrols,separator",
			theme_advanced_buttons3_add : "iespell,media,advhr,separator,print,separator,ltr,rtl,separator,fullscreen,ksSmile",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			plugi2n_insertdate_dateFormat : "%Y-%m-%d",
		    plugi2n_insertdate_timeFormat : "%H:%M:%S",
			paste_use_dialog : false,
			theme_advanced_resizing : true,
			theme_advanced_resize_horizontal : false,
			paste_auto_cleanup_on_paste : true,
			paste_convert_headers_to_strong : false,
			paste_strip_class_attributes : "all",
			paste_remove_spans : false,
			paste_remove_styles : false,
			relative_urls:false,
			language : "ru",
			file_browser_callback : "myFileBrowser"
		});});</script>';
	}
}
if($params['field']['option_1']>0&&$params['field']['option_2']==0)
{
$sResult.='<script type="text/javascript">$(document).ready(function(){' .
			'$("#'.$params['prefix'].'ext_'.$params['field']['title'].'").keydown(' .
				'function(event){' .
					'if (event.which<30) return true;'.
					'if(this.value.length+1>'.$params['field']['option_1'].') return false;' .
					'return true;});});'.
		'</script>';
}
?>