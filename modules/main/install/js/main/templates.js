/**
 * В этом файле находятся функции яваскрипта работающего с настройкой привязок шаблонов
 *
 */
//Определяем утилиты для всех разделов che.ru
CTemplates.prototype=new Object();
function CTemplates(){};

CTemplates.prototype.TypeChanged=function(select,id)
{
	var obSelect=select;
	if(typeof(obSelect)!='object') return false;
	if(obSelect.value=='userGroup') this.SetUserGroupValue(obSelect.parentNode.nextSibling.id,id);
	else if(obSelect.value=='reg') this.SetRegValue(obSelect.parentNode.nextSibling.id,id);
	else this.SetTextValue(obSelect.parentNode.nextSibling.id,id);
	return false;
};

CTemplates.prototype.SetTextValue=function(tdId,id)
{
	if((document.getElementById(tdId)))
	{
		document.getElementById(tdId).innerHTML="<input type=\"text\" name=\""+id+"[url_path]\" value=\"\" style=\"width:95%\" class=\"form_input\"/>";
	}
};

/**
 * Данная функция выполняется если был выбран режим сортировки по регулярному выражению
 */
CTemplates.prototype.SetRegValue=function(tdId,id)
{
	if((document.getElementById(tdId)))
	{
		document.getElementById(tdId).innerHTML="<input type=\"text\" name=\""+id+"[url_path]\" value=\"\" style=\"width:95%\" class=\"form_input\"/>";
	}
};


CTemplates.prototype.SetUserGroupValue=function(tdId,id)
{
	$.get("/admin.php?module=main&modpage=templates&ACTION=getgroups&tdId="+tdId+"&id="+id, {},
		function(json)
		{
			if((json.tdId!='')&&(document.getElementById(json.tdId)))
			{
				$('#'+json.tdId).empty().append(json.html);
			}
		},
		'json');

	/*
	//Оригинал запроса на аяксе.
	document.oXmlHttp=zXmlHttp.createRequest();
	var oXmlHttp=document.oXmlHttp;
	oXmlHttp.open("get","/admin.php?module=main&modpage=templates&ACTION=getgroups&tdId="+tdId+"&id="+id,true);
	oXmlHttp.onreadystatechange=function()
	{
		oXml=document.oXmlHttp;
		if (oXml.readyState==4)
		{
			if (oXml.status==200)
			{
				var arResult=oXml.responseText.split('||');
				if((arResult[0]!='')&&(document.getElementById(arResult[0])))
				{
					document.getElementById(arResult[0]).innerHTML=arResult[2];
				}
			}
			else
			{
				alert("Ошибка запроса:"+oXml.status);
			}
		}
	}
	oXmlHttp.send(null);
	return false;*/
};

document.obTemplates=new CTemplates();