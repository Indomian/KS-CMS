<script type="text/javascript" src="/js/main/dragDrop.js"></script>
{literal}
<script type="text/javascript">
function showData(oData)
{
    var elId=document.oReq.elId;
    if (oData!=null)
    {
        var parent=document.getElementById(elId);
        if (parent.firstChild.tagName=='A')
		{
        	parent.firstChild.firstChild.src="{/literal}{#images_path#}{literal}/icons_menu/minus.gif";
   		}
   		else
   		{
			parent.childNodes[1].firstChild.src="{/literal}{#images_path#}{literal}/icons_menu/minus.gif";
		}
		var ul=document.createElement("UL");
		for(i = 1; i < oData.length; i++)
        {
        	var li=document.createElement("li");
            li.id="item"+oData[i].id;
            if (li.attachEvent)
            {
            	li.attachEvent('onmouseover', ShowButtons);
            }
            else
            {
            	li.onmouseover = ShowButtons;
            }
			
			if(oData[i].active==1)
			{
				activeHTML="{/literal}<a href=\"{get_url ACTION=hide}&CSC_elmid="+oData[i].id+"\" title=\"{#hide#}\">" +
    			"<img src=\"{#images_path#}/icons2/active1.gif\" width=\"16\" height=\"16\" alt=\"{#hide#}\"/></a>&nbsp;{literal}";
			}
			else
			{
				activeHTML="{/literal}<a href=\"{get_url ACTION=show}&CSC_elmid="+oData[i].id+"\" title=\"{#show#}\">" +
	   			"<img src=\"{#images_path#}/icons2/active0.gif\" width=\"16\" height=\"16\" alt=\"{#show#}\"/></a>&nbsp;{literal}";
			}
            li._ks_typeid="{/literal}{$dataList.SECTION.id}{literal}";
            li.className="treeItem";
            li.innerHTML = "{/literal}<span style=\"display: none; float: right; overflow: hidden;\" width=\"100%\" style=\"padding-left:20px;\" id=\"btn"+oData[i].id+"\">" + activeHTML +
    			"<a href=\"{get_url ACTION=edit}&CSC_elmid="+oData[i].id+"\" title=\"{#edit#}\"><img src=\"{#images_path#}/icons2/edit.gif\" width=\"16\" height=\"16\" alt=\"{#edit#}\"/></a>&nbsp;" +
    			"<a href=\"{get_url ACTION=delete}&CSC_elmid="+oData[i].id+"\" onclick=\"return confirm('{#delete_confirm#}');\"  title=\"{#delete#}\">" +
    			"<img src=\"{#images_path#}/icons2/delete.gif\" width=\"16\" height=\"16\" alt=\"{#delete#}\"/></a>" +
    			"</span>" +
    			"<span>" +
	        	"<a href=\"#\" onclick=\"showSubItems('item"+oData[i].id+"'); return false;\">" +
    		   	"<img src=\"{#images_path#}/icons_menu/plus.gif\" alt=\"icon\" height=\"13\" width=\"13\" id=\"plus"+oData[i].id+"\"/>" +
    		   	"</a>&nbsp;" +
            	"<img src=\"{#images_path#}/icons_tree/folder.gif\" alt=\"icon\" height=\"16\" width=\"16\" />&nbsp;" +
            	"<a href=\"{get_url ACTION=edit}&CSC_elmid="+oData[i].id+"\">"+oData[i].anchor+"</a>" +
            	"</span>";
    		{literal}
    		li=ul.appendChild(li);
    		new DragObject(li);
		 	new DropTarget(li);
		 	obj=new DropTarget(li.lastChild.firstChild.firstChild);
			obj.onEnter=function(){
				this.obElement.className='overPlus';
				var id=parseInt(this.obElement.id.substring(4,this.obElement.id.length));
				showSubItems('item'+id);
			};
			obj.canAccept=function(dragObject){
				return true;
			};
			obj.accept=function(dragObject){
				return false;
			};
		}
        var li_add = document.createElement("li");
        li_add.id = "parentadd"+oData[0].parent_id;
	  	li_add.innerHTML =
        	"<p><span class=\"add_01\">" +
			"<img height=\"16\" width=\"16\" src=\"{/literal}{#images_path#}{literal}/icons2/create.gif\" alt=\"icon\" />" +
			"<a href=\"{/literal}{get_url ACTION=new}&CSC_parid="+oData[0].parent_id+"\">{#create_menu#}{literal}</a>" +
			"</span></p>";
	  	li_add=ul.appendChild(li_add);
	  	new DropTarget(li_add);
	  	parent.appendChild(ul);
	  	HideLoading(document.loading);
  	}
}

