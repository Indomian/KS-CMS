/**
 * В этом файле находятся функции яваскрипта работающего с настройкой привязок шаблонов
 *
 */
//Определяем утилиты для всех разделов che.ru
var obTemplates={
	'SetTextValue':function(tdId,id){
		$(tdId).html("<input type=\"text\" name=\""+id+"[url_path]\" value=\"\" style=\"width:95%\" class=\"form_input\"/>");
	},
	/**
	 * Данная функция выполняется если был выбран режим сортировки по регулярному выражению
	 */
	'SetRegValue':function(tdId,id){
		$(tdId).html("<input type=\"text\" name=\""+id+"[url_path]\" value=\"\" style=\"width:95%\" class=\"form_input\"/>");
	},
	'SetUserGroupValue':function(tdId,id){
		$.getJSON("/admin.php?module=main&page=templates&action=getgroups&tdId="+tdId+"&id="+id,function(json){
			if('tdId' in json)
				$('#'+json.tdId).empty().append(json.html);
		});
	}
};

$(document).ready(function(){
	$('.changeType').change(function(){
		if($(this).val()=='userGroup') obTemplates.SetUserGroupValue($(this).parent().next().attr('id'),$(this).attr('name').replace('[type]',''));
		else if($(this).val()=='reg') obTemplates.SetRegValue($(this).parent().next().attr('id'),$(this).attr('name').replace('[type]',''));
		else obTemplates.SetTextValue($(this).parent().next().attr('id'),$(this).attr('name').replace('[type]',''));
		return false;
	});
});
