function ShowTinySmall(obSelector)
{
	var obTextareas=$(obSelector);
	if(obTextareas.length>0)
	{
		obTextareas.each(function(){
			if($(this).attr('isTiny')==1) return;
			$(this).attr("isTiny","1").tinymce({
				script_url : "/js/tiny_mce/tiny_mce.js",
				paste_auto_cleanup_on_paste : true,
				paste_convert_headers_to_strong : false,
				paste_strip_class_attributes : "all",
				paste_remove_spans : false,
				paste_remove_styles : false,
				relative_urls:false,
				setup:TinyMCESave,
				language : "ru"
			});
		});
	}
}

function TinyMCESave(ed)
{
	ed.onInit.add(function(ed) {
		var obTextarea=ed.getElement();
		$(obTextarea).blur(function(){
			if($(this).tinymce().isHidden())
			{
				$(this).tinymce().setContent(this.value);
			}
		});
	});
}

function ShowTinyBig(obSelector)
{
	var obTextareas=$(obSelector);
	if(obTextareas.length>0)
	{
		obTextareas.each(function(){
			if($(this).attr('isTiny')==1) return;
			$(this).attr("isTiny","1").tinymce({
				script_url : "/js/tiny_mce/tiny_mce.js",
				theme : "advanced",
				plugins : "safari,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,media,searchreplace,print,contextmenu,paste,directionality,fullscreen",
				theme_advanced_buttons1_add_before : "save,separator",
				theme_advanced_buttons1_add : "fontselect,fontsizeselect,typograf",
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
				setup:TinyMCESave,
				language : "ru"
			}).after('<div class="toggleEditor"><a href="#" class="showEditor">Показать редактор</a><a href="#" class="hideEditor">Скрыть редактор</a></div>');
		});
	}
}
(function(){
	function SetHiderEvents()
	{
		 $('.showEditor').unbind("click").click(function(e){
			 var obTiny=$(this).parent().parent().children('textarea:tinymce');
			 $(this).hide().parent().children('.hideEditor').show();
			 obTiny.tinymce().show();
			 e.preventDefault();
		}).hide();
		$('.hideEditor').unbind("click").click(function(e){
			var obTiny=$(this).parent().parent().children('textarea:tinymce');
			$(this).hide().parent().children('.showEditor').show();
			obTiny.tinymce().hide();
			e.preventDefault()
		});
	}
	$(document).ready(function(){
		$(document).bind("AfterInitTiny",SetHiderEvents);
	});
})()