/* Функция раскрывания/закрывания вложенных элементов дерева */
function showSubItems(elId)
{
	/* Получаем элемент дерева li */
	var parent = document.getElementById(elId);

	if(parent.lastChild.tagName=='UL')
	{
		/* Если внутри элемента дерева уже создан список внутренних элементов */
		if (parent.lastChild.style.display=='none')
		{
			/* Показываем список */
			parent.lastChild.style.display='block';

			/* Меняем плюс на минус */
			parent.childNodes[1].childNodes[0].childNodes[0].src="{/literal}{#images_path#}{literal}/icons_menu/minus.gif";
		}
		else
		{
			/* Скрываем список */
			parent.lastChild.style.display='none';

			/* Меняем минус на плюс */
			parent.childNodes[1].childNodes[0].childNodes[0].src="{/literal}{#images_path#}{literal}/icons_menu/plus.gif";
		}
		return false;
	}

	/* При создании списка вложенных элементов мы раскрываем список, поэтому меняем значок раскрытости элемента с плюса на минус */
	parent.childNodes[1].childNodes[0].childNodes[0].src="{/literal}{#images_path#}{literal}/icons_menu/minus.gif";

	/* Создаём новый объект для работы с асинхронными запросами */
	if(!document.oReq)
		document.oReq=new Object();

	var oReq = document.oReq;
	oReq.elId = elId;
	elId = elId.substr(4, elId.length-4);

	/* Выполняем ajax-запрос и при его выполнении вызываем функцию showData для создания списка элементов, вложенных в текущих li */
	$.get("/admin.php?module=navigation&page=ajax&action=getElements&CSC_id={/literal}{$dataList.SECTION.id}{literal}&CSC_parid="+elId,null,showData,"json");
	document.loading=ShowLoading();
	document.oReq=oReq;
}

function ShowButtons(event)
{
	if (!event)event=window.event;
	if (event.srcElement)
	{
		if(document.toHide)
		{
			document.toHide.style.display='none';
		}
		if(event.srcElement.tagName=='LI')
		{
			var id=event.srcElement.id.substr(4,event.srcElement.id.length-4);
			if (document.getElementById('btn'+id))
			{
				document.getElementById('btn'+id).style.display="inline";
				document.toHide=document.getElementById('btn'+id);
			}
		}
		else
		{
			var parent=event.srcElement;
			while(parent=parent.parentElement)
			{
				if(parent.tagName=='LI') break;
			}
			var id=parent.id.substr(4,parent.id.length-4);
			if (document.getElementById('btn'+id))
			{
				document.getElementById('btn'+id).style.display="inline";
				document.toHide=document.getElementById('btn'+id);
			}
		}
	}
	else
	{
		if(document.toHide)
		{
			document.toHide.style.display='none';
		}
		if(event.target.tagName=='LI')
		{
			var id = event.target.id.substr(4,event.target.id.length-4);
			if (document.getElementById('btn'+id))
			{
				document.getElementById('btn'+id).style.display="inline";
				document.toHide=document.getElementById('btn'+id);
			}
		}
		else
		{
			var parent=event.target;
			while(parent=parent.parentNode)
			{
				if(parent.tagName=='LI') break;
			}
			var id=parent.id.substr(4,parent.id.length-4);
			if (document.getElementById('btn'+id))
			{
				document.getElementById('btn'+id).style.display="inline";
				document.toHide=document.getElementById('btn'+id);
			}
		}
	}
}

function HideButtons(event)
{
	if (!event)event=window.event;
	if (event.srcElement)
	{
		if(event.srcElement.tagName=='LI')
		{
			var id=event.srcElement.id.substr(4,event.srcElement.id.length-4);
			document.getElementById('btn'+id).style.display="none";
		}
	}
	else
	{
		if(event.target.tagName=='LI')
		{
			var id=event.target.id.substr(4,event.target.id.length-4);
			document.getElementById('btn'+id).style.display="none";
		}
	}
}
</script>
{/literal}
<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
	<li><a href="/admin.php?module=navigation"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
    <li><a href="{get_url}"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title_manage#} "{$dataList.SECTION.name}"</span></a></li>
