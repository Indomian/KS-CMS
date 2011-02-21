CCatsubcat.prototype=new Object();
function CCatsubcat(){};

CCatsubcat.prototype.toggleTextIdentEdit=function()
{
	var value=$('input[name=CSC_text_ident]').val();
	$('span#textIdentText').hide().parent().empty().append('<input type="text" name="CSC_text_ident" value="'+value+'" class="form_input" style="width:98%;"/>');
	return false;
};

CCatsubcat.prototype.togglePanel=function(elm)
{
	$('input[name=CM_anchor]').val($('input[name=CSC_title]').val());
	if($(elm).val()==1)
	{
		this.selectType($('select[name=CM_type_id]').attr('value'), 0, document.getElementById('item0'));
		$('#panel').show();
	}
	else
	{
		$('#panel').hide();
	}
}

/* Функция выполняет ajax-запрос на получение списка вложенных элементов пункта меню */
CCatsubcat.prototype.selectType=function(type_id, parent_id, div)
{
	var current_tag = document.getElementById("item"+parent_id);
	if ((current_tag.lastChild && current_tag.lastChild.tagName!='UL') || parent_id==0)
	{
		document.myloading=ShowLoading();
		document.div=div;
		document.type_id=type_id;
		document.parent_id=parent_id;
		$.get("/admin.php?module=navigation&page=ajax&action=getElements&CSC_id="+type_id+"&CSC_parid="+parent_id,null,function(oData)
		{
		   	if (oData)
		    {
		        var parent = document.div;

		        /* Очищаем тэг для вывода нового меню */
			    if(document.parent_id==0)
			       	parent.innerHTML='';

		        var ul=document.createElement("UL");
		        for(i=1; i<oData.length;i++)
            	{
            		var li=document.createElement("li");
            		li.id="item"+oData[i].id;
            		$(li).append($('<input type="radio" name="CM_parent_id"/>').attr('value',oData[i].id));
            		var obA=$('<a href="#">').attr('rel',oData[i].id);
            		$(li).append(obA);
            		obA.append($('<img src="/uploads/templates/admin/images/icons_menu/plus.gif" alt="plus" width="13" height="13"/>'));
            		obA.click(function(event){
            			obCatsubcat.selectType(document.type_id,this.rel,document.getElementById('item'+this.rel));
            			event.stopImmediatePropagation();
            		});
            		$(li).append('<img src="/uploads/templates/admin/images/icons2/folder.gif" alt="icon" height="20" width="20"/>&nbsp;')
            		$(li).append(oData[i].anchor);
            		$(ul).append($(li));
            	}
            	parent.appendChild(ul);
				HideLoading(document.myloading);
		  	}
		},"json");
		return false;
	}
}
window.obCatsubcat=new CCatsubcat();