</ul>

<h1>{#title_manage#} "{$dataList.SECTION.name}"</h1>

<div class="manage">
	<table class="layout">
		<tr>
			<td width="100%">
				<span>{#header_menu_hint#}</span>
			</td>
		</tr>
	</table>
</div>
{strip}
<div class="tree">
	<ul>
	{if $dataList.ITEMS!=''}
	{foreach from=$dataList.ITEMS item=oItem key=oKey name=myList}
		{if $oItem.ITEMS!=''}{assign var="image_name" value='minus'}{else}{assign var="image_name" value='plus'}{/if}
		<li id="item{$oItem.id}" onmouseover="ShowButtons(event)" class="treeItem" _ks_typeid="{$dataList.SECTION.id}">
			<span id="btn{$oItem.id}" style="display: none; float: right; overflow: hidden;">
            	{if $oItem.active}
                <a href="{get_url ACTION=hide CSC_elmid=$oItem.id}" title="{#hide#}">
                <img src="{#images_path#}/icons2/active1.gif" width="16" height="16" alt="{#hide#}" /></a>&nbsp;
                {else}
                <a href="{get_url ACTION=show CSC_elmid=$oItem.id}" title="{#show#}">
                <img src="{#images_path#}/icons2/active0.gif" width="16" height="16" alt="{#show#}" /></a>&nbsp;
                {/if}
   				<a href="{get_url ACTION=edit CSC_elmid=$oItem.id}" title="{#edit#}">
				<img src="{#images_path#}/icons2/edit.gif" width="16" height="16" alt="{#edit#}" /></a>&nbsp;
				<a href="{get_url ACTION=delete CSC_elmid=$oItem.id}" onclick="return confirm('{#delete_confirm#}');" title="{#delete#}">
				<img src="{#images_path#}/icons2/delete.gif" width="16" height="16" alt="{#delete#}" /></a>
			</span>
	   		<span>
	   		<a href="#" onclick="showSubItems('item{$oItem.id}'); return false;">
	   			<img src="{#images_path#}/icons_menu/{$image_name}.gif" alt="icon" height="13" width="13" id="{$image_name}{$oItem.id}"/>
	   		</a>&nbsp;
	   		<script type="text/javascript">
	   		<!--

	   		-->
	   		</script>
	   		<img src="{#images_path#}/icons_tree/folder.gif" alt="icon" height="16" width="16" />&nbsp;
   			<a href="{get_url ACTION=edit CSC_elmid=$oItem.id}">{$oItem.anchor}</a>
   			</span>
   			{if $oItem.ITEMS!=''}
   				{include file="admin/navigation_subelements.tpl" dataList=$oItem}
   			{/if}
   		</li>
	{/foreach}
	{/if}
		<li><p><span class="add_01"><img height="16" width="16" src="{#images_path#}/icons2/create.gif" alt="{#create_menu#}" /><a href="{get_url ACTION=new CSC_parid=$oItem.parent_id}">{#create_menu#}</a></span></p></li>
	</ul>
</div>
{/strip}
<script type="text/javascript">
<!--
var obj;
{foreach from=$dataList.ITEMS item=oItem key=oKey name=myList}
	obj=document.getElementById('item{$oItem.id}');
	if(typeof(obj)=='object')
	{ldelim}
		 new DragObject(obj);
		 new DropTarget(obj);
		 obj._ks_typeid="{$dataList.SECTION.id}";
	{rdelim}
	obj=new DropTarget(obj.lastChild.firstChild.firstChild);
	obj.onEnter=function(){ldelim}
		this.obElement.className='overPlus';
		var id=parseInt(this.obElement.id.substring(4,this.obElement.id.length));
		showSubItems('item'+id);
	{rdelim}
	obj.canAccept=function(dragObject){ldelim}
		return true;
	{rdelim}
	obj.accept=function(dragObject){ldelim}
		return false;
	{rdelim}
{/foreach}

-->
</script>
{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/folder.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
<dt>{#title_manage#}</dt>
<dd>{#hint_list_menu#}</dd>
</dl>
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